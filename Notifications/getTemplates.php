<?php
$mysqli = require __DIR__ . "/database.php";

// Retrieve the template ID from the POST request
$templateId = $_POST['template_id'];

// Prepare and execute the SQL query to fetch the message from the email_templates table
$sql = "select message from email_template WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $templateId);
$stmt->execute();
$stmt->bind_result($message);
$stmt->fetch();

// Return the fetched message
echo $message;

// Close the database connection
$stmt->close();
$mysqli->close();
?>
