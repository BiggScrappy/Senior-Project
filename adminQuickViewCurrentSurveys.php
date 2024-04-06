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

 <!--View Active Surveys -->
 <!--Ember Adkins 901893134-->
 <!DOCTYPE html>
<html>
<head>
    <title>View Active Surveys</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
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
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   

<form action="viewRespondentList.php" method="post">


<main class="table">
    <section class="table_header">
    <h1>Active Surveys</h1>
    </section>
    <input type="text" id="myInput"  placeholder="Search for names..">

        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th>Surveys Completed</th>
                        <th>Survey ID Number</th>
                        <th>Survey Type</th>
                        <th>Organization Name</th>
                        <th>Project Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Select</th>
                    </tr>
                </thead>
    <tbody id="body">

<?php
    $user_id = $user["user_id"];

    $sql="select * from surveys where start_date is not null and survey_template_id is not null and  organization_id is not null and surveyor_role_id is not null; ";
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
            $sql="select name from organizations where id=".$row["organization_id"].";";
            $thing=$mysqli->query($sql);
            $orgName=mysqli_fetch_assoc($thing);
 
            $sql="select name from projects where id=".$row["project_id"].";";
            $thing=$mysqli->query($sql);
            $projectName=mysqli_fetch_assoc($thing);
        
            $sql="select name from survey_templates where id=".$row["survey_template_id"].";";
            $thing=$mysqli->query($sql);
            $surveyName=mysqli_fetch_assoc($thing);

            $percentage= number_format((float)$percentage,2,'.','');

            echo "<tr>";
            echo "<td>", $users_completed, "/",$user_count, " (",$percentage,"%)", "</td>";
            echo "<td> ", $survey_id,"</td>";

            echo "<td>",$surveyName["name"],"</td>";
            echo "<td>",$orgName["name"],"</td>";
            echo "<td>",$projectName["name"],"</td>";
            echo "<td>",$row["start_date"],"</td>";
            echo "<td>",$row["end_date"],"</td>";

            echo "<td> <label> <input type='radio' id='".$survey_id."' name='survey_id' value='".$survey_id."'>View Respondent List</label> </td>";  
            echo "</tr>"; }
           
         
        

        }
    }

    ?>
       </tbody>
                </table>
        </section>
    </section>
            
    <button>View List</button>
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
