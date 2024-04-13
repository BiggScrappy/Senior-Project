<?php
// Check if the request is made via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the message and user ID from the POST data
    $message = $_POST["message"];
    $userId = $_POST["user_id"];

    // Validate the message (you might want to add more validation)
    if (!empty($message)) {
        // Include the database connection
        include('database.php');

        // Prepare the SQL statement to insert the template into the database
        $sql = "INSERT INTO email_template (message, created_by, created_at) VALUES (?, ?, NOW())";

        // Prepare and bind parameters
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $message, $userId);

        // Execute the statement
        if ($stmt->execute()) {
            // Template saved successfully
            echo "Template saved successfully!";
        } else {
            // Error occurred while saving the template
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();

        // Close the database connection
        $mysqli->close();
    } else {
        // If message is empty, return an error message
        echo "Error: Message cannot be empty!";
    }
} else {
    // If the request method is not POST, return an error message
    echo "Error: Invalid request method!";
}
?>
