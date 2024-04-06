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
 <!--Make New User -->
 <!--Ember Adkins 901893134-->
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
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
<h1>Select Survey</h1>
<?php if(isset($user)): ?>
    <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
    <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
    <p> ID Number: <?= htmlspecialchars($user["user_id"]) ?></p>    
<?php else: ?>
    <p><a href="login.php">Login</a> </p>
<?php endif; 
?>   

    <h1>Make New User</h1>

    <form action="process-signup.php" method="post">
        <!--input user -->
        <div>
            <label for="name">name</label>
            <input type="text" id="name" name="name">
        </div>
        <!--put email-->
        <div>
            <label for="email">email</label>
            <input type="email" id="email" name="email">
        </div>
        <!--assign password-->
        <div>
            <label for="password">password</label>
            <input type="password" id="password" name="password">
        </div>
        <!--double check password-->
        <div>
            <label for="password_confirmation">Repeat Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        <!--select user role-->
        <div>
            <label for="role">Select User Role</label>
            <select name="role", id="role">
                <option value="Admin">Admin</option>
                <option value="Surveyor">Surveyor</option>
                <option value="Respondent">Respondent</option>
            </select>
        </div>

        <button>Make User</button>
        
    </form>

</body>
</html>
