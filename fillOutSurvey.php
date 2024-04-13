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

$_SESSION["survey_id"]= $_POST["survey_id"];
?>
 <!--Fill Out Survey -->
 <!--Ember Adkins 901893134-->
<!DOCTYPE html>
<html>
<head>
    <title>Fill Out Survey</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="userInformation.css">
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
<div class="container">
<h1>Fill Out Survey</h1>
    <form action="process-fillOutSurvey.php" method="post" id="surveyForm">
        <!-- Gather Surveys-->
        <?php
            $userID = $user["user_id"];
            $survey_id= $_SESSION["survey_id"];
           // echo $survey_id;
            $sql = "select * from User_Surveys
            where user_id=".$userID ." AND survey_id=".$survey_id." order by question_id  ;";
            $result = $mysqli->query($sql);
            $questionNum=0;
            if (mysqli_num_rows($result) > 0) {

                while($row = mysqli_fetch_assoc($result)) {
                    $survey_id = $row["survey_id"];
                    $question_id = $row["question_id"];
                    $question_text = $row["question"];
                    $questionNum=$questionNum+1;
                    
                    echo $questionNum, ".) ";
                    echo $question_text;
                    echo "<br>";
             

                    if ($row["question_type_id"]==='2'){
                        echo "<div>"  ; 
                        
                     ?>
                    <input type="radio" id = 'true' name="<?php echo $questionNum ?>" value='true'>
                    <label for 'true'>True</label><br>
                    <input type="radio" id = "false" name="<?php echo $questionNum ?>" value='false'>
                    <label for 'false'>False</label><br>
        <?php
                        echo "</div>";
                        echo "<br>";
                    }
                    
                    elseif($row["question_type_id"]==='1'){
    
                        $sql="select option_text from multiplechoice_options where question_id=".$question_id.";";
                        $answers = $mysqli->query($sql);
                         

                        foreach($answers as $thing){
                            foreach($thing as $answer){
                                ?>              
                               <label> <input type="radio" id="<?php echo  $question_id; ?>" name="<?php echo $questionNum ?>" value="<?php echo  $answer; ?>"><?php echo $answer ?> </label> <br/>
                         
                         
                         <?php       
                            }   
                        }
                       
                        echo "<br>";
                    
                    }
                    elseif ($row["question_type_id"]==='3') { ?>
                        <div>
                        <input type="radio" id='1' name="<?php echo $questionNum ?>" value=1>
                        <label for '1'>1</label><br>
                        <input type="radio" id="2" name="<?php echo $questionNum ?>" value=2>
                        <label for '2'>2</label><br>
                        <input type="radio" id="3" name="<?php echo $questionNum ?>" value=3>
                        <label for '3'>3</label><br>
                        <input type="radio" id = "4" name="<?php echo $questionNum ?>" value=4>
                        <label for '4'>4</label><br>
                        <input type="radio" id='5' name="<?php echo $questionNum ?>" value=5>
                        <label for '5'>5</label><br>
                         <br />
                    </div>
                <?php
                    } 

                    elseif ($row["question_type_id"]==='4') { ?>
                       
                        <div>
                             <input type="text" id="openResponse" name="<?php echo $questionNum ?>">
                        </div>
                    
                <?php    
                    }





                 }
        }


    



            ?>


<button id="submitSurveyBtn">Submit Survey</button>
    </form>
    </div>

<script>
  // Assuming submitSurveyBtn and surveyForm are defined elsewhere in your code
  submitSurveyBtn.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default form submission action
    if (confirm('Are you sure you want to submit the survey? (Answers cannot be changed after submitted)')) {
      surveyForm.submit(); // Submit the form if confirmed
    }
  });
</script>

</body>
</html> 
