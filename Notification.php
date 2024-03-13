<?php
// MySQL database connection parameters
$servername = "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com"; 
$username = "admin"; 
$password = "AdminPass"; 
$database = "dam_database";

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Calculate the date 10 days ago from survey creation
//how get survey creation??
$sql = "SELECT created_at FROM surveys WHERE created_at < '$ten_days_ago'";
$result = $conn->query($sql);

$ten_days_ago = date('Y-m-d', strtotime('-10 days'));

//querry to find survey made 10 days ago

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each row
    while($row = $result->fetch_assoc()) {
        // Send email to each user
        $to = $row['email'];
        $subject = "Take Survey";
        $message = "Take the survey";
        $headers = "From: smith2834@marshall.edu" . "\r\n" .
                   "Reply-To: smith2834@marshall.edu" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        // Send email
        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent successfully to: $to<br>";
            // Update the last email sent date for the user
            $update_sql = "UPDATE users SET last_email_sent_date = CURDATE() WHERE email = '$to'";
            $conn->query($update_sql);
        } else {
            echo "Failed to send email to: $to<br>";
        }
    }
} else {
    echo "No users found in the database whose last email sent date is older than 10 days";
}

//Close MySQL connection
$conn->close();
?>
