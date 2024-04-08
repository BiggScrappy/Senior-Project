<?php
     ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
     session_start();

     //verify user info
     if(isset($_SESSION["user_id"])){

        $mysqli = require __DIR__ . "/database.php";
    
        $sql = "SELECT * FROM User_Information
                WHERE user_id = {$_SESSION["user_id"]}";
        
        $result = $mysqli->query($sql);
    
        $user = $result-> fetch_assoc();
    }
    $userID=$user['user_id'];

    $name= (isset($_POST["name"]) ? $_POST["name"] : '');
 
    $table= (isset($_POST["table"]) ? $_POST["table"] : '');
 

//project
if($table==="Project"){
    $sql = "INSERT INTO projects(name,created_at,created_by) VALUES(?,?,?);";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}
$today=date("Y,m,d");
$stmt->bind_param("sss",                 
                    $name,
                    $today,
                    $userID,                 
                  );
if($stmt->execute()){
    header("Location: additionalFunctions-success.php");
}
}
//organization
elseif($table==="Organization"){
    $sql = "INSERT INTO organizations(name,created_at,created_by) VALUES(?,?,?);";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}
$today=date("Y,m,d");
$stmt->bind_param("sss",                 
                    $name,
                    $today,
                    $userID,                 
                  );
if($stmt->execute()){
    header("Location: additionalFunctions-success.php");
}
}
//surveyor role
elseif($table==="SurveyorRole"){
    $sql = "INSERT INTO surveyor_roles(name,created_at,created_by) VALUES(?,?,?);";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}
$today=date("Y,m,d");
$stmt->bind_param("sss",                 
                    $name,
                    $today,
                    $userID,                 
                  );
if($stmt->execute()){
    header("Location: additionalFunctions-success.php");
}
}