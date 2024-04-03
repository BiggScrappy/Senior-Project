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
        <select id="email" name="email[]" multiple> <!-- Add multiple attribute here -->
            <option value="all" id="selectAllOption">Select All</option> <!-- Add Select All option here -->
            <!-- Populate options from database -->
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
        <textarea id="message" name="message" rows="4"></textarea><br><br>
        <label for="sendDateTime">Send Date and Time:</label><br>
        <input type="datetime-local" id="sendDateTime" name="sendDateTime"><br><br>
        <button type="submit" id="editButton" name="action" value="edit">Edit in Outlook</button>
        <button type="submit" id="scheduleButton" name="action" value="schedule">Schedule Email</button>
    </form>

    <script>
        $(document).ready(function() {
            // Event listener for "Select All" option
            $("#selectAllOption").click(function() {
                // Toggle select all options except the "Select All" option itself
                $("#email option").not(this).prop("selected", $(this).prop("selected"));
            });

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
        });

        // Prevent form submission on button click (Edit in Outlook)
        $("#editButton").click(function(event) {
            event.preventDefault();

            // Get selected emails from dropdown, excluding the "Select All" option
            var selectedEmails = $("#email option:selected").not("#selectAllOption").map(function() {
                return $(this).val();
            }).get();

            var toField = selectedEmails ? selectedEmails.join(";") : "";

            // Construct the mailto URL
            var mailto = "mailto:" + toField;

            // Redirect to the mailto URL
            window.location.href = mailto;
        });
    </script>
</body>
</html>
