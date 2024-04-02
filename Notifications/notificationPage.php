<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form</title>
    <!-- Include jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <h2>Send Email to Surveyors</h2>
    <form id="emailForm" action="submitButton.php" method="post">
        <label for="email">Select Email:</label><br>
        <select id="email" name="email">
            <?php
                include('database.php');

            // Retrieve emails from the database and populate the dropdown
            $email = mysqli_query( $con, "SELECT email FROM users");
            $result = $conn->query($sql);
                while($c = mysqli_fetch_array($email)) 
                {
                    echo '<option value="' . $row["email"] . '">' . $row["email"] . '</option>';
            ?>
            <option value="<?php echo $c['ID']?>"><?php echo $c['email'] ?></option>
            <?php } ?>
        </select><br><br>
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject"><br><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4"></textarea><br><br>
        <label for="sendDateTime">Send Date and Time:</label><br>
        <input type="datetime-local" id="sendDateTime" name="sendDateTime"><br><br>
        <input type="submit" value="Schedule Email">
    </form>

    <script>
        $(document).ready(function() {
            // Prevent the form from submitting
            $("#emailForm").submit(function(event) {
                event.preventDefault();

                // Get the form data
                var formData = $(this).serialize();

                // Submit the form data to the PHP script
                $.post("submitButton.php", formData, function(response) {
                    // Handle the response from the server
                    alert(response);
                });
            });
        });
    </script>
</body>
</html>
