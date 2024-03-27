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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

<!--Verify User Info-->
    <h1>View Respondent List </h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   

<?php
   
    $survey_id= (isset($_POST["survey_id"]) ? $_POST["survey_id"] : '');
    echo "survey id: ",$survey_id,"<br>";
    echo "Key: ", "completed: &#9745;","incomplete: &#9744; <br>";
    //gather all users for given survey
    $sql="select user_id from user_surveys where survey_id=".$survey_id.";";
    $result = $mysqli->query($sql);
    foreach($result as $thing){
        $user_id= $thing["user_id"];

        echo $user_id, " | ";

        $sql="select * from users where id=".$user_id.";";
        $userInfo = $mysqli->query($sql);
        foreach($userInfo as $user){
            echo $user["username"]," | ";
            echo $user["email"]," | ";
            $sql="select completed from user_surveys where survey_id=".$survey_id." and user_id=".$user_id.";";
            $completed = $mysqli->query($sql);
            $row = mysqli_fetch_assoc($completed);
            if($row["completed"]==="1"){
              echo" &#9745;";
            }
            else{
              echo" &#9744;"; 
            }
        
            echo"<br>";

        }


    }
?>
<?php if($userRole==="surveyor"):?>
    <p><a href="surveyorQuickViewCurrentSurveys.php">View Surveys</a></p>
<?php elseif($userRole==="admin"):?>
    <p><a href="adminQuickViewCurrentSurveys.php">View Surveys</a></p>
<?php endif; ?>

<p><a href="index.php">Go to Home</a></p>
</body>
</html> 
