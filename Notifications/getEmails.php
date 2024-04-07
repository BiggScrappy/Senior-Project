<?php
    include('database.php');
    // Fetch emails based on the selected survey ID
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["survey_id"])) {
        $surveyId = $mysqli->real_escape_string($_POST["survey_id"]);
        $emails = array();
        $sql = "SELECT DISTINCT email FROM users 
                JOIN user_surveys ON users.id = user_surveys.user_id 
                WHERE user_surveys.survey_id = '$surveyId'";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row['email'];
            }
        }
        echo json_encode($emails);
    }
?>
