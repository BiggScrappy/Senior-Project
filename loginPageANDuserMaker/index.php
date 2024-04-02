<?php
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
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
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/c51fcdbfd4.js" crossorigin="anonymous"></script>
</head>
<body>
 <!--home page -->
 <!--Ember Adkins 901893134-->
 <!-- Load an icon library -->


<div class="header">
  <a href="#default" class="logo">USACE Dam Safety</a>
  <div class="header-right">
    <a class="active" href="index.php">Home</a>
  
<?php if(isset($_SESSION["user_id"])): ?>
    <a href="logout.php">Logout</a>
<?php elseif(!isset($_SESSION["user_id"])): ?>
    <a href="login.php">Login</a>
<?php endif; ?>
  </div>
</div>

    <h1>Home</h1>

    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> Role: <?= htmlspecialchars($user["role_name"]) ?></p>

        <?php if($user["role_name"]==="respondent"): ?>
            <p><a href="selectSurvey.php">
                <button class="btn"><i class="fa-regular fa-pen-to-square"></i> Fill Out Survey</button>
            </a></p>
            <p><a href="selectPreviousSurveys.php">
                <button class="btn"><i class="fa-regular fa-clipboard"></i> View Past Surveys</button>
            </a></p>


        <?php elseif($user["role_name"]==="admin"): ?>
            <p><a href="SurveyBuilder.html">
                <button class="btn"><i class="fa-regular fa-pen-to-square"></i> Make Survey</button>
           </a></p>
            <p><a href="adminQuickViewCurrentSurveys.php">
                <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> See Live Surveys</button>
            </a></p>
            <p><a href="adminSelectPreviousSurvey.php">
            <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> See Previous Surveys</button>
            </a></p>
            <p><a href="signup.html">
            <button class="btn"><i class="fa-regular fa-circle-user"></i></i> Make New User</button>
            </a></p>
            <p><a href="notification.php">
            <button class="btn"><i class="fa-regular fa-paper-plane"></i></i> Email Notification</button>
            </a></p>

        <?php elseif($user["role_name"]==="surveyor"): ?>
            <p><a href="assignSurvey.php">
            <button class="btn"><i class="fa-regular fa-pen-to-square"></i> Assign Survey</button>
            </a> </p>
            <p><a href="surveyorQuickViewCurrentSurveys.php">
            <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> See Live Surveys</button>
            </a> </p>
            <p><a href="surveyorSelectPreviousSurveys.php">
            <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> See Previous Surveys</button>
            </a> </p>

        <?php endif; ?>
    <?php endif; ?>


</body?>
</html>
