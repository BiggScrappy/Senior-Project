const express = require('express');
const { json } = require('body-parser');
const { createPool } = require('mysql2/promise');
const { join } = require('path');
const cors = require('cors');
const morgan = require('morgan');

const app = express();
const port = 3000;

// Log requests to the console
app.use(morgan('dev'));

// Use the cors middleware before your route handlers
app.use(cors());

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

// Function to handle CRUD operations for questions
app.post('/questions', async (req, res) => {
  // Handle creating a new question
  const { question, question_type_id } = req.body;

  try {
    const timeout = setTimeout(() => {
      throw new Error('Database operation timed out');
    }, 10000); // Set a timeout of 10 seconds (adjust as needed)

    const [result] = await db.query('INSERT INTO questions (question, question_type_id) VALUES (?, ?)', [question, question_type_id]);

    clearTimeout(timeout); // Clear the timeout if the operation succeeds
    res.status(201).json({ id: result.insertId });
  } catch (error) {
    console.error('Error creating question:', error.stack);
    console.error('Request body:', req.body);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

// Function to handle creating a new survey template
app.post('/survey_templates', async (req, res) => {
  // Handle creating a new survey template
  const { userID, title, description, locked, questions } = req.body; // Destructure the request body

  try {
    console.log('Request body:', req.body);

    // Start a database transaction for consistency
    await db.query('START TRANSACTION');

    // Validate title and description (optional, adjust as needed)
    if (!title || !description) {
      throw new Error('Missing required fields: title and description');
    }

    // Prepared statement for survey template insertion
    const insertTemplateQuery = 'INSERT INTO survey_templates (userID, name, description, locked) VALUES (?, ?, ?, ?)';
    const [surveyTemplateResult] = await db.query(insertTemplateQuery, [userID, title, description, locked]);
    const surveyTemplateId = surveyTemplateResult.insertId;

    // Assign questions to the survey template (assuming questions is an array of question IDs)
    for (const questionId of questions) {
      const assignQuestionQuery = 'INSERT INTO survey_template_questions (survey_template_id, question_id) VALUES (?, ?)';
      await db.query(assignQuestionQuery, [surveyTemplateId, questionId]);
    }

    // Commit the transaction if everything went well
    await db.query('COMMIT');

    console.log('SQL query:', insertTemplateQuery, [userID, title, description, locked]);

    res.status(201).json({ id: surveyTemplateId }); // Send a success response with the created template ID
  } catch (error) {
    await db.query('ROLLBACK'); // Rollback any changes if an error occurred
    console.error('Error creating survey template:', error);

    if (error.message.includes('timeout')) {
      res.status(500).json({ error: 'Database operation timed out. Please try again later.' });
    } else {
      res.status(500).json({ error: 'Internal Server Error' }); // Generic error for other issues
    }
  }
});


// Function to handle assigning questions to a survey template
app.post('/survey_template_questions', async (req, res) => {
  // Handle assigning questions to a survey template
  const { survey_template_id, question_id } = req.body;
  try {
    await db.query('INSERT INTO survey_template_questions (survey_template_id, question_id) VALUES (?, ?)', [survey_template_id, question_id]);
    res.status(201).json({ message: 'Questions assigned to survey template successfully' });
  } catch (error) {
    console.error('Error assigning questions to survey template:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

// Function to handle creating multiple-choice options for a question
app.post('/multiple_choice_options', async (req, res) => {
  // Handle creating multiple-choice options for a question
  const { question_id, option_text } = req.body;
  try {
    await db.query('INSERT INTO multiple_choice_options (question_id, option_text) VALUES (?, ?)', [question_id, option_text]);
    res.status(201).json({ message: 'Multiple-choice option created successfully' });
  } catch (error) {
    console.error('Error creating multiple-choice option:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

// Function to handle locking and unlocking a survey
app.put('/surveys/:id/lock', async (req, res) => {
  // Handle locking or unlocking a survey
  const { id } = req.params;
  const { locked } = req.body;
  try {
    await db.query('UPDATE surveys SET locked = ? WHERE id = ?', [locked, id]);
    res.status(200).json({ message: `Survey ${locked ? 'locked' : 'unlocked'} successfully` });
  } catch (error) {
    console.error('Error locking/unlocking survey:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

app.listen(port, () => {
  console.log(`Server listening on http://localhost:${port}`);
});
