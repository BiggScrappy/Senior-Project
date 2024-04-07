<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form</title>
    <!-- Include jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Style for the select dropdown */
        #survey {
            height: auto;
            width: 200px;
        }
    </style>
</head>
<body>
    <h2>Send Email to Surveyors</h2>
    <?php
    include('database.php');

    // Retrieve distinct survey IDs from the database
    $surveyOptions = array();
    $sql = "SELECT DISTINCT survey_id FROM user_surveys";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $surveyOptions[] = $row['survey_id'];
        }
    }
    ?>

    <form id="emailForm" action="#" method="post">
        <label for="survey">Select Survey ID:</label><br>
        <select id="survey" name="survey">
            <?php
            // Populate survey select options from database
            foreach ($surveyOptions as $option) {
                echo "<option value='" . $option . "'>" . $option . "</option>";
            }
            ?>
        </select><br><br>
        <label for="email">Select Email:</label><br>
        <select id="email" name="email[]" multiple>
            <option value="all" id="selectAllOption">Select All</option>
            <!-- Additional static option for Select All -->
        </select><br><br>
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject"><br><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4"></textarea><br><br>
        <button type="button" id="editBtn">Edit Email</button>
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
                $.ajax({
                    url: "getEmails.php",
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
        });
    </script>
</body>
</html>
