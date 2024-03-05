const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const mysql = require('mysql2');

const app = express();
const port = 3000;

app.use(cors()); // Enable CORS

// MySQL database connection parameters
const pool = mysql.createPool({
  connectionLimit: 10,
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
});

// Middleware for parsing JSON
app.use(bodyParser.json());

// Connect to MySQL database
pool.getConnection((err, db) => {
  if (err) {
    console.error('Error connecting to MySQL database:', err);
  } else {
    console.log('Connected to MySQL database');
  }
});

// Save survey data to the database
app.post('/saveSurvey', (req, res) => {
  const surveyData = req.body;

  // Insert survey data into the database
  pool.query(
    'INSERT INTO surveys (title, evaluationMonth, evaluationDay, lockSurvey) VALUES (?, ?, ?, ?)',
    [surveyData.title, surveyData.evaluationMonth, surveyData.evaluationDay, surveyData.lockSurvey],
    (err, results) => {
      if (err) {
        console.error('Error inserting survey data into the database:', err);
        return res.status(500).send('Error saving survey data to the database');
      }

      // Get the survey ID from the inserted row
      const surveyId = results.insertId;

      // Use a function to recursively handle the insertion of questions and answers
      const insertQuestions = (index) => {
        if (index < surveyData.questions.length) {
          const question = surveyData.questions[index];

          // Insert questions into the database
          pool.query(
            'INSERT INTO questions (surveyId, questionText) VALUES (?, ?)',
            [surveyId, question.questionText],
            (err, questionResult) => {
              if (err) {
                console.error('Error inserting question data into the database:', err);
                return res.status(500).send('Error saving survey data to the database');
              }

              // Get the question ID from the inserted row
              const questionId = questionResult.insertId;

              // Insert answers into the database
              const insertAnswers = (answerIndex) => {
                if (answerIndex < question.answers.length) {
                  const answer = question.answers[answerIndex];

                  // Insert answers into the database
                  pool.query(
                    'INSERT INTO answers (questionId, answerText) VALUES (?, ?)',
                    [questionId, answer],
                    (err) => {
                      if (err) {
                        console.error('Error inserting answer data into the database:', err);
                        return res.status(500).send('Error saving survey data to the database');
                      }

                      // Recursively insert the next answer
                      insertAnswers(answerIndex + 1);
                    }
                  );
                } else {
                  // Recursively insert the next question
                  insertQuestions(index + 1);
                }
              };

              // Start the recursive process for answers
              insertAnswers(0);
            }
          );
        } else {
          // All questions and answers inserted successfully
          res.status(200).send('Survey data saved to the database');
        }
      };

      // Start the recursive process for questions
      insertQuestions(0);
    }
  );
});

// Start the server
app.listen(port, () => {
  console.log(`Server listening at http://localhost:${port}`);
});
