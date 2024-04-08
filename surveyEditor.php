<?php
ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/home1/missysme/sessions'));
session_start();

$mysqli = require __DIR__ . "/database.php";

if(isset($_SESSION["user_id"])){
    $sql = "SELECT * FROM User_Information WHERE user_id = {$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    $sql = "SELECT s.id, s.title, COUNT(us.id) AS total_responses,
            SUM(IF(us.completed = 1, 1, 0)) AS completed_responses
            FROM surveys s
            LEFT JOIN user_surveys us ON s.id = us.survey_id
            GROUP BY s.id";
    $result = $mysqli->query($sql);
    $surveys = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Survey Editor</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
  <style>
    .completed {
      color: green;
    }
    .incomplete {
      color: red;
    }
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
  <h1>Survey Editor</h1>

  <h2>Previous Surveys</h2>
  <ul>
    <?php foreach($surveys as $survey): ?>
      <li>
        <a href="edit_survey.php?survey_id=<?= $survey['id'] ?>">
          <?= $survey['title'] ?>
        </a>
        <?php if ($survey['completed_responses'] == $survey['total_responses']): ?>
          <span class="completed">Completed</span>
        <?php else: ?>
          <span class="incomplete">Incomplete</span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

</body>
</html>
