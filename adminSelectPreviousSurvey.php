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

?>

 <!--View Survey -->
 <!--Ember Adkins 901893134-->
 <!DOCTYPE html>
<html>
<head>
    <title>Fill Out Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

<!--Verify User Info-->
    <h1>View Survey</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   

<form action="viewPreviousSurveys.php" method="post">
<?php
    $user_id = $user["user_id"];

    $sql="select * from surveys where end_date<now(); ";
    $result = $mysqli->query($sql);

    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
           $survey_id=$row["id"];
           echo $survey_id,"<br>";

           echo "survey template id: ",$row["survey_template_id"],"<br>";
           echo "surveyor id: ",$row["surveyor_id"],"<br>";
           echo "Org id: ",$row["organization_id"],"<br>";
           echo "project id: ",$row["project_id"],"<br>";
           echo "start date: ",$row["start_date"]," | ";
           echo "end date: ",$row["end_date"],"<br>";

           echo "<label> <input type='radio' id='".$survey_id."' name='survey_id' value='".$survey_id."'>Select</label> <br/>";
         
       

        }
    }

    ?>
    <button>Submit</button>
</form>
<p><a href="index.php">Go to Home</a></p>
</body>
</html> 