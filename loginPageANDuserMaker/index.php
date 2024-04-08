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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="userInformation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/c51fcdbfd4.js" crossorigin="anonymous"></script>
</head>
<body>
 <!--home page -->
 <!--Ember Adkins 901893134-->

    <?php if(isset($user)): ?>

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

      <!--Verify User Info-->
    <h1>Welcome</h1>
    <div class="userInformation">
    <?php if(isset($user)): ?>
        
        <p> <b> Hello <?= htmlspecialchars($user["username"]) ?>!</b></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> Role: <?= htmlspecialchars($user["role_name"]) ?></p> 
         
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   
    </div>


        <?php if($user["role_name"]==="respondent"): ?>
            <p><a href="selectSurvey.php">
                <button class="btn"><i class="fa-regular fa-pen-to-square"></i> Fill Out Survey</button>
            </a></p>
            <p><a href="selectPreviousSurveys.php">
                <button class="btn"><i class="fa-regular fa-clipboard"></i> View Past Surveys</button>
            </a></p>


        <?php elseif($user["role_name"]==="admin"): ?>
            <p><a href="survey_form.php">
                <button class="btn"><i class="fa-regular fa-pen-to-square"></i> Make Survey</button>
           </a></p>
            <p><a href="adminQuickViewCurrentSurveys.php">
                <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> See Live Surveys</button>
            </a></p>
            <p><a href="adminSelectPreviousSurvey.php">
            <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> See Previous Surveys</button>
            </a></p>
            <p><a href="makeNewUser.php">
            <button class="btn"><i class="fa-regular fa-circle-user"></i></i> Make New User</button>
            </a></p>
            <p><a href="notificationPage.php">
            <button class="btn"><i class="fa-regular fa-paper-plane"></i></i> Email Notification</button>
            </a></p>
            <p><a href="additionalFunctions.php">
            <button class="btn"><i class="fa-solid fa-gear"></i></i> Additional Functions</button>
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
    <?php else: ?>   
        <?php  
        include("login.php"); 
    ?> 
    <?php endif; ?>


</body?>
</html>
