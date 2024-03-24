const express = require('express');
const { json, urlencoded } = require('body-parser');
const { createPool, createConnection } = require('mysql2/promise');
const { join } = require('path');

const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(json());
app.use(urlencoded({ extended: true }));

// Serve the HTML file from the root directory
app.get('/', (req, res) => {
  // Serve the HTML file from the same HTTP server
  res.sendFile(join(__dirname, 'SurveyBuilder.html'));
});

// Handle request for favicon.ico
app.get('/favicon.ico', (req, res) => res.status(204).end());

// Database connection details (replace with your actual credentials)
const dbConfig = {
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
};

// Connect to the database
let db;

// Function to connect to the database
async function connectToDatabase() {
  try {
    db = await createPool(dbConfig);
    console.log('Connected to database successfully');
  } catch (error) {
    console.error('Error connecting to database:', error);
    process.exit(1); // Exit the process on failure
  }
}

app.listen(port, () => {
  console.log(`Server listening on http://localhost:${port}`);
});

connectToDatabase(); // Call the function to connect to the database

// Route to handle survey data submission
app.post('/submit-survey', async (req, res) => {
  const surveyData = req.body;
  const userID = parseInt(req.body.userID, 10);

  try {
    const db = await createConnection(dbConfig);

    // 1. Insert survey details into the 'surveys' table
    const [surveyResult] = await db.query('INSERT INTO survey_templates(name, description) VALUES (?, ?)', [surveyData.title, surveyData.surveyDescription]);
    const surveyID = surveyResult.insertId;

    // 2. Insert questions and associate them with the survey
    const questionIds = [];

    for (const question of surveyData.questions) {
      // Fetch the question_type_id from the question_types table
      let questionTypeId;
      switch (question.type) {
        case 1: // Boolean
          questionTypeId = 1;
          break;
        case 2: // Likert
          questionTypeId = 2;
          break;
        case 3: // Multiple-choice
          questionTypeId = 3;
          break;
        case 4: // Open-ended
          questionTypeId = 4;
          break;
        default:
          console.error(`Invalid question type: ${question.type}`);
          continue; // Skip unsupported types
      }

      // Insert the question into the database
      const [questionResult] = await db.query('INSERT INTO questions(question, question_type_id) VALUES (?, ?)', [question.text, questionTypeId]);
      const questionID = questionResult.insertId;
      questionIds.push(questionID);

      // Associate the question with the survey in the 'Survey_Questions' table
      await db.query('INSERT INTO survey_template_questions (survey_template_id, question_id) VALUES (?, ?)', [surveyID, questionID]);

      // Insert options for multiple-choice questions into the 'multiplechoice_options' table
      if (question.type === 3 /* Multiple-choice */) {
        for (const option of question.options) {
          await db.query('INSERT INTO multiplechoice_options (question_id, option_text) VALUES (?, ?)', [questionID, option]);
        }
      }
    }

    console.log('Survey data received and stored successfully');
    res.status(200).json({ surveyId: surveyID, questionIds });

  } catch (error) {
    console.error('Error saving survey data to the database:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});
