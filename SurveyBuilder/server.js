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
  database: "dam_database",
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

// Function to insert sample data into the survey_templates table
async function insertSampleSurveyTemplates() {
  try {
    await db.query('INSERT INTO survey_templates (name, description, created_at, created_by) VALUES (?, ?, NOW(), 1)', ['Customer Satisfaction Survey', 'Survey to collect feedback from customers']);
    await db.query('INSERT INTO survey_templates (name, description, created_at, created_by) VALUES (?, ?, NOW(), 1)', ['Employee Engagement Survey', 'Survey to measure employee satisfaction and engagement']);
    console.log('Sample survey templates inserted successfully');
  } catch (error) {
    console.error('Error inserting sample survey templates:', error);
  }
}

// Function to insert sample data into the questions table
async function insertSampleQuestions() {
  try {
    await db.query('INSERT INTO questions (question_type_id, question, created_at, created_by) VALUES (?, ?, NOW(), 1)', [1, 'How satisfied are you with our products?']);
    await db.query('INSERT INTO questions (question_type_id, question, created_at, created_by) VALUES (?, ?, NOW(), 1)', [2, 'Are you happy with your work-life balance?']);
    await db.query('INSERT INTO questions (question_type_id, question, created_at, created_by) VALUES (?, ?, NOW(), 1)', [3, 'Rate your agreement with the following statement: "The company values my contributions."']);
    console.log('Sample questions inserted successfully');
  } catch (error) {
    console.error('Error inserting sample questions:', error);
  }
}

// Function to insert sample data into the surveys table
async function insertSampleSurveys() {
  try {
    await db.query('INSERT INTO surveys (survey_template_id, surveyor_id, organization_id, project_id, surveyor_role_id, start_date, end_date, created_at, created_by) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW(), 1)', [1, 1, 1, 1, 1]);
    await db.query('INSERT INTO surveys (survey_template_id, surveyor_id, organization_id, project_id, surveyor_role_id, start_date, end_date, created_at, created_by) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW(), 1)', [2, 1, 1, 1, 1]);
    console.log('Sample surveys inserted successfully');
  } catch (error) {
    console.error('Error inserting sample surveys:', error);
  }
}

// Insert sample data into the tables
async function insertSampleData() {
  await insertSampleSurveyTemplates();
  await insertSampleQuestions();
  await insertSampleSurveys();
}

// Call the function to insert sample data when the server starts
app.listen(port, async () => {
  console.log(`Server listening on http://localhost:${port}`);
  await insertSampleData();
});

app.listen(port, () => {
  console.log(`Server listening on http://localhost:${port}`);
});
