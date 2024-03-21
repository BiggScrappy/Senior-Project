<?php
// MySQL database connection parameters
$servername = "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com"; 
$username = "admin"; 
$password = "AdminPass"; 
$database = "dam_database";

// Connect to MySQL database
if (!extension_loaded('mysqli')) {
    echo 'MySQLi extension is not installed.';
} else {
    echo 'MySQLi extension is installed.';
}

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate the date 10 days ago
$ten_days_ago = date('Y-m-d', strtotime('-10 days'));

// Query to find surveys created 10 days ago
$sql = "SELECT email FROM surveys WHERE created_at < '$ten_days_ago'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each row
    while($row = $result->fetch_assoc()) {
        // Generate email content
        $to = $row['email'];
        $subject = "Take Survey";
        $message = "Take the survey by clicking on the following link: http://your_survey_link_here";
        $headers = "From: smith2834@marshall.edu" . "\r\n" .
                   "Reply-To: smith2834@marshall.edu" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        // Save email content as draft on server
        $draftFilePath = '/path/to/drafts/' . uniqid() . '.txt';
        file_put_contents($draftFilePath, "To: $to\r\nSubject: $subject\r\n\r\n$message\r\n");

        // Send notification email to user with link to draft
        $notificationSubject = "Your Survey Draft";
        $notificationMessage = "We've created a draft of your survey email. You can edit and send it from Outlook. Click here: http://your_server_url/view_draft.php?draft=" . basename($draftFilePath);
        mail($to, $notificationSubject, $notificationMessage, $headers);
    }
} else {
    echo "No users found in the database whose last email sent date is older than 10 days";
}

// Close MySQL connection
$conn->close();
?>
