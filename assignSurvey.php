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
</head>
<body>

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
            <label for="email">Enter the emails of the users you would like to assign to this survey, separated by a comma:</label>
            <input type="email" id="email" name="email" multiple list="users"/>

            <datalist id="users">
                
                <?php
                    $sql = "select email from users;";
                    $result = $mysqli->query($sql);
                    foreach($result as $i){
                        echo "<option value=\"".$i['email']."\">".$i['email']."</option>"; 
                    }
                ?>  
            </datalist>
            
        </div>
        <button>Assign Survey</button>
    </form>
    <p><a href="index.php">Go to Home</a></p>
</body>
</html>
