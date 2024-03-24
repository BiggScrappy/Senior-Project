<?php
//ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
session_start();

if(isset($_SESSION["user_id"])){
    
    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM User_Information
            WHERE user_id = {$_SESSION["user_id"]}";
    
    $result = $mysqli->query($sql);

    $user = $result-> fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
 <!--home page -->
 <!--Ember Adkins 901893134-->
    <h1>Home</h1>

    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> Role: <?= htmlspecialchars($user["role_name"]) ?></p>

        <?php if($user["role_name"]==="respondent"): ?>
            <p><a href="selectSurvey.php">Fill Out Survey</a></p>
            <p><a href="selectPreviousSurveys.php">View Past Survey</a></p>

        <?php elseif($user["role_name"]==="admin"): ?>
            <p><a href="SurveyBuilder.html">Make Survey</a></p>
            <p><a href="adminQuickViewCurrentSurveys.php">See Live Surveys</a></p>
            <p><a href="SurveyBuilder.html">See Previous Surveys</a></p>
            <p><a href="signup.html">Make New User</a></p>
            <p><a href="Notification.php">Email Notification</a></p>

        <?php elseif($user["role_name"]==="surveyor"): ?>
            <p><a href="assignSurvey.php">Assign Survey</a> </p>
            <p><a href="surveyorQuickViewCurrentSurveys.php">View Current Surveys</a> </p>
            <p><a href="surveyorSelectPreviousSurveys.php">View Past Surveys</a> </p>


        <?php endif; ?>
        <p><a href="logout.php">Log out</a></p>

    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>


</body?>
</html>
