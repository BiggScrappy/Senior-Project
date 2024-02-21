<?php
// Dummy user data (replace with your authentication logic and database queries)
$users = [
    'admin' => ['password' => 'adminpass', 'role' => 'admin'],
    'surveyor' => ['password' => 'surveypass', 'role' => 'surveyor'],
    'respondent' => ['password' => 'respondentpass', 'role' => 'respondent']
];

// Retrieve user input
$userInputUsername = isset($_POST['username']) ? $_POST['username'] : '';
$userInputPassword = isset($_POST['password']) ? $_POST['password'] : '';

// Check if the provided credentials are valid
if (array_key_exists($userInputUsername, $users) && $userInputPassword === $users[$userInputUsername]['password']) {
    // Successful login, redirect based on user role
    switch ($users[$userInputUsername]['role']) {
        case 'admin':
            header('Location: admin_dashboard.php');
            break;
        case 'surveyor':
            header('Location: surveyor_dashboard.php');
            break;
        case 'respondent':
            header('Location: respondent_surveys.php');
            break;
        default:
            // Invalid role, redirect to login with an error
            header('Location: index.php?error=true');
            break;
    }
    exit();
} else {
    // Invalid credentials, redirect back to the login page with an error
    header('Location: index.php?error=true');
    exit();
}
?>
