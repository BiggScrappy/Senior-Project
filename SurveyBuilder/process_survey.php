<?php
session_start();

// Assuming you have a database connection
include_once 'database.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // If the form is not submitted, redirect to the form page
    header("Location: survey_form.php");
    exit();
}

// Process survey data
$surveyTitle = $_POST['surveyTitle'];
$lockSurvey = isset($_POST['lockSurvey']) ? 1 : 0;
$userID = $_POST['userID'];
$surveyDescription = $_POST['surveyDescription'];

// Insert survey template
$insertSurveyTemplateQuery = "INSERT INTO survey_templates (name, description, created_at, created_by, locked) VALUES (?, ?, NOW(), ?, ?)";
$stmt = $mysqli->prepare($insertSurveyTemplateQuery);
if ($stmt) {
    $stmt->bind_param("ssii", $surveyTitle, $surveyDescription, $userID, $lockSurvey);
    $stmt->execute();
    // Retrieve the last inserted ID
    $surveyTemplateId = $mysqli->insert_id;
    $stmt->close();
} else {
    // Handle the error
    echo "Error: " . $mysqli->error;
}

// Loop through each question and insert data . 

// Nates's attempt to fix the mc errors. 
if (isset($_POST['questionType']) && isset($_POST['questionText'])) {
    $questionTypes = $_POST['questionType'];
    $questionTexts = $_POST['questionText'];
    $optionsArray = $_POST['options']; // Retrieve all options

    // Check if both arrays have the same length
    if (count($questionTypes) === count($questionTexts) ) {
        $questionsCount = count($questionTypes);
        for ($i = 0; $i < $questionsCount; $i++) {
            $currentQuestionType = $questionTypes[$i];
            $currentQuestionText = $questionTexts[$i];
            
            if($currentQuestionType === "multiple-choice") {
                $currentQuestionType = 1;
            } elseif($currentQuestionType === "true-false") {
                $currentQuestionType = 2;
            } elseif($currentQuestionType === "likert") {
                $currentQuestionType = 3;
            } elseif($currentQuestionType === "open-ended") {
                $currentQuestionType = 4;
            }

            // Insert question
            $insertQuestionQuery = "INSERT INTO questions (question_type_id, question) VALUES (?, ?)";
            $stmt = $mysqli->prepare($insertQuestionQuery);
            $stmt->bind_param("ss", $currentQuestionType, $currentQuestionText);
            $stmt->execute();
            $questionId = $mysqli->insert_id;
            $stmt->close();

            // Additional logic for specific question types (e.g., options for multiple choice)
            if ($currentQuestionType === 1 && isset($options)) {
                $options = explode(',', $optionsArray[$i]); // Split options by commas <--- split the options string by commas using the 'explode' function, then insert each option into the database using prepared statements 
                foreach ($options as $optionText) {
                    $insertOptionQuery = "INSERT INTO multiplechoice_options (question_id, option_text) VALUES (?, ?)";
                    $stmt = $mysqli->prepare($insertOptionQuery);
                    $stmt->bind_param("ss", $questionId, $optionText);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // Insert into SURVEY_TEMPLATE_QUESTIONS table
            $insertTemplateQuestionQuery = "INSERT INTO survey_template_questions (survey_template_id, question_id) VALUES (?, ?)";
            $stmt = $mysqli->prepare($insertTemplateQuestionQuery);
            $stmt->bind_param("ii", $surveyTemplateId, $questionId);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        echo "Error: Number of question types, question texts, and options do not match.";
    }
}
