<?php
ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
session_start();
$mysqli = require __DIR__ . "/database.php";

if(isset($_SESSION["user_id"])){
    $sql = "SELECT * FROM User_Information WHERE user_id = {$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $organizationName = $_POST["organizationName"];

        $sql = "INSERT INTO organizations (name) VALUES ('$organizationName')";

        if ($mysqli->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Organization</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
  <style>
  </style>
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

<?php if(isset($user)): ?>
    <h1>Welcome</h1>
    <p>Hello <?= htmlspecialchars($user["username"]) ?></p>
<?php endif; ?>

<div class="container">
  <h1>New Organization</h1>

  <form id="organizationForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="form-group">
      <label for="organizationName">Organization Name:</label>
      <input type="text" id="organizationName" name="organizationName" required>
    </div>
    
    <input type="hidden" name="organization_id" value="auto_increment">

    <button type="submit" class="btn-primary">Create Organization</button>
  </form>
</div>

<button id="darkModeToggle" class="btn-primary" onclick="toggleDarkMode()">Toggle Dark Mode</button>

<script>
  function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
  }
</script>

</body>
</html>
