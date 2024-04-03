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
                 $mysqli = require __DIR__ . "/database.php";
?>
             <input type="email" id="email" name="email" multiple list="users"/>

            <datalist id="users">
    
     <?php
        $sql = "select email from users;";
        $result = $mysqli->query($sql);
        foreach($result as $i){
            echo "<option value=\"".$i['email']."\">".$i['email']."</option>"; 
        }
    ?>  
</datalist>
            <?php  ?>
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
