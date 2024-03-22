 <!--Select Survey -->
 <!--Ember Adkins 901893134-->
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
    <title>Select Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

<!--Verify User Info-->
    <h1>Select Survey</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   


    <form action="fillOutSurvey.php" method="post">
<?php
    $user_id=$user["user_id"];

    //find user's assigned surveys
    $sql="select * from user_surveys where user_id=".$user_id." order by survey_id;";
    $result = $mysqli->query($sql);
 
    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
           $survey_id=$row["survey_id"];
           echo $survey_id,"<br>";

           $sql2="select * from surveys where id=".$survey_id.";";
           $result2 = $mysqli->query($sql2);
           $new = mysqli_fetch_assoc($result2);

           echo "survey template id: ",$new["survey_template_id"],"<br>";
           echo "surveyor id: ",$new["surveyor_id"],"<br>";
           echo "Org id: ",$new["organization_id"],"<br>";
           echo "project id: ",$new["project_id"],"<br>";
           echo "created at: ",$new["created_at"],"<br>";

           echo "<label> <input type='radio' id='".$survey_id."' name='survey' value='".$survey_id."'>Select</label> <br/>";
         
       

        }
    }
 ?>   
<button>Submit</button>
</form>

</body>
</html> 
