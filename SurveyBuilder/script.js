// Function to add a new question to the interface
function addQuestion(type) {
  const questionContainer = document.getElementById('questionsContainer');

  // Create a new question element
  const questionElement = document.createElement('div');
  questionElement.classList.add('question');

  // Label for question text
  const questionLabel = document.createElement('label');
  questionLabel.textContent = 'Question:';
  questionElement.appendChild(questionLabel);

  // Input field for question text
  const questionInput = document.createElement('input');
  questionInput.type = 'text';
  questionInput.placeholder = 'Enter your question here';
  questionElement.appendChild(questionInput);

  // Additional elements based on question type
  if (type === 'multiple-choice') {
    const optionsContainer = document.createElement('div');
    optionsContainer.classList.add('options-container');

    // Add two initial options by default
    addOption(optionsContainer);
    addOption(optionsContainer);

    questionElement.appendChild(optionsContainer);

    // Button to add more options
    const addOptionButton = document.createElement('button');
    addOptionButton.textContent = 'Add Option';
    addOptionButton.addEventListener('click', () => addOption(optionsContainer));
    questionElement.appendChild(addOptionButton);
  }

  // Add the question element to the container
  questionContainer.appendChild(questionElement);

  // Update the survey preview (example for basic text display)
  updateSurveyPreview();
}

// Function to add an option element within a multiple-choice question
function addOption(optionsContainer) {
  const optionElement = document.createElement('div');
  optionElement.classList.add('option');

  // Label for option text
  const optionLabel = document.createElement('label');
  optionLabel.textContent = 'Option:';
  optionElement.appendChild(optionLabel);

  // Input field for option text
  const optionInput = document.createElement('input');
  optionInput.type = 'text';
  optionInput.placeholder = 'Enter option text';
  optionElement.appendChild(optionInput);

  // Button to remove the option
  const removeOptionButton = document.createElement('button');
  removeOptionButton.textContent = 'Remove';
  removeOptionButton.addEventListener('click', () => optionsContainer.removeChild(optionElement));
  optionElement.appendChild(removeOptionButton);

  // Add the option element to the container
  optionsContainer.appendChild(optionElement);
}

// Function to handle survey submission
async function submitSurvey(event) {
  event.preventDefault(); // Prevent default form submission behavior

  const surveyData = {
    title: document.getElementById('surveyTitle').value,
    locked: document.getElementById('lockSurvey').checked,
    userID: document.getElementById('userID').value,
    questions: [],
  };

  // Collect question data from the interface
  const questions = document.querySelectorAll('.question');
  for (const question of questions) {
    const questionText = question.querySelector('input[type="text"]').value;
    let options = [];

    if (question.querySelector('.options-container')) {
      const optionInputs = question.querySelectorAll('.option input[type="text"]');
      for (const optionInput of optionInputs) {
        options.push(optionInput.value);
      }
    }

    surveyData.questions.push({ text: questionText, type: options.length ? 'multiple-choice' : 'open-ended', options });
  }

  try {
    // Send an AJAX request to the server (using Fetch API)
    const response = await fetch('http://localhost:3000/submit-survey', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(surveyData),
    });

    const data = await response.json();

    if (response.ok) {
      console.log('Survey data submitted successfully:', data);
      // Update UI to indicate success (e.g., show a success message)
    } else {
      console.error('Error submitting survey data:', data);
      // Update UI to indicate error (e.g., show an error message)
    }
  } catch (error) {
    console.error('Error during survey submission:', error);
    // Update UI to indicate error (
  }

}