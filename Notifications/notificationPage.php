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

    <?php
        // Save template if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["saveTemplate"]) && isset($_POST["template"])) {
                $template = $_POST["template"];
                $sql = "INSERT INTO email_template (message) VALUES ('$template')";
                if ($mysqli->query($sql) === TRUE) {
                    echo "Template saved successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $mysqli->error;
                }
            }
        }

        // Retrieve templates from database
        $templates = array();
        $sql = "SELECT * FROM email_template";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $templates[] = $row['message'];
            }
        }
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
        <textarea id="message" name="message" rows="4"></textarea><br><br>
        <label for="templateSelect">Select Template:</label><br>
        <select id="templateSelect" name="template">
            <?php
            // Populate template select options from database
            foreach ($templates as $template) {
                echo "<option value='" . htmlspecialchars($template) . "'>" . htmlspecialchars($template) . "</option>";
            }
            ?>
        </select><br><br>
        <button type="submit" name="saveTemplate">Save as Template</button>
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
