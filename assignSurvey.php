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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> </script> 
    <script type="text/javascript" src="jquery.js"></script> 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="style.css">
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
    <h1>Assign Survey</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   


    <form action="process-assignSurvey.php" method="post">
        
        <!--Select Organization-->
        <div>
            <label for="orgName">Select Organization</label>
            <select name="orgName", id="orgName">
            <?php
                $mysqli = require __DIR__ . "/database.php";
                $sql = "select name from organizations;";
                $result = $mysqli->query($sql);
                foreach($result as $i){
                    echo "<option value=\"".$i['name']."\">".$i['name']."</option>"; 
                }
            ?>               
            </select>
            <script>
                 $(document).ready(function() {
                  $('#orgName').select2();
                   });
            </script>
        </div>
       <!--Select Project-->
       <div>
            <label for="projectName">Select Project</label>
            <select name="projectName", id="projectName">

            <?php
                $sql = "select name from projects;";
                $result = $mysqli->query($sql);
                foreach($result as $i){
                    echo "<option value=\"".$i['name']."\">".$i['name']."</option>"; 
                }
            ?>               
            </select>
            <script>
                 $(document).ready(function() {
                  $('#projectName').select2();
                   });
            </script>
        </div>
       <!--Select Surveyor Role -->
       <div>
            <label for="surveyorRole">Select Surveyor Roles</label>
            <select name="surveyorRole", id="surveyorRole">

            <?php
                $sql = "select name from surveyor_roles;";
                $result = $mysqli->query($sql);
                foreach($result as $i){
                    echo "<option value=\"".$i['name']."\">".$i['name']."</option>"; 
                }
            ?>               
            </select>
            <script>
                 $(document).ready(function() {
                  $('#surveyorRole').select2();
                   });
            </script>
        </div>


        <!--select survey-->
        <div>
            <label for="surveyTemplate">Select Survey Template</label>
            <select name="surveyTemplate", id="surveyTemplate">

            <?php
                $sql = "select name from survey_templates;";
                $result = $mysqli->query($sql);
                foreach($result as $i){
                    echo "<option value=\"".$i['name']."\">".$i['name']."</option>"; 
                }
            ?>               
            </select>
            <script>
                 $(document).ready(function() {
                  $('#surveyTemplate').select2();
                   });
            </script>
        </div>

        <!--add start date-->
        <div>
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate">
        </div>

        <!--add end date-->
        <div>
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate">
        </div>
        <!--add respondent emails-->
        <div>
           <label for="email">Enter the emails of the users you would like to assign to this survey:</label>
           <select multiple name ="email[]" id="email">     
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
        <button>Assign Survey</button>
    </form>
  
</body>
</html>
