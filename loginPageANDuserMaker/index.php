<?php 

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
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <h1>Home</h1>

    <?php if(isset($user)): ?>
        <p> Hello <?= htmlspecialchars($user["username"]) ?></p>
        <p> Email: <?= htmlspecialchars($user["email"]) ?></p>
        <p> Role: <?= htmlspecialchars($user["role_name"]) ?></p>

        <?php if($user["role_name"]==="respondent"): ?>
            <p> u stink </p>

        <?php elseif($user["role_name"]==="admin"): ?>
            <p> u smell </p>

        <?php elseif($user["role_name"]==="surveyor"): ?>
            <p> u ok </p>

        <?php endif; ?>
        <p><a href="logout.php">Log out</a>

    <?php else: ?>
        <p><a href="login.php">Login</a> or <a href="signup.html"> </p>
    <?php endif; ?>


</body?>
</html>
