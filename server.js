const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2');

const app = express();
const port = 3000;

// MySQL database connection parameters
const db = mysql.createConnection({
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
});

// Connect to MySQL database
db.connect((err) => {
  if (err) {
    console.error('Error connecting to MySQL database:', err);
  } else {
    console.log('Connected to MySQL database');
  }
});

// Middleware for parsing JSON
app.use(bodyParser.json());

// Save survey data to the database
app.post('/saveSurvey', (req, res) => {
  const surveyData = req.body;

  // Insert survey data into the database
  db.query('INSERT INTO surveys (title, evaluationMonth, evaluationDay, lockSurvey) VALUES (?, ?, ?, ?)',
    [surveyData.title, surveyData.evaluationMonth, surveyData.evaluationDay, surveyData.lockSurvey],
    (err, results) => {
      if (err) {
        console.error('Error inserting survey data into the database:', err);
        res.status(500).send('Error saving survey data to the database');
      } else {
        // Get the survey ID from the inserted row
        const surveyId = results.insertId;

        // Insert questions and answers into the database
        surveyData.questions.forEach((question) => {
          db.query('INSERT INTO questions (surveyId, questionText) VALUES (?, ?)',
            [surveyId, question.questionText],
            (err, questionResult) => {
              if (err) {
                console.error('Error inserting question data into the database:', err);
                res.status(500).send('Error saving survey data to the database');
              } else {
                // Get the question ID from the inserted row
                const questionId = questionResult.insertId;

                // Insert answers into the database
                question.answers.forEach((answer) => {
                  db.query('INSERT INTO answers (questionId, answerText) VALUES (?, ?)',
                    [questionId, answer],
                    (err) => {
                      if (err) {
                        console.error('Error inserting answer data into the database:', err);
                        res.status(500).send('Error saving survey data to the database');
                      }
                    });
                });
              }
            });
        });

        res.status(200).send('Survey data saved to the database');
      }
    });
});

// Start the server
app.listen(port, () => {
  console.log(`Server listening at http://localhost:${port}`);
});

