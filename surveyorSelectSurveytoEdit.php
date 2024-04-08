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
 <!--Assign Survey -->
 <!--Ember Adkins 901893134-->
<!DOCTYPE html>
<html>
<head>
    <title>Assign Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">   
   <link rel="stylesheet" href="userInformation.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> </script> 
    <script type="text/javascript" src="jquery.js"></script> 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script src="https://kit.fontawesome.com/c51fcdbfd4.js" crossorigin="anonymous"></script>
    
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

<form action="editLiveSurvey.php" method="post">

<main class="table">
<div class="wrapper">
    <section class="table_header">
    <h1>Previous Surveys</h1>
    </section>
    <input type="text" id="myInput"  placeholder="Search..">
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th>Survey ID</th>
                        <th>Survey Type</th>
                        <th>Organization</th>
                        <th>Project</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody id="body">

<?php
    $user_id = $user["user_id"];

    $sql="select id, surveyor_id, organization_id, project_id, survey_template_id, DATE(start_date) as start_date,DATE(end_date) as end_date from surveys where surveyor_id=".$user_id." and end_date>now() and end_date!=0 and survey_template_id is not null and  organization_id is not null and surveyor_role_id is not null; ";
    $result = $mysqli->query($sql);

    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
           $survey_id=$row["id"];
         

           $sql="select name from organizations where id=".$row["organization_id"].";";
           $thing=$mysqli->query($sql);
           $orgName=mysqli_fetch_assoc($thing);

           $sql="select name from projects where id=".$row["project_id"].";";
           $thing=$mysqli->query($sql);
           $projectName=mysqli_fetch_assoc($thing);
       
           $sql="select name from survey_templates where id=".$row["survey_template_id"].";";
           $thing=$mysqli->query($sql);
           $surveyName=mysqli_fetch_assoc($thing);

           echo "<tr>";
           echo "<th>",$survey_id,"</th>";
           echo "<th>",$surveyName["name"],"</th>";
           echo "<th>",$orgName["name"],"</th>";
           echo "<th>",$projectName["name"],"</th>";
           echo "<th>",$row["start_date"],"</th>";
           echo "<th>",$row["end_date"],"</th>";


           echo "<th>","<label> <input type='radio' id='".$survey_id."' name='survey_id' value='".$survey_id."'>Select</label>","</th>";
         
        echo "</tr>";

        }
    }

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