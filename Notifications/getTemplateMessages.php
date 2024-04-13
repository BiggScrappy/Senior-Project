<?php
include('database.php');

// Check if the template ID is provided in the request
if(isset($_POST["template_id"])) {
    $templateId = $_POST["template_id"];

    // Prepare and execute the SQL query to fetch the message for the given template ID
    $sql = "select message FROM email_template WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $templateId);
    $stmt->execute();
    $stmt->bind_result($message);
    
    // Fetch the result
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    // Return the message as JSON
    echo json_encode($message);
} else {
    // If template ID is not provided, return an error message
    echo json_encode("Error: Template ID not provided.");
}
?>
