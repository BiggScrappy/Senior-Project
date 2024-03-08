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

// Check if the surveys table exists
pool.getConnection((err, db) => {
  if (err) {
    console.error('Error connecting to MySQL database:', err);
  } else {
    console.log('Connected to MySQL database');

    db.query('SHOW TABLES LIKE "surveys"', (err, results) => {
      if (err) {
        console.error('Error checking for surveys table:', err);
      } else {
        if (results.length === 0) {
          console.log('Surveys table does not exist');
          // You might want to create the table here if it doesn't exist
        } else {
          console.log('Surveys table exists');
        }
      }
    });
  }
});

// Save survey data to the database
app.post('/saveSurvey', (req, res) => {
  const surveyData = req.body;

  // Extract survey data
  const {
    title,
    evaluationMonth,
    evaluationDay,
    lockSurvey,
    questions
  } = surveyData;

  // Rest of your existing code for saving survey data
  // ...

  // Example: Insert survey data into surveys table
  const insertSurveyQuery = `
    INSERT INTO surveys (survey_template_id, surveyor_id, organization_id, project_id, surveyor_role_id, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by)
    VALUES (?, ?, ?, ?, ?, NOW(), ?, NOW(), ?, NULL, NULL)`;

  pool.query(insertSurveyQuery, [1, 1, 1, 1, 1, 1, 1], (err, results) => {
    if (err) {
      console.error('Error inserting survey data:', err);
      res.status(500).json({ error: 'Error saving survey data to the database' });
    } else {
      console.log('Survey data saved to the database');
      res.status(200).json({ message: 'Survey data saved successfully' });
    }
  });
});

// Start the server
app.listen(port, () => {
  console.log(`Server listening at http://localhost:${port}`);
});
