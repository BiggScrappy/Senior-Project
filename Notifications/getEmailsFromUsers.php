<?php
// Include your database connection file
include('database.php');

// Query to retrieve distinct email addresses from the users table
$sql = "SELECT DISTINCT email FROM users";
$result = $mysqli->query($sql);

// Array to store the fetched email addresses
$emails = array();

// Check if there are any results
if ($result->num_rows > 0) {
    // Loop through each row and fetch the email address
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

// Return the email addresses as JSON
echo json_encode($emails);

// Close the database connection
$mysqli->close();
?>
