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

$userID = $user["user_id"];

?>

 <!--View Active Surveys -->
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
    <h1>View Active Survey</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   

<form action="viewRespondentList.php" method="post">


<?php
    $user_id = $user["user_id"];

    $sql="select * from surveys where surveyor_id=".$user_id." ; ";
    $result = $mysqli->query($sql);

    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
           $survey_id=$row["id"];

           $sql="select count(*) as user_count from user_surveys where survey_id=".$survey_id.";";
           $count = $mysqli->query($sql);
           $user_count_list = mysqli_fetch_assoc($count); 
           $user_count= $user_count_list["user_count"];

           $sql="select count(*) as user_count from user_surveys where survey_id=".$survey_id." and completed=1;";
           $count_done= $mysqli->query($sql);
           $user_count_done = mysqli_fetch_assoc($count_done); 
           $users_completed = $user_count_done["user_count"];
           //circumvents division by zero error
           if($user_count>0){
            $percentage=($users_completed*100)/$user_count;
           }
           else{
            $percentage=0;
           }

        //filters out surveys with no users assigned, if any 
        if($user_count>0){
           echo "Surveys done: ", $users_completed, "/",$user_count, " (",$percentage,"%)", " | ";
           echo "survey number: ", $survey_id," | ";

           echo "survey template id: ",$row["survey_template_id"]," | ";
           echo "Org id: ",$row["organization_id"]," | ";
           echo "project id: ",$row["project_id"]," | ";
           echo "start date: ",$row["start_date"]," | ";
           echo "end date: ",$row["end_date"]," <br> ";

           echo "<label> <input type='radio' id='".$survey_id."' name='survey_id' value='".$survey_id."'>View Respondent List</label> <br/>";  
        }
           
         
        

        }
    }

    ?>
    <button>View List</button>
</form>
<p><a href="index.php">Go to Home</a></p>
</body>
</html> 