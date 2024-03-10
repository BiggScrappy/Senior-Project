<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a new MySQL connection
$host = "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com";
$dbname = "dam_database";
$username = "admin";
$password = "adminPass";

// Create a new MySQL connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_errno) {
    $error = "Connection failed: " . $mysqli->connect_error;
    http_response_code(500);
    echo $error;
    exit;
}

// Serve the Survey Builder HTML file
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $contents = file_get_contents('SurveyBuilderv4.html');
    echo $contents;
}

// Save survey data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surveyData = json_decode(file_get_contents("php://input"), true);

    // Insert the survey data into the database
    $title = $mysqli->real_escape_string($surveyData['title']);
    $evaluationPeriod = $mysqli->real_escape_string($surveyData['evaluationMonth'] . '-' . $surveyData['evaluationDay']);
    $lockSurvey = $surveyData['lockSurvey'] ? 1 : 0;

    $sql = "INSERT INTO surveys (title, evaluation_period, lock_survey) VALUES ('$title', '$evaluationPeriod', $lockSurvey)";

    if ($mysqli->query($sql) === TRUE) {
        $surveyId = $mysqli->insert_id;

        // Insert the questions and answers into the database
        foreach ($surveyData['questions'] as $question) {
            $questionText = $mysqli->real_escape_string($question['questionText']);
            $response = $mysqli->real_escape_string($question['response']);

            $sql = "INSERT INTO questions (survey_id, question_text, response) VALUES ($surveyId, '$questionText', '$response')";

            if ($mysqli->query($sql) === TRUE) {
                $questionId = $mysqli->insert_id;

                // Insert the answers for the question
                foreach ($question['answers'] as $answer) {
                    $answerText = $mysqli->real_escape_string($answer);
                    $sql = "INSERT INTO answers (question_id, answer_text) VALUES ($questionId, '$answerText')";
                    $mysqli->query($sql);
                }
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error inserting question: ' . $mysqli->error]);
                exit;
            }
        }

        http_response_code(200);
        echo json_encode(['message' => 'Survey data saved successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error inserting survey data: ' . $mysqli->error]);
    }
}

// Close the database connection
$mysqli->close();
