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
        #email {
            height: auto;
            width: 200px;
        }
    </style>
</head>
<body>
    <h2>Send Email to Surveyors</h2>
    <?php
    $mysqli = require __DIR__ . "/database.php";
    ?>

    <form id="emailForm" action="" method="post">
        <label for="email">Select Email:</label><br>
        <select id="email" name="email[]" multiple>
            <option value="all" id="selectAllOption">Select All</option>
            <?php
            $sql = "SELECT email FROM users;";
            $result = $mysqli->query($sql);
            foreach ($result as $row) {
                echo "<option value=\"" . $row['email'] . "\">" . $row['email'] . "</option>";
            }
            ?>
        </select><br><br>
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject"><br><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4"></textarea><br>
        <label for="templateSelect">Select Template:</label><br>
        <select id="templateSelect"></select><br><br>
        <button type="button" id="saveTemplate">Save as Template</button>
        <button type="button" id="loadTemplate">Load Template</button><br><br>
        <button type="button" id="editButton" name="action" value="edit">Edit in Outlook</button>
    </form>

    <script>
        $(document).ready(function() {
            // Function to handle select all option
            function handleSelectAll() {
                $("#email option").prop("selected", $(this).prop("selected"));
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

            // Load saved templates
            var templates = JSON.parse(localStorage.getItem("emailTemplates")) || [];

            // Populate template select options
            templates.forEach(function(template, index) {
                $("#templateSelect").append("<option value='" + index + "'>Template " + (index + 1) + "</option>");
            });

            // Save template button click event
            $("#saveTemplate").click(function() {
                var template = $("#message").val();
                templates.push(template);
                localStorage.setItem("emailTemplates", JSON.stringify(templates));
                $("#templateSelect").append("<option value='" + (templates.length - 1) + "'>Template " + templates.length + "</option>");
                alert("Template saved successfully!");
            });

            // Load template button click event
            $("#loadTemplate").click(function() {
                var selectedIndex = $("#templateSelect").val();
                if (selectedIndex !== null) {
                    var template = templates[selectedIndex];
                    $("#message").val(template);
                    alert("Template loaded successfully!");
                } else {
                    alert("No template selected!");
                }
            });

            // Edit in Outlook button click event
            $("#editButton").click(function(event) {
                event.preventDefault();
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
