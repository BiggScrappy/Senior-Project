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


//Fill Out Survey -->
//Ember Adkins 901893134-->
   
    $userID = $user["user_id"];
    $survey_id= $_SESSION["survey_id"];

    
    $sql = "select * from User_Surveys
    where user_id=".$userID."  AND survey_id=".$survey_id." order by question_id  ;";
    $result = $mysqli->query($sql);
    $questionNum=0;

    while($row = mysqli_fetch_assoc($result)) {
        $survey_id = $row["survey_id"];
        $question_id = $row["question_id"];

        $questionNum=$questionNum+1;
        $answer= (isset($_POST["$questionNum"]) ? $_POST["$questionNum"] : '');   
        $sql = "INSERT INTO responses(question_id,survey_id,response,created_at,created_by)
                VALUES(?,?,?,?,?);";

        $stmt = $mysqli -> stmt_init();

        if (! $stmt->prepare($sql)){
         die("sql womp womp" . $mysqli->error);
        }
        $today=date("Y,m,d");
        $stmt->bind_param("sssss",      
                    $question_id,           
                    $survey_id,          
                    $answer,
                    $today,
                    $userID
                  );
        $stmt->execute();  
        
        $sql="update user_surveys set completed=1 where user_id=".$userID." and survey_id=".$survey_id.";";
        $stmt = $mysqli -> stmt_init();

        if (! $stmt->prepare($sql)){
         die("sql womp womp" . $mysqli->error);
        }
        $stmt->execute(); 

        header("Location: filloutSurvey-success.html");
     
    }
