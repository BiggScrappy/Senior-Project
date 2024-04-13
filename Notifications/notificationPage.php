<?php
    ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
    session_start();

    //verify user info
    if(isset($_SESSION["user_id"])){
        $mysqli = require __DIR__ . "/database.php";

        $sql = "SELECT * FROM User_Information
                WHERE user_id = {$_SESSION["user_id"]}";

        $result = $mysqli->query($sql);

        $user = $result->fetch_assoc();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form</title>
    <!-- Include jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="userInformation.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        /* Style for the select dropdown */
        #survey {
            height: auto;
            width: 200px;
        }
    </style>
</head>
<body>
<!--header-->
<div class="header">
        <a href="#default" class="logo">USACE Dam Safety</a>
        <div class="header-right">
          <a class="active" href="index.php">Home</a>    
      <?php if(isset($_SESSION["user_id"])): ?>
          <a href="logout.php">Logout</a>
      <?php elseif(!isset($_SESSION["user_id"])): ?>
          <a href="login.php">Login</a>
      <?php endif; ?>
        </div>
      </div>

<!--Verify User Info-->
<h1>Welcome</h1>
    <div class="userInformation">
    <?php if(isset($user)): ?>
        
        <p> <b> Hello <?= htmlspecialchars($user["username"]) ?>!</b></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> Role: <?= htmlspecialchars($user["role_name"]) ?></p> 
         
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   
    </div>


    <h2>Send Email to Surveyors</h2>
    <?php
    include('database.php');

    // Retrieve survey IDs from the surveys table based on the provided query
    $surveyOptions = array();
    $sql = "SELECT surveys.id, surveys.start_date, surveys.end_date, organizations.name
            FROM surveys
            JOIN organizations ON surveys.organization_id = organizations.id
            WHERE surveys.start_date IS NOT NULL
              AND surveys.end_date IS NOT NULL
              AND surveys.end_date > NOW()";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format the option text with survey ID, start date, end date, and organization name
            $optionText = "Survey ID: " . $row['id'] . " - Start Date: " . $row['start_date'] . " - End Date: " . $row['end_date'] . " - Organization: " . $row['name'];
            $surveyOptions[$row['id']] = $optionText;
        }
    }
    ?>

    <form id="emailForm" action="#" method="post">
        <label for="survey">Select Survey ID:</label><br>
        <select id="survey" name="survey">
            <option value="">No Survey Selected</option>
            <?php
            // Populate survey select options from database
            foreach ($surveyOptions as $surveyId => $optionText) {
                echo "<option value='" . $surveyId . "'>" . $optionText . "</option>";
            }
            ?>
        </select><br><br>
        <label for="email">Select Email:</label><br>
        <select id="email" name="email[]" multiple>
            <option value="all" id="selectAllOption">Select All</option>
            <!-- Additional static option for Select All -->
        </select><br><br>
        <!-- Button for Select Incomplete -->
        <button type="button" id="selectIncompleteBtn">Select Incomplete</button><br><br>
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject"><br><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4"></textarea><br><br>
        <!-- Dropdown for selecting templates -->
        <label for="template">Select Template:</label><br>
        <select id="template" name="template">
            <option value="">Select Template</option>
            <!-- Options will be dynamically filled by JavaScript -->
        </select><br><br>
        <button type="button" id="editBtn">Edit Email</button>
        <button type="button" id="saveTemplateBtn">Save Template</button> <!-- New button to save the template -->
    </form>

    <script>
        $(document).ready(function() {
            // Function to handle select all option
            function handleSelectAll() {
                // Check if the "Select All" option is selected
                var selectAll = $(this).prop("selected");
                // Set all options except the "Select All" option based on the state of "Select All"
                $("#email option:not(#selectAllOption)").prop("selected", selectAll);
            }

            // Event listener for "Select All" option
            $("#selectAllOption").click(handleSelectAll);

            // Enable multiple selection by clicking for the dropdown
            $("#email").mousedown(function(e) {
                e.preventDefault();

                var originalScrollTop = $(this).scrollTop();

                $(this).focus().one("mouseup", function() {
                    var $select = $(this);

                    $select.scrollTop(originalScrollTop);

                    var $option = $(document.elementFromPoint(e.clientX, e.clientY));
                    if ($option.prop("selected")) {
                        $option.prop("selected", false);
                    } else {
                        $option.prop("selected", true);
                    }

                    $select.focus();
                });
            });

            // Function to fetch and populate email options
            function populateEmailOptions(surveyId) {
                if (surveyId === '') {
                    // If "No Survey Selected" is chosen, populate with distinct emails from users
                    $.ajax({
                        url: "getEmailsFromUsers.php", // PHP script to fetch emails from users table
                        method: "POST",
                        dataType: "json",
                        success: function(data) {
                            // Clear previous options
                            $("#email").empty();
                            // Add static option for "Select All"
                            $("#email").append("<option value='all' id='selectAllOption'>Select All</option>");
                            // Add fetched options
                            $.each(data, function(index, email) {
                                $("#email").append("<option value='" + email + "'>" + email + "</option>");
                            });
                            // Reattach event listener for "Select All" option
                            $("#selectAllOption").click(handleSelectAll);
                        }
                    });
                } else {
                    // Otherwise, fetch emails based on selected survey
                    $.ajax({
                        url: "getEmails.php", // PHP script to fetch emails based on survey
                        method: "POST",
                        data: {survey_id: surveyId},
                        dataType: "json",
                        success: function(data) {
                            // Clear previous options
                            $("#email").empty();
                            // Add static option for "Select All"
                            $("#email").append("<option value='all' id='selectAllOption'>Select All</option>");
                            // Add fetched options
                            $.each(data, function(index, email) {
                                $("#email").append("<option value='" + email + "'>" + email + "</option>");
                            });
                            // Reattach event listener for "Select All" option
                            $("#selectAllOption").click(handleSelectAll);
                        }
                    });
                }
            }

            // Update email options based on selected survey
            $("#survey").change(function() {
                var surveyId = $(this).val();
                populateEmailOptions(surveyId);
            });

            // Initial population of email options based on default selected survey
            var initialSurveyId = $("#survey").val();
            populateEmailOptions(initialSurveyId);

            // Open Outlook draft when edit button is clicked
            $("#editBtn").click(function() {
                var message = $("#message").val();
                var subject = $("#subject").val();
                var selectedEmails = $("#email").val().filter(function(email) {
                    return email !== "all"; // Exclude the "all" option
                }).join(";");
                var mailtoLink = "mailto:" + encodeURIComponent(selectedEmails) + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(message);
                window.location.href = mailtoLink;
            });

            // Functionality for Select Incomplete button
            $("#selectIncompleteBtn").click(function() {
                var surveyId = $("#survey").val();

                // Call the PHP method to fetch incomplete emails
                $.ajax({
                    url: "getIncomplete.php", // Adjust the URL accordingly
                    method: "POST",
                    data: {surveyId: surveyId},
                    dataType: "json",
                    success: function(data) {
                        // Clear previous options
                        $("#email").empty();
                        // Add static option for "Select All"
                        $("#email").append("<option value='all' id='selectAllOption'>Select All</option>");
                        // Add fetched options
                        $.each(data, function(index, email) {
                            $("#email").append("<option value='" + email + "'>" + email + "</option>");
                        });
                        // Reattach event listener for "Select All" option
                        $("#selectAllOption").click(handleSelectAll);
                    }
                });
            });

            // Function to handle saving the template
            $("#saveTemplateBtn").click(function() {
                var message = $("#message").val();

                // Send the message content to the PHP script for saving
                $.ajax({
                    url: "saveTemplate.php", // PHP script to save the template
                    method: "POST",
                    data: {message: message},
                    success: function(response) {
                        // Optionally, handle the response from the server
                        alert("Template saved successfully!");
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if any
                        console.error(xhr.responseText);
                    }
                });
            });

            // Function to handle select template
            $("#template").change(function() {
                var selectedTemplate = $(this).val();

                // Fetch the selected template message from the server
                $.ajax({
                    url: "getTemplateMessage.php", // PHP script to fetch the template message
                    method: "POST",
                    data: {template_id: selectedTemplate},
                    success: function(message) {
                        // Fill the message box with the selected template message
                        $("#message").val(message);
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if any
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
