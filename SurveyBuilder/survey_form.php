<?php
ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
session_start();

// Include database connection
$mysqli = require __DIR__ . "/database.php";

// Verify user info
if(isset($_SESSION["user_id"])){
  $sql = "SELECT * FROM User_Information WHERE user_id = {$_SESSION["user_id"]}";
  $result = $mysqli->query($sql);
  $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Survey Builder</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
  <style>
    /* General styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa; /* Light gray background */
      color: #333; /* Dark text color */
      transition: background-color 0.3s ease;
    }
    /* Dark mode styles */
    body.dark-mode {
      background-color: #333; /* Dark background color */
      color: #fff; /* Light text color */
    }

    body.dark-mode .container {
      background-color: #444; /* Dark container background */
    }

    body.dark-mode label {
      color: #ccc; /* Light gray label color in dark mode */
    }

    body.dark-mode input[type="text"],
    body.dark-mode input[type="number"],
    body.dark-mode textarea,
    body.dark-mode select {
      background-color: #444; /* Dark input background */
      color: #fff; /* Light text color */
      border-color: #666; /* Dark border color */
    }

    body.dark-mode button,
    body.dark-mode .btn-primary {
      background-color: #007bff; /* Blue button color in dark mode */
      color: #fff; /* Light text color */
    }

    body.dark-mode button:hover,
    body.dark-mode .btn-primary:hover {
      background-color: #0056b3; /* Darker shade of blue on hover in dark mode */
    }

    /* Success message style */
    .success-message {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="header">
  <a href="#default" class="logo">USACE Dam Safety</a>
  <div class="header-right">
    <a class="active" href="index.php">Home</a>
    <?php if(isset($_SESSION["user_id"])): ?>
      <a href="logout.php">Logout</a>
    <?php elseif(!isset($_SESSION["user_id"])): ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</div>

<?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> Role: <?= htmlspecialchars($user["role_name"]) ?></p>
<?php endif; ?>

<div class="container">
  <h1>Survey Builder</h1>

  <form id="surveyForm" method="post" action="process_survey.php">
    <div class="form-group">
      <label for="surveyTitle">Survey Title:</label>
      <input type="text" id="surveyTitle" name="surveyTitle" required>
    </div>

    <div class="form-group">
      <label for="lockSurvey">
        <input type="checkbox" id="lockSurvey" name="lockSurvey"> Lock Survey
      </label>
    </div>

    <!-- Removed the User ID input field and replaced it with session handling -->
    <!-- <div class="form-group">
      <label for="userID">User ID:</label>
      <input type="number" id="userID" name="userID" placeholder="Enter your user ID" required />
    </div> -->

    <div class="form-group">
      <label for="surveyDescription">Survey Description:</label>
      <textarea id="surveyDescription" name="surveyDescription" required></textarea>
    </div>

    <div id="questionsContainer"></div>
    <button type="button" id="addQuestionBtn" class="btn-primary">Add Question</button>
    <button type="button" id="submitSurveyBtn" class="btn-primary">Submit Survey</button>
  </form>
</div>

<!-- Dark mode toggle button -->
<button id="darkModeToggle" class="btn-primary" onclick="toggleDarkMode()">Toggle Dark Mode</button>

<script>
  // JavaScript for dynamic question generation
  const questionsContainer = document.getElementById('questionsContainer');
  const addQuestionBtn = document.getElementById('addQuestionBtn');
  const submitSurveyBtn = document.getElementById('submitSurveyBtn');
  const surveyForm = document.getElementById('surveyForm');

  addQuestionBtn.addEventListener('click', function() {
    // Create a new question container
    const questionContainer = document.createElement('div');
    questionContainer.classList.add('question-container'); // Add a class for styling

    // Generate question type selection
    const questionTypeSelect = document.createElement('select');
    questionTypeSelect.name = 'questionType[]'; // Array name for multiple questions
    questionTypeSelect.innerHTML = `
      <option value="">Select question type</option>
      <option value="open-ended">Open-Ended</option>
      <option value="multiple-choice">Multiple Choice</option>
      <option value="likert">Likert Scale</option>
      <option value="true-false">True/False</option>
    `;
    questionContainer.appendChild(questionTypeSelect);

    // Add input fields based on question type (implemented later)
    const questionInputContainer = document.createElement('div');
    questionInputContainer.classList.add('question-input-container'); // Add a class for styling
    questionContainer.appendChild(questionInputContainer);

    // Add button to remove the question
    const removeQuestionBtn = document.createElement('button');
    removeQuestionBtn.textContent = 'Remove Question';
    removeQuestionBtn.classList.add('btn-primary'); // Add class for button styling
    removeQuestionBtn.addEventListener('click', function() {
      questionsContainer.removeChild(questionContainer);
    });
    questionContainer.appendChild(removeQuestionBtn);

    // Update question input fields on type change
    questionTypeSelect.addEventListener('change', function() {
      const questionType = this.value;
      questionInputContainer.innerHTML = ''; // Clear existing inputs
      generateQuestionInputs(questionType, questionInputContainer);
    });

    // Call the function initially to generate input fields for the default option
    generateQuestionInputs(questionTypeSelect.value, questionInputContainer);

    // Add the question container to the form
    questionsContainer.appendChild(questionContainer);
  });

  // Function to generate question input fields based on selected type
  function generateQuestionInputs(questionType, questionInputContainer) {
    if (questionType === '') {
      // Do nothing or show a placeholder message
      return;
    }

    switch (questionType) {
      case 'open-ended':
        const openEndedInput = document.createElement('input');
        openEndedInput.type = 'text';
        openEndedInput.name = 'questionText[]'; // Array name for multiple questions
        openEndedInput.placeholder = 'Enter your question here...';
        questionInputContainer.appendChild(openEndedInput);
        break;

      case 'multiple-choice':
        // Add multiple choice question input fields
        const mcQuestionInput = document.createElement('input');
        mcQuestionInput.type = 'text';
        mcQuestionInput.name = 'questionText[]';  // Array name for multiple questions
        mcQuestionInput.placeholder = 'Enter your question here...';
        questionInputContainer.appendChild(mcQuestionInput);

        // Add multiple choice options input
        const mcOptionsInput = document.createElement('textarea');
        mcOptionsInput.name = 'options[]';  // Array name for multiple options
        mcOptionsInput.placeholder = 'Enter options separated by commas...';
        questionInputContainer.appendChild(mcOptionsInput);
        break;

      case 'likert':
        // Create Likert scale input fields
        const likertInput = document.createElement('input');
        likertInput.type = 'text';
        likertInput.name = 'questionText[]';  // Change to 'likertOption[]' to create an array
        likertInput.placeholder = 'Enter Likert option';
        questionInputContainer.appendChild(likertInput);
        break;

      case 'true-false':
        const trueFalseInput = document.createElement('input');
        trueFalseInput.type = 'text';
        trueFalseInput.name = 'questionText[]'; // Array name for multiple questions
        trueFalseInput.placeholder = 'Enter your question here...';
        questionInputContainer.appendChild(trueFalseInput);
        break;

      default:
        break;
    }
  }

  // JavaScript for toggling dark mode
  function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
  }

  // Function to confirm submission before submitting the form
  submitSurveyBtn.addEventListener('click', function() {
    if (confirm('Are you sure you want to submit the survey?')) {
      surveyForm.submit();
    }
  });
</script>

</body>
</html>
