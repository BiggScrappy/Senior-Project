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

$userID = $user["user_id"];
$userRole=$user["role_name"];

?>

 <!--View Respondent List -->
 <!--Ember Adkins 901893134-->
 <!DOCTYPE html>
<html>
<head>
    <title>View Active Surveys</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">  
    <link rel="stylesheet" href="userInformation.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://kit.fontawesome.com/c51fcdbfd4.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
   
</head>
<body>


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


<?php

   
    $survey_id= (isset($_POST["survey_id"]) ? $_POST["survey_id"] : '');
    echo "survey id: ",$survey_id,"<br>";
    echo "Key: ", "completed: &#9745;","incomplete: &#9744; <br>";
?>
<main class="table">
    <section class="table_header">
    <h1>Respondents</h1>
    </section>
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
    <tbody>
<?php
    //gather all users for given survey
    $sql="select user_id from user_surveys where survey_id=".$survey_id.";";
    $result = $mysqli->query($sql);
    foreach($result as $thing){
        $user_id= $thing["user_id"];

     

        $sql="select * from users where id=".$user_id.";";
        $userInfo = $mysqli->query($sql);
        foreach($userInfo as $user){
            echo "<tr>";
            echo "<th>",$user["id"],"</th>";
            echo "<th>",$user["username"],"</th>";
            echo "<th>", $user["email"],"</th>";

            $sql="select completed from user_surveys where survey_id=".$survey_id." and user_id=".$user_id.";";
            $completed = $mysqli->query($sql);
            $row = mysqli_fetch_assoc($completed);
            if($row["completed"]==="1"){
              echo "<th>"," &#9745;" ,"</th>";
            }
            else{
              echo "<th>"," &#9744;" ,"</th>"; 
            }
        
            echo "</tr>";

        }


    }
?>
  </tbody>
</table>
        </section>
    </section>
<?php if($userRole==="surveyor"):?>
    <p><a href="surveyorQuickViewCurrentSurveys.php">
    <button class="btn"><i class="fa-regular fa-clipboard"></i> View Another Survey</button>
    </a></p>
<?php elseif($userRole==="admin"):?>
    <p><a href="adminQuickViewCurrentSurveys.php">
    <button class="btn"><i class="fa-regular fa-clipboard"></i> View Another Survey</button>
</a></p>
<?php endif; ?>
</body>
</html> 
