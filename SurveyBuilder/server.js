const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');

const app = express();
const port = 3000;

// Database connection details
const dbConfig = {
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
};

let db;

// Function to connect to the database
async function connectDatabase() {
  try {
    db = await mysql.createPool(dbConfig);
    console.log('Connected to database successfully');
  } catch (error) {
    console.error('Error connecting to database:', error);
    process.exit(1);
  }
}

// Function to retrieve the database instance
function getDatabase() {
  if (!db) {
    throw new Error('Database not initialized');
  }
  return db;
}

// Connect to the database
connectDatabase();

// Configure bodyParser middleware
app.use(bodyParser.json());

// Serve the surveybuilder.html file
app.get('/', (req, res) => {
  res.sendFile(__dirname + '/surveybuilder.html');
});

// Route to handle survey data submission
app.post('/submit-survey', async (req, res) => {
  const surveyData = req.body;

  const db = getDatabase(); // Retrieve the database instance

  try {
    // Insert survey details into the 'surveys_table'
    const [surveyResult] = await db.query('INSERT INTO surveys_table (useriD, surveyTitle, surveyDescription, surveyClosed) VALUES (?, ?, ?, ?)', [
      surveyData.userID,
      surveyData.title,
      surveyData.description,
      surveyData.closed ? 1 : 0, // Convert boolean to integer for MySQL
    ]);
    const surveyID = surveyResult.insertId;

    // Insert each question into the 'questions_table'
    for (const question of surveyData.questions) {
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
        for (const option of question.options) {
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
      surveyData.orderDate,
      surveyData.finishedDate,
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

// Export functions for external use if needed
module.exports = { connectDatabase, getDatabase };
