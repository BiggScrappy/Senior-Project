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
<!--Edit Live Survey -->
 <!--Ember Adkins 901893134-->
 <!DOCTYPE html>
 <html>
 <head>
     <title>Edit Live Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="userInformation.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="table.css">  
    
    <script src="https://kit.fontawesome.com/c51fcdbfd4.js" crossorigin="anonymous"></script>
    <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> </script> 
    <script type="text/javascript" src="jquery.js"></script> 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link rel="stylesheet" href="accordion.css">  
<style>
    $('#accordion').accordion({
    collapsible: true,
    active:1,
    heightStyle: 'content',
    beforeActivate: function() {/*...*/}
  });
</style>
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

<!--Edit Survey Form-->
<div class="container">
<form action="process-editLiveSurvey.php" method="post">
<h1>Edit Live Survey</h1>

<?php
//collect survey id
$surveyID= (isset($_POST["survey_id"]) ? $_POST["survey_id"] : '');
//save to session
$_SESSION["survey_id"]= $_POST["survey_id"];
echo $surveyID,"<br>";
//select survey info
$sql = "select * from surveys where id=".$surveyID.";";
$result = $mysqli->query($sql);
$row = mysqli_fetch_assoc($result);
//collect ids
$surveyTempID = $row["survey_template_id"];
$orgID=$row["organization_id"];
$projectID=$row["project_id"];
$surveyorRoleID=$row["surveyor_role_id"];

$surveyorID=$user["user_id"];
$createdAt=$row["created_at"];
$startDate=$row["start_date"];
$endDate=$row["end_date"];
//collect names
$sql="select name from organizations where id=".$orgID.";";
$thing=$mysqli->query($sql);
$orgName=mysqli_fetch_assoc($thing);

$sql="select name from projects where id=".$projectID.";";
$thing=$mysqli->query($sql);
$projectName=mysqli_fetch_assoc($thing);

$sql="select name from survey_templates where id=".$surveyTempID.";";
$thing=$mysqli->query($sql);
$surveyName=mysqli_fetch_assoc($thing);

$sql="select name from surveyor_roles where id=".$surveyorRoleID.";";
$thing=$mysqli->query($sql);
$surveyorRole=mysqli_fetch_assoc($thing);

?>
<!--Organization-->
<h2>Organization Name: <?php echo $orgName["name"]; ?></h2> 

<!--Project-->
<h2>Project Name: <?php echo $projectName["name"]; ?> </h2>
<!--Survey-->
<h2>Survey Type: <?php echo $surveyName["name"]; ?> </h2>

<h2>Surveyor Role: <?php echo $surveyorRole["name"]; ?> </h2>

<!--Edit Surveyor Role-->
<button class="accordion" type="button"><b>Edit Surveyor Role</b></button>
<div class="panel">
    <!--Select Surveyor Role -->
    <div>
        <label for="surveyorRole">Edit Surveyor Role</label>
        <select name="surveyorRole", id="surveyorRole" class="input">
        <?php
            $sql = "select * from surveyor_roles;";
            $result = $mysqli->query($sql);
            echo "<option disabled selected value>--select an option--</option>";
            foreach($result as $i){
                echo "<option value=\"".$i['id']."\">".$i['name']."</option>"; 
            }
        ?>               
        </select>
        <script>
                $(document).ready(function() {
                $('#surveyorRole').select2();
                });
        </script>
    </div>
</div>

    <!--Email List Current Users-->
    <main class="table">
        <div class="wrapper">
    <section class="table_header">
    <h1>Current Respondents</h1>
    </section>
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
    <tbody>
<?php
    //gather all users for given survey
    $sql="select user_id from user_surveys where survey_id=".$surveyID." and deleted_at is null;";
    $result = $mysqli->query($sql);
    foreach($result as $thing){
        $user_id= $thing["user_id"];
        $sql="select * from users where id=".$user_id.";";
        $userInfo = $mysqli->query($sql);
    
        foreach($userInfo as $user){
            echo "<tr>";
            echo "<td>",$user["id"],"</td>";
            echo "<td>",$user["username"],"</td>";
            echo "<td>", $user["email"],"</td>";  
            echo "</tr>";
        }

    }
?>
  </tbody>
</table>
        </section>
</div>
    </section>
<button class="accordion" type="button"><b>Remove Emails</b></button>
<div class="panel">
<!--remove respondent emails-->
     <div align="center">
           <label for="removeEmail">Enter the emails to delete:</label>
           <br>
           <select multiple name ="removeEmail[]" id="removeEmail" class="input" style="width: 600px;">     
                <?php
                    $sql = "select * from Survey_Email_List where survey_id=".$surveyID." and deleted is null;";
                    $result = $mysqli->query($sql);
                    foreach($result as $i){
                        echo "<option value=\"".$i['email']."\">".$i['email']."</option>"; 
                    }
                ?>  
            </select>
            <script>
                 $(document).ready(function() {
                  $('#removeEmail').select2();
                   });
            </script>
        </div>
</div>
<!--Edit Email List-->
<button class="accordion" type="button"><b>Add Emails</b></button>
<div class="panel">
<!--add respondent emails-->
     <div align="center">
           <label for="email">Enter the emails of the users you would like to assign to this survey:</label>
           <br>
           <select multiple name ="email[]" id="email" class="input" style="width: 600px;">     
                <?php
                    $sql = "select email from User_Information  where role_name='respondent';";
                    $result = $mysqli->query($sql);
                    foreach($result as $i){
                        echo "<option value=\"".$i['email']."\">".$i['email']."</option>"; 
                    }
                ?>  
            </select>
            <script>
                 $(document).ready(function() {
                  $('#email').select2();
                   });
            </script>
        </div>
</div>

<h2>Start Date: <?php echo $startDate; ?> </h2>
<!--Edit Start Date-->
<button class="accordion" type="button"><b>Edit Start Date</b></button>
<div class="panel">
    <!--add start date-->
    <div>
        <label for="startDate">New Start Date:</label>
        <input type="date" id="starrtDate" name="startDate" class="input"  value="">
        </div>
</div>
<h2>End Date: <?php echo $endDate; ?> </h2>
<!--Edit End Date-->
<button class="accordion" type="button"><b>Edit End Date</b></button>
<div class="panel">
    <!--add end date-->
    <div>
        <label for="endDate">New End Date:</label>
        <input type="date" id="endDate" name="endDate" class="input"  value="">
        </div>
</div>

<script>
    var acc = document.getElementsByClassName("accordion");
    var i;
    for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        /* Toggle between adding and removing the "active" class,
        to highlight the button that controls the panel */
        this.classList.toggle("active");

        /* Toggle between hiding and showing the active panel */
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
        panel.style.display = "none";
        } else {
        panel.style.display = "block";
        }
    });
    }
</script>

    </form>
    <button>Submit</button>
</div>

  
</body>
</html>
