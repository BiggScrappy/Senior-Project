 <!--Fill Out Survey -->
 <!--Ember Adkins 901893134-->
 <?php 
//ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
session_start();

if(isset($_SESSION["user_id"])){

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM User_Information
            WHERE user_id = {$_SESSION["user_id"]}";
    
    $result = $mysqli->query($sql);

    $user = $result-> fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fill Out Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

<!--Verify User Info-->
    <h1>Fill Out Survey</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   


    <form action="process-fillOutSurvey.php" method="post">
        <!-- Gather Surveys-->
        <?php
            $userID = $user["user_id"];

            $sql = "select * from User_Surveys
            where user_id=".$userID ." order by survey_id, question_id ;";
                    $result = $mysqli->query($sql);
        ?>

               <?php foreach($result as $i) {                  
                       $survey_id = $i["survey_id"];
                       $question_id = $i["question_id"];
                       $question_text = $i["question"];

                        if($i["question_type_id"]==='1'){
                            echo "confirm mc <br>";
                            echo "question: ", $question_text, "<br>";
                            $sql="select option_text from multiplechoice_options where question_id=".$question_id.";";
                            $answers = $mysqli->query($sql);
                         
                            Echo
                           "<select name='answer' id='answer-select'>";
                           Echo
                            "<option value=''>--Please choose an option--</option>";
                          
                           
                            foreach($answers as $thing){
                                foreach($thing as $answer){
                                    //echo $answer, "<br>";
                                    Echo"<div>";
                                    Echo  "<option value=".$answer.">".$answer."</option>";
                                    Echo"</div>";
                                }   
                            }
                            echo "<br>";
                        
                        }
                        elseif ($i["question_type_id"]==='2') {
                            echo "confirm tf <br>";
                            echo $question_id, ": " , $question_text,"<br>";
                            echo "<label for='true/false'>".$question_text."</label>";
                           
                          //  echo "<input type='radio' id='true' name='true/false' value='true' >";
                           //Echo "<label for 'true'>True</label><br>";
                          //  Echo "<input type='radio' id='false' name='true/false' value='false' >";
                           // Echo "<label for 'false'>False</label><br>";
                        
                        }  
                        elseif ($i["question_type_id"]==='3') {
                            echo "confirm likert";
                        }
                        elseif ($i["question_type_id"]==='4') {
                            echo "confirm open";}
                        
                        
                        
                    echo "<br>";
                   
                    }

        