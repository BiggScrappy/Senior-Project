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
 <!--Select Survey -->
 <!--Ember Adkins 901893134-->
<!DOCTYPE html>
<html>
<head>
    <title>Select Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="userInformation.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="table.css">
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


<form action="fillOutSurvey.php" method="post">

 

<main class="table">
    <div class="wrapper">
    <section class="table_header">
    <h1>Select Survey To Complete</h1>
    <input type="text" id="myInput"  placeholder="Search...">
    </section>
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Survey ID</th>
                        <th>Survey Type</th>
                        <th>Organization</th>
                        <th>Project</th>
                        <th>Start Date</th>
                        <th>End Date</th>   
                    </tr>
                </thead>
                <tbody id="body">
<?php
    $user_id=$user["user_id"];

    //find user's assigned surveys
    $sql="select * from user_surveys where user_id=".$user_id." and completed !=1 and survey_id IS NOT NULL order by survey_id;";
    $result = $mysqli->query($sql);
 
    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
            $survey_id=$row["survey_id"];
        

            $sql2="select organization_id, project_id,survey_template_id,DATE(start_date) as start_date,DATE(end_date) as end_date from surveys where id=".$survey_id." and start_date is not null and end_date is not null and end_date>now();";
            $answer = $mysqli->query($sql2);
            if (mysqli_num_rows($answer) > 0) {

                while($new = mysqli_fetch_assoc($answer)) {
        
                    $sql="select name from organizations where id=".$new["organization_id"].";";
                    $thing=$mysqli->query($sql);
                    $orgName=mysqli_fetch_assoc($thing);

        
                    $sql="select name from projects where id=".$new["project_id"].";";
                    $thing=$mysqli->query($sql);
                    $projectName=mysqli_fetch_assoc($thing);

                    $sql="select name from survey_templates where id=".$new["survey_template_id"].";";
                    $thing=$mysqli->query($sql);
                    $surveyName=mysqli_fetch_assoc($thing);
        
                    echo "<tr>";
                    echo "<td>","<label> <input type='radio' id='".$survey_id."' name='survey_id' value='".$survey_id."'></label>","</td>";   
                    echo "<td>",$survey_id,"</td>";
                    echo "<td>",$surveyName["name"],"</td>";
                    echo "<td>",$orgName["name"],"</td>";
                    echo "<td>",$projectName["name"],"</td>";
                    echo "<td>",$new["start_date"],"</td>";
                    echo "<td>",$new["end_date"],"</td>";
                    echo "</tr>";
        }
    }
}}
 ?>   
      </tbody>
</table>
        </section>
</div>
    </section>
         
<button>Submit</button>
</form>
<script>
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#body tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
</script>
</body>
</html> 
