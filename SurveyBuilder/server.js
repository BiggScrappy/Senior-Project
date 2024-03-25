const express = require('express');
const { json } = require('body-parser');
const { createPool } = require('mysql2/promise');
const { join } = require('path');

const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(json());

// Serve the HTML file from the root directory
app.get('/', (req, res) => {
  res.sendFile(join(__dirname, 'SurveyBuilder.html'));
});

// Handle request for favicon.ico
app.get('/favicon.ico', (req, res) => res.status(204).end());

// Database connection details
const dbConfig = {
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
};

// Connect to the database
let db;
async function connectToDatabase() {
  try {
    const pool = await createPool(dbConfig);
    db = pool;
    console.log('Connected to database successfully');
  } catch (error) {
    console.error('Error connecting to database:', error);
    process.exit(1);
  }
}

connectToDatabase();

// Route to handle survey data submission
app.post('/submit-survey', async (req, res) => {
  const surveyData = req.body;
  const userID = parseInt(req.body.userID, 10);

  try {
    // 1. Insert survey details into the 'survey_templates' table
    const [surveyResult] = await db.query('INSERT INTO survey_templates(name, description) VALUES (?, ?)', [surveyData.title, '']);
    const surveyID = surveyResult.insertId;

    // 2. Insert questions and associate them with the survey
    const questionIds = [];
    for (const question of surveyData.questions) {
      const { text, type, options } = question;

      // Fetch the question_type_id from the question_types table
      const [questionTypeResult] = await db.query('SELECT id FROM question_types WHERE type = ?', [type]);
      const questionTypeId = questionTypeResult[0].id;

      // Insert the question into the 'questions' table
      const [questionResult] = await db.query('INSERT INTO questions(question, question_type_id) VALUES (?, ?)', [text, questionTypeId]);
      const questionID = questionResult.insertId;
      questionIds.push(questionID);

      // Associate the question with the survey in the 'survey_template_questions' table
      await db.query('INSERT INTO survey_template_questions (survey_template_id, question_id) VALUES (?, ?)', [surveyID, questionID]);

      // Insert options for multiple-choice, boolean, and Likert questions into the 'multiplechoice_options' table
      if (options && options.length > 0) {
        for (const option of options) {
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

app.listen(port, () => {
  console.log(`Server listening on http://localhost:${port}`);
});
