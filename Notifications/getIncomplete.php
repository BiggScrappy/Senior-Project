<?php
// Include the database connection file
include('database.php');

// Check if surveyId is set and not empty
if(isset($_POST['surveyId']) && !empty($_POST['surveyId'])) {
    // Retrieve surveyId from POST data
    $surveyId = $_POST['surveyId'];

    // Initialize an array to store incomplete email addresses
    $incompleteEmails = array();

    // SQL query to retrieve incomplete email addresses
    $sql = "SELECT DISTINCT email FROM users 
            JOIN user_surveys ON users.id = user_surveys.user_id 
            WHERE completed = 0 AND user_surveys.survey_id = '$surveyId'";

    $result = $mysqli->query($sql);

    // Check if query was successful
    if ($result) {
        // Fetch data and populate incompleteEmails array
        while ($row = $result->fetch_assoc()) {
            $incompleteEmails[] = $row['email'];
        }
        // Return JSON response with incompleteEmails array
        echo json_encode($incompleteEmails);
    } else {
        // If query failed, return an empty JSON array
        echo json_encode(array());
    }
} else {
    // If surveyId is not set or empty, return an empty JSON array
    echo json_encode(array());
}
?>
