// Function to add a new question to the interface
function addQuestion(type) {
  const questionContainer = document.getElementById('questionsContainer');

  // ... rest of the code for creating question elements (unchanged)

  // Trigger a POST request to add the question to the server
  const questionData = {
    text: questionInput.value, // Assuming questionInput captures question text
    type: type, // Assuming type is determined elsewhere
  };

  fetch('/add-question', { // Replace with your actual endpoint
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(questionData),
  })
    .then(response => {
      if (response.ok) {
        console.log('Question added successfully!');
        // Update UI to reflect successful question addition (optional)
      } else {
        console.error('Error adding question:', response.statusText);
        // Update UI to indicate an error (optional)
      }
    })
    .catch(error => {
      console.error('Error during question submission:', error);
      // Update UI to indicate an error (optional)
    });

  // Add the question element to the container (unchanged)
  questionContainer.appendChild(questionElement);

  // Update the survey preview (example for basic text display)
  updateSurveyPreview();
}

// Function to submit the survey data to the server
async function submitSurvey(event) {
  event.preventDefault(); // Prevent the default form submission behavior

  const userIDElement = document.getElementById('userID');
  const surveyTitle = document.getElementById('surveyTitle').value;
  const lockSurvey = document.getElementById('lockSurvey').checked;

  if (userIDElement) {
    const userID = userIDElement.value;
    const questions = [];

    // Collect questions from the questionsContainer
    const questionsContainer = document.getElementById('questionsContainer');
    const questionElements = questionsContainer.getElementsByClassName('question');

    for (let i = 0; i < questionElements.length; i++) {
      const questionElement = questionElements[i];
      const questionType = questionElement.dataset.type; // Assuming you add data-type attribute to the question div

      const questionText = questionElement.querySelector('input[type="text"]').value;
      const question = { text: questionText, type: questionType };

      if (questionType === 'multiple-choice') {
        const optionsContainer = questionElement.querySelector('div');
        const optionElements = optionsContainer.querySelectorAll('input[type="text"]');
        const options = Array.from(optionElements).map((option) => option.value);
        question.options = options;
      } else if (questionType === 'boolean') {
        const responseElement = questionElement.querySelector('select');
        const response = responseElement.value;
        question.response = response;
      } else if (questionType === 'likert') {
        const responseElement = questionElement.querySelector('select');
        const response = responseElement.value;
        question.response = response;
      }

      questions.push(question);
    }

    const surveyData = {
      userID,
      title: surveyTitle,
      locked: lockSurvey,
      questions,
    };

    try {
      const response = await fetch('/submit-survey', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(surveyData),
      });

      if (response.ok) {
        const { surveyId, questionIds } = await response.json();
        console.log('Survey data submitted successfully');
        // Reset survey builder interface or show confirmation message
      } else {
        const errorData = await response.json();
        console.error('Failed to submit survey data:', errorData.error);
        // Handle error, show error message, or retry submission
      }
    } catch (error) {
      console.error('Error submitting survey data:', error);
      // Handle error, show error message, or retry submission
    }
  } else {
    console.error('Element with ID "userID" not found');
    // Handle the case where the element is not found
  }
}

// Function to update the survey preview
function updateSurveyPreview() {
  const surveyPreviewContainer = document.getElementById('surveyPreview');
  surveyPreviewContainer.innerHTML = ''; // Clear the previous preview

  const questionsContainer = document.getElementById('questionsContainer');
  const questionElements = questionsContainer.getElementsByClassName('question');

  for (let i = 0; i < questionElements.length; i++) {
    const questionElement = questionElements[i];
    const questionType = questionElement.dataset.type;
    const questionText = questionElement.querySelector('input[type="text"]').value;

    const previewElement = document.createElement('div');
    previewElement.textContent = `${questionType}: ${questionText}`;
    surveyPreviewContainer.appendChild(previewElement);
  }
}
