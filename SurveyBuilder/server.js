const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');
const path = require('path');

const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Serve the HTML file from the root directory
app.get('/', (req, res) => {
  // Serve the HTML file from the same HTTP server
  res.sendFile(path.join(__dirname, 'SurveyBuilder.html'));
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
    db = await mysql.createPool(dbConfig);
    console.log('Connected to database successfully');
  

    
  
  } catch (error) {
    console.error('Error connecting to database:', error);
    process.exit(1);
  }
}

app.listen(port, () => {
  console.log(`Server listening on http://localhost:${port}`);
});

connectToDatabase(); // Call the function to connect to the database

// Route to handle survey data submission
app.post('/submit-survey', async (req, res) => {
  const surveyData = req.body;

  try {
    const db = await mysql.createConnection(dbConfig);

    // 1. Insert survey details into the 'surveys' table
    const [surveyResult] = await db.query('INSERT INTO surveys (title, locked, surveyDescription, surveyClosed) VALUES (?, ?, ?, ?)', [surveyData.title, surveyData.locked ? 1 : 0, surveyData.surveyDescription, 0] // Default closed to 0
    );

    const surveyID = surveyResult.insertId;

    // 2. Insert questions and associate them with the survey
    const questionIds = [];
    for (const question of surveyData.questions) {
      // Insert question into the 'questions' table
      const [questionResult] = await db.query('INSERT INTO questions (text, type) VALUES (?, ?)', [
        question.text,
        question.type,
      ]);
      const questionID = questionResult.insertId;
      questionIds.push(questionID);

      // Associate the question with the survey in the 'Survey_Questions' table
      await db.query('INSERT INTO Survey_Questions (surveyID, questionID) VALUES (?, ?)', [
        surveyID,
        questionID,
      ]);

      // Insert options for multiple-choice questions into the 'multiplechoice_options' table
      if (question.type === 'multiple-choice') {
        for (const option of question.options) {
          await db.query('INSERT INTO multiplechoice_options (questionID, optionText) VALUES (?, ?)', [
            questionID,
            option,
          ]);
        }
      }
    }

    // 3. Associate the user with the survey in the 'User_Surveys' table
    await db.query('INSERT INTO User_Surveys (userID, surveyID) VALUES (?, ?)', [
      surveyData.userID,
      surveyID,
    ]);

    console.log('Survey data received and stored successfully');
    res.status(200).json({ surveyId: surveyID, questionIds });
  } catch (error) {
    console.error('Error saving survey data to the database:', error);
    res.status(500).send('Internal Server Error');
  }
});
