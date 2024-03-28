<?php
session_start();

// Include the database connection file
include_once 'database.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $surveyTitle = $_POST['surveyTitle'];
    $lockSurvey = isset($_POST['lockSurvey']) ? 1 : 0;
    $userID = $_POST['userID'];
    $surveyDescription = $_POST['surveyDescription'];
    $questionText = $_POST['questionText'];  // Array of question texts
    $questionType = $_POST['questionType'];  // Array of question types

    // Begin transaction
    $mysqli->begin_transaction();

    try {
        // Insert survey template
        $insertSurveyTemplateQuery = "INSERT INTO survey_templates (name, description, created_at, created_by) VALUES (?, ?, NOW(), ?)";
        $stmt = $mysqli->prepare($insertSurveyTemplateQuery);
        $stmt->bind_param("ssi", $surveyTitle, $surveyDescription, $userID);
        $stmt->execute();

        // Retrieve the last inserted ID
        $surveyTemplateId = $mysqli->insert_id;
        $stmt->close();

        // Loop through each question and insert data
        for ($i = 0; $i < count($questionText); $i++) {
            $currentQuestionText = $questionText[$i];
            $currentQuestionType = $questionType[$i];

            // Insert question
            $insertQuestionQuery = "INSERT INTO questions (question_type_id, question, created_at, created_by) VALUES (?, ?, NOW(), ?)";
            $stmt = $mysqli->prepare($insertQuestionQuery);
            $stmt->bind_param("isi", $currentQuestionType, $currentQuestionText, $userID);
            $stmt->execute();
            $questionId = $mysqli->insert_id;
            $stmt->close();

            // Insert into survey_template_questions
            $insertSurveyTemplateQuestionQuery = "INSERT INTO survey_template_questions (question_id, survey_template_id, created_at, created_by) VALUES (?, ?, NOW(), ?)";
            $stmt = $mysqli->prepare($insertSurveyTemplateQuestionQuery);
            $stmt->bind_param("iis", $questionId, $surveyTemplateId, $userID);
            $stmt->execute();
            $stmt->close();

            // Handle additional logic for specific question types (e.g., options for multiple choice)
            if ($currentQuestionType == "multiple-choice") {
                // Assuming options are submitted as an array within each questionText element (adjust based on your implementation)
                $options = json_decode($currentQuestionText, true)['options'];  // Assuming options are encoded in JSON within questionText
                if (is_array($options)) {
                    foreach ($options as $optionText) {
                        $insertOptionQuery = "INSERT INTO multiplechoice_options (question_id, option_text, created_at, created_by) VALUES (?, ?, NOW(), ?)";
                        $stmt = $mysqli->prepare($insertOptionQuery);
                        $stmt->bind_param("isi", $questionId, $optionText, $userID);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
            // ... Implement similar logic for other question types with additional data (if applicable)
        }

        // Commit transaction
        $mysqli->commit();

        // Redirect after successful form submission
        header("Location: success.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $mysqli->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // If the form is not submitted, redirect to the form page
    header("Location: survey_form.php");
    exit();
}
?>
