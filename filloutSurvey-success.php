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
<!--Assign Survey Success -->
 <!--Ember Adkins 901893134-->
 <!DOCTYPE html>
 <html>
 <head>
     <title>Signup</title>
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
<h1>Welcome</h1>
    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> </p>
    <?php endif; ?>  
<h1>Success!</h1>
   
    <p><a href="selectSurvey.php">
                <button class="btn"><i class="fa-regular fa-pen-to-square"></i> Fill Out Another Survey</button>
            </a></p>
    <p><a href="index.php">
                <button class="btn"><i class="fa-solid fa-house"></i></i> Go Home</button>
           </a></p>
    

 </body>
 </html>
