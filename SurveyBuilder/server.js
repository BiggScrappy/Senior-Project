const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');
const path = require('path');

const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(bodyParser.json());

// Serve the HTML file from the root directory
app.get('/', (req, res) => {
  // Serve the HTML file from the same HTTP server
  res.sendFile(path.join(__dirname, 'SurveyBuilder.html'));
});

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
  
    // Create the 'surveys_table' table
  await db.query(`
  CREATE TABLE IF NOT EXISTS surveys_table (
    surveyID INT AUTO_INCREMENT PRIMARY KEY,
    userID VARCHAR(255) NOT NULL,
    surveyTitle VARCHAR(255) NOT NULL,
    surveyDescription TEXT,
    surveyClosed BOOLEAN NOT NULL DEFAULT 0
  )
`);
    
  
  } catch (error) {
    console.error('Error connecting to database:', error);
    process.exit(1);
  }
}

connectToDatabase(); // Call the function to connect to the database

// Route to handle survey data submission
app.post('/submit-survey', async (req, res) => {
  const surveyData = req.body;

  try {
    // Insert survey details into the 'surveys_table'
    const [surveyResult] = await db.query('INSERT INTO surveys_table (userID, surveyTitle, surveyDescription, surveyClosed) VALUES (?, ?, ?, ?)', [
      surveyData.userID,
      surveyData.title,
      surveyData.description || '', // Use an empty string if surveyData.description is undefined or null
      surveyData.closed ? 1 : 0, // Convert boolean to integer for MySQL
    ]);

    const surveyID = surveyResult.insertId;

    // Insert each question into the 'questions_table'
    for (const question of surveyData.questions || []) { // Use an empty array if surveyData.questions is undefined or null
      const [questionResult] = await db.query('INSERT INTO questions_table (surveyID, question, assignSurvey) VALUES (?, ?, ?)', [
        surveyID,
        question.text,
        question.assignSurvey ? 1 : 0, // Convert boolean to integer for MySQL
      ]);

      const questionID = questionResult.insertId;

      // Insert question type into the 'question_type' table
      await db.query('INSERT INTO question_type (questionType, FK_questionID) VALUES (?, ?)', [
        question.type,
        questionID,
      ]);

      // If the question is of type 'multiple-choice', insert options into the 'mc_question_options_table'
      if (question.type === 'multiple-choice') {
        for (const option of question.options || []) { // Use an empty array if question.options is undefined or null
          await db.query('INSERT INTO mc_question_options_table (FK_questionID, optionText) VALUES (?, ?)', [
            questionID,
            option,
          ]);
        }
      }
    }

    // Insert survey assignment details into the 'assign_surveys_table'
    await db.query('INSERT INTO assign_surveys_table (FK_surveyID, FK_userID, orderDate, finishedDate) VALUES (?, ?, ?, ?)', [
      surveyID,
      surveyData.userID,
      surveyData.orderDate || null, // Use null if surveyData.orderDate is undefined
      surveyData.finishedDate || null, // Use null if surveyData.finishedDate is undefined
    ]);

    console.log('Survey data received and stored successfully');
    res.sendStatus(200);
  } catch (error) {
    console.error('Error saving survey data to the database:', error);
    res.status(500).send('Internal Server Error');
  }
});

// Start the server
app.listen(port, () => {
  console.log(`Server listening on port ${port}`);
});
