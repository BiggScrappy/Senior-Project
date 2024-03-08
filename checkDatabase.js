const mysql = require('mysql2');

// MySQL database connection parameters
const pool = mysql.createPool({
  connectionLimit: 10,
  host: "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com",
  user: "admin",
  password: "adminPass",
  database: "dam_database"
});

// Fetch survey data from the database
pool.query('SELECT * FROM surveys', (err, results) => {
  if (err) {
    console.error('Error fetching survey data from the database:', err);
  } else {
    console.log('Survey data fetched successfully:', results);
    // Do something with the fetched survey data
  }

  // Close the database connection
  pool.end();
});
