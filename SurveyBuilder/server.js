const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors'); // Import the cors module
const mysql = require('mysql2/promise');
const app = express();
const port = 3000;

// Database connection details
const db = mysql.createConnection({
  host: 'damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com',
  user: 'admin',
  password: 'adminPass',
  database: 'dam_database',
});

// Ensure that the connection is established
db.connect((err) => {
  if (err) {
    console.error('Failed to connect to the database:', err);
    throw err;
  }
  console.log('Connected to the database');
});

app.use(bodyParser.json());
app.use(cors()); // Enable CORS with the cors middleware

// Handle POST request to submit survey data
app.post('/submitSurvey', async (req, res) => {
  const surveyData = req.body;

  try {
    // Insert survey data into 'surveys' table
    const [surveyResult] = await db.query('INSERT INTO surveys (title) VALUES (?)', [surveyData.title]);
    const surveyId = surveyResult.insertId;

    // Insert questions and answers into 'questions' and 'answers' tables
    for (const question of surveyData.questions) {
      const [questionResult] = await db.query('INSERT INTO questions (survey_id, text, response) VALUES (?, ?, ?)', [surveyId, question.text, question.response]);
      const questionId = questionResult.insertId;

      for (const answer of question.answers) {
        await db.query('INSERT INTO answers (question_id, text) VALUES (?, ?)', [questionId, answer]);
      }
    }

    console.log('Survey data received and stored successfully');
    res.send('Survey data received and stored successfully!');
  } catch (error) {
    console.error('Error saving survey data to the database:', error);
    res.status(500).send('Internal Server Error');
  }
});

// ... (other routes and server configurations)

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});

