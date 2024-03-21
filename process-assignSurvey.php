 <!--Assign Survey -->
 <!--Ember Adkins 901893134-->
<?php
    //redirect session save data to useable path on bluehost
     //ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
     session_start();

     //verify user info
     if(isset($_SESSION["user_id"])){

        $mysqli = require __DIR__ . "/database.php";
    
        $sql = "SELECT * FROM User_Information
                WHERE user_id = {$_SESSION["user_id"]}";
        
        $result = $mysqli->query($sql);
    
        $user = $result-> fetch_assoc();
    }
   
    $userID = $user["user_id"];
    //gather info from form
    $orgName= (isset($_POST["orgName"]) ? $_POST["orgName"] : '');
    $projectName= (isset($_POST["projectName"]) ? $_POST["projectName"] : '');
    $surveyorRole= (isset($_POST["surveyorRole"]) ? $_POST["surveyorRole"] : '');
    $surveyTemplate= (isset($_POST["surveyTemplate"]) ? $_POST["surveyTemplate"] : '');

    $mysqli = require __DIR__ . "/database.php";

     //Grab Survey id
     $sql = "select id from survey_templates where name='".$surveyTemplate. "' ;";
     $surveyTemplates = $mysqli->query($sql);
     $survey = $surveyTemplates-> fetch_assoc();
     $surveyID = $survey['id'];

     
    
    
    //Grab Organization ID
    $sql = "select id from organizations where name='".$orgName. "' ;";
    $organizations = $mysqli->query($sql);
    $org = $organizations-> fetch_assoc();
    $orgID = $org['id'];
     

    //Grab project ID
    $sql = "select id, name from projects where name='".$projectName."' ;";
    $projects = $mysqli->query($sql);
    $project = $projects-> fetch_assoc();
    $projectID = $project['id'];
     

    //Grab surveyor Role ID
    $sql = "select id, name from surveyor_roles where name='".$surveyorRole."' ;";
    $surveyorRoles = $mysqli->query($sql);
    $role = $surveyorRoles-> fetch_assoc();
    $roleID = $role['id'];
 
    

//insert assignment into db
$sql = "INSERT INTO surveys(survey_template_id,surveyor_id,organization_id, project_id,surveyor_role_id,created_at,created_by)
VALUES(?,?,?,?,?,?,?);";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}
$today=date("Y,m,d");
$stmt->bind_param("sssssss",                 
                    $surveyID,
                    $userID,
                    $orgID,
                    $projectID,
                    $roleID,
                    $today,
                    $userID
                  );

$stmt->execute();  

$sql= "select id from surveys where(survey_template_id=".$surveyID." and surveyor_id=".$userID." and organization_id=".$orgID." and project_id=".$projectID.");";
$result = $mysqli->query($sql);
$survey = $result-> fetch_assoc();
$surveyID = $survey['id'];

//make email list
$newEmailList=array (explode(",",$emailList));
    //grab ids for users based on email
    foreach($newEmailList as $i){
        foreach($i as $email){
            $sql = "select id from users where email='".$email. "' ;";
            $result = $mysqli->query($sql);
            $user = $result-> fetch_assoc();
            $userID = $user['id'];
            echo $userID, "<br>";
            $sql = "INSERT INTO user_surveys(user_id,survey_id) VALUES(?,?);";
            $stmt = $mysqli -> stmt_init();
            if (! $stmt->prepare($sql)){
                die("sql womp womp" . $mysqli->error);
            }
            $stmt->bind_param("ss",
                            $userID,
                            $surveyID);
            $stmt->execute(); 
        }     
    }

header("Location: assignSurvey-success.html");
exit;


     
