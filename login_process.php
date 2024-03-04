<?php
// Include your database connection code here
$servername = "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com"; 
$username = "admin"; 
$password = "AdminPass"; 
$database = "dam_database";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user input
$userInputUsername = isset($_POST['username']) ? $_POST['username'] : '';
$userInputPassword = isset($_POST['password']) ? $_POST['password'] : '';

// Check if the provided credentials are valid
$query = "SELECT * FROM users WHERE username = '$userInputUsername' AND password = '$userInputPassword'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Successful login
    $user = $result->fetch_assoc();

    if ($user['role'] == 'admin') {
        // Redirect to admin dashboard
        header('Location: admin_dashboard.php');
    } else {
        // Redirect to normal user dashboard
        header('Location: normal_user_dashboard.php');
    }

    exit();
} else {
    // Invalid credentials, redirect back to the login page with an error
    header('Location: index.php?error=true');
    exit();
}
?>

