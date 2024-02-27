const mysql = require('mysql');

// MySQL database connection parameters
const connection = mysql.createConnection({
  host: 'damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com',
  user: 'admin',
  password: 'AdminPass',
  database: 'dam_database',
});

// Connect to MySQL database
connection.connect((err) => {
  if (err) {
    console.error('Database connection failed: ', err);
  } else {
    console.log('Connected to the database');
  }
});

module.exports = connection;
