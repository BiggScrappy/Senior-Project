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
    <h1>View Survey</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>   

<?php
    $sql = "select * from User_Surveys
    where user_id=".$userID ." AND survey_id=".$survey_id." order by question_id;";
    $result = $mysqli->query($sql);

    if (mysqli_num_rows($result) > 0) {

        while($row = mysqli_fetch_assoc($result)) {
            $survey_id = $row["survey_id"];
            $question_id = $row["question_id"];
            $question_text = $row["question"];
            $question_type = $row["question_type_id"];

            echo $survey_id, " ", $question_id, " ", $question_text, "<br>";

            if($question_type==1){
                $sql="select option_text from multiplechoice_options where question_id=".$question_id.";";
                        $answers = $mysqli->query($sql);
                         
                        
                        foreach($answers as $thing){
                            foreach($thing as $answer){
                                ?>              
                               <li> <?php echo  $answer;?> </li>                        
                         <?php                          
                            } 
                        }  
                $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." ; ";
                $response = $mysqli->query($sql);
                $responseUser = mysqli_fetch_assoc($response);

                echo "User's Response: ", $responseUser["response"], "<br>","<br>";
            }
            elseif($question_type==2){
               echo "<ul>";
               echo" <li> True </li>";
               echo" <li> False </li>";
               echo "</ul>";

               $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." ; ";
                $response = $mysqli->query($sql);
                $responseUser = mysqli_fetch_assoc($response);

                echo "User's Response: ", $responseUser["response"], "<br>","<br>";
            }
            elseif($question_type==3){
               echo "<ul>";
               echo" <li> 1 </li>";
               echo" <li> 2 </li>";
               echo" <li> 3 </li>";
               echo" <li> 4 </li>";
               echo" <li> 5 </li>";
               echo "</ul>";
               $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." ; ";
               $response = $mysqli->query($sql);
               $responseUser = mysqli_fetch_assoc($response);

               echo "User's Response: ", $responseUser["response"], "<br>","<br>";
            }
            elseif($question_type==4){
                $sql="select response from responses where question_id =".$question_id." and survey_id=".$survey_id." ; ";
                $response = $mysqli->query($sql);
                $responseUser = mysqli_fetch_assoc($response);
                echo "User's Response: ", $responseUser["response"], "<br>","<br>";
            }

        }   
    }

    ?>

<p><a href="selectPreviousSurveys.php"> 
    <button class="btn"><i class="fa-regular fa-chart-bar"></i></i> View a Different Survey</button>
</a></p>

</body>
</html> 