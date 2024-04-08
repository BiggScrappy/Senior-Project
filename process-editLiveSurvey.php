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
$survey_id= $_SESSION["survey_id"];
$today=date("Y,m,d");
//edit end date
if(isset($_POST["surveyorRole"])){
    $surveyorRole= (isset($_POST["surveyorRole"]) ? $_POST["surveyorRole"] : '');
    
     //insert into usersurveys
     $sql = "update surveys set updated_at='".$today."' , updated_by='". $userID."' , surveyor_role_id='".$surveyorRole."' where id='".$survey_id."';";
     $stmt = $mysqli -> stmt_init();
     if (! $stmt->prepare($sql)){
         die("sql womp womp" . $mysqli->error);
     }
     $stmt->execute(); 
    
 }




if(isset($_POST["email"])){
   
    $emailList = (isset($_POST["email"]) ? $_POST["email"] : '');
    foreach($emailList as $email){
        echo $email,"<br>";
        $sql = "select id from users where email='".$email. "' ;";
        $result = $mysqli->query($sql);
        $user = $result-> fetch_assoc();
        $RuserID = $user['id'];

       //insert into usersurveys
        $sql = "INSERT INTO user_surveys(user_id,survey_id,created_at,created_by) VALUES(?,?,?,?);";
        $stmt = $mysqli -> stmt_init();
        if (! $stmt->prepare($sql)){
            die("sql womp womp" . $mysqli->error);
        }
        $stmt->bind_param("ssss",
                        $RuserID,
                        $survey_id,
                        $today,
                        $userID
                    );
        $stmt->execute(); 
    }      
}


//check remove email
if(isset($_POST["removeEmail"])){
    //make email list
    $emailList= (isset($_POST["removeEmail"]) ? $_POST["removeEmail"] : '');
    foreach($emailList as $email){
        echo $email,"<br>";
        $sql = "select id from users where email='".$email. "' ;";
        $result = $mysqli->query($sql);
        $user = $result-> fetch_assoc();
        $RuserID = $user['id'];

       //insert into usersurveys
        $sql = "update user_surveys set deleted_at='".$today."' , deleted_by='". $userID."' where user_id='".$RuserID."' and survey_id='".$survey_id."';";
        $stmt = $mysqli -> stmt_init();
        if (! $stmt->prepare($sql)){
            die("sql womp womp" . $mysqli->error);
        }
        $stmt->execute(); 
    }      
}

//edit start date
if(!empty($_POST["startDate"])){
    
$startDate= (isset($_POST["startDate"]) ? $_POST["startDate"] : '');

    //insert into usersurveys
 $sql = "update surveys set updated_at='".$today."' , updated_by='". $userID."' , start_date='".$startDate."' where id='".$survey_id."';";
 $stmt = $mysqli -> stmt_init();
 if (! $stmt->prepare($sql)){
     die("sql womp womp" . $mysqli->error);
 }
 $stmt->execute(); 

 
}

//edit end date
if(!empty($_POST["endDate"])){
 $endDate= (isset($_POST["endDate"]) ? $_POST["endDate"] : '');    
  
       //insert into usersurveys
     $sql = "update surveys set updated_at='".$today."' , updated_by='". $userID."' , end_date='".$endDate."' where id='".$survey_id."';";
     $stmt = $mysqli -> stmt_init();
     if (! $stmt->prepare($sql)){
         die("sql womp womp" . $mysqli->error);
     }
     $stmt->execute(); 
      
    
    
 }