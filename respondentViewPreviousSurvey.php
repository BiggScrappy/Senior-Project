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
$survey_id= (isset($_POST["survey_id"]) ? $_POST["survey_id"] : '');
$userID = $user["user_id"];
?>

 <!--View Survey -->
 <!--Ember Adkins 901893134-->
 <!DOCTYPE html>
<html>
<head>
    <title>Fill Out Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="userInformation.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
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


    <h1>View Survey</h1>
<?php
    echo "<b> Survey ID: ", $survey_id,"</b><br>";
    $sql = "select * from User_Surveys
    where user_id=".$userID ." AND survey_id=".$survey_id." order by question_id;";
    $result = $mysqli->query($sql);

    $questionNumber=0;
    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
            $survey_id = $row["survey_id"];
            $question_id = $row["question_id"];
            $question_text = $row["question"];
            $question_type = $row["question_type_id"];
            $questionNumber=$questionNumber+1;
            echo $questionNumber, ".) ", $question_text, "<br>";

            if($question_type==1){
                $sql="select option_text from multiplechoice_options where question_id=".$question_id.";";
                        $answers = $mysqli->query($sql);
                        $optionNum = 1;
                        
                        foreach($answers as $thing){
                            foreach($thing as $answer){
                                ?>              
                                <p style='margin-left: 25px;'> <?php echo $optionNum, ".) ",$answer;?> </p>                         
                         <?php   
                             $optionNum=$optionNum +1;                        
                            } 
                        }  
                $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." and created_by=".$userID." ; ";
                $response = $mysqli->query($sql);
                $responseUser = mysqli_fetch_assoc($response);

                echo "<b> Your Response: ", $responseUser["response"], "</b><br>","<br>";
            }
            elseif($question_type==2){
                echo" <b> True </b>";
                echo "/";
                echo" <b>  False </b>";
                echo "<br>";

               $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." and created_by=".$userID."  ; ";
                $response = $mysqli->query($sql);
                $responseUser = mysqli_fetch_assoc($response);

                echo "<b> Your Response: ", $responseUser["response"], "</b><br>","<br>";
            }
            elseif($question_type==3){
               echo" <b>disagree 1, 2, 3, 4, 5 agree </b><br>";
               $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." and created_by=".$userID."  ; ";
               $response = $mysqli->query($sql);
               $responseUser = mysqli_fetch_assoc($response);

               echo "<b>","Your Response: ", $responseUser["response"], "</b><br>","<br>";
            }
            elseif($question_type==4){
                $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." and created_by=".$userID."  ; ";
                $response = $mysqli->query($sql);
                $responseUser = mysqli_fetch_assoc($response);
                echo "<b> Your Response: ", $responseUser["response"], "</b><br>","<br>";
            }
             
        }   
    }

    ?>

<p><a href="selectPreviousSurveys.php"> 
    <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> View a Different Survey</button>
</a></p>

</body>
</html> 
