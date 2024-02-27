// survey_server.js
const express = require('express');
const bodyParser = require('body-parser');

const app = express();
const PORT = 3001; // Use a different port for the survey builder server

// Set up middleware for parsing JSON requests
app.use(bodyParser.json());

// Define an endpoint to handle survey-related functionality
app.post('/submitSurvey', handleSurveySubmission);

// Function to handle survey submission
function handleSurveySubmission(req, res) {
  // Extract survey data from the request body
  const surveyData = req.body;

  // You need to implement the logic to handle survey data here
  // For example, you can log the survey data or perform other actions

  console.log('Received survey data:', surveyData);

  // Send a response to the client
  res.json({ message: 'Survey data received successfully' });
}

// Start the server
app.listen(PORT, () => {
  console.log(`Survey Builder Server is running on http://localhost:${PORT}`);
});

