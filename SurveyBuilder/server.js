const express = require('express');
const cors = require('cors'); // Import the cors library

const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');

const app = express();
const port = 3000;

// Database connection details (replace with your actual credentials)
const dbConfig = {
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
};

// Connect to the database
let db;
try {
  db = mysql.createPool(dbConfig);
  console.log('Connected to database successfully');
} catch (error) {
  console.error('Error connecting to database:', error);
  process.exit(1);
}

// Configure CORS middleware with specific origin (adjust for production)
const allowedOrigins = ['http://localhost:3000', 'https://red-nananne-12.tiiny.site'];
const corsOptions = {
  origin: allowedOrigins,
};
app.use(cors(corsOptions));

// Configure bodyParser middleware
app.use(bodyParser.json());

// Serve the surveybuilder.html file
app.get('/', (req, res) => {
  res.sendFile(__dirname + '/surveybuilderv4.html');
});

// Route to handle survey data submission
app.post('/submit-survey', async (req, res) => {
  const surveyData = req.body;

  try {
    // ... (existing code for database operations)

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
