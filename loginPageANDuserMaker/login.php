
 <!--login-->
 <!--Ember Adkins 901893134-->


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://kit.fontawesome.com/c51fcdbfd4.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
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

    <h1>Login</h1>

    <form action="process-login.php" method="post">
        <label for="email">email</label>
        <input type="email" name="email" id="email"
        value="<?= htmlspecialchars($_POST["email"] ?? "")  ?>">

        <label for="password">password</label>
        <input type="password" name="password" id="password">

        <button>Login</button>
    </form>

</body>
</html>
