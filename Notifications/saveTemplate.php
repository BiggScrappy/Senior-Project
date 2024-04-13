<?php
$mysqli = require __DIR__ . "/database.php";

// Retrieve the message from the POST request
$message = $_POST['message'];

// Prepare and execute the SQL query to insert the message into the database
$sql = "INSERT INTO email_template (message) VALUES (?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $message);
$result = $stmt->execute();

// Check if the query was successful
if ($result) {
    // Return a success response
    echo "Template saved successfully!";
} else {
    // Return an error response
    http_response_code(500);
    echo "Error: Unable to save template.";
}

// Close the database connection
$stmt->close();
$mysqli->close();
?>
