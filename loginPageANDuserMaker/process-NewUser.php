 <!--Make New User -->
 <!--Ember Adkins 901893134-->
<?php

//check name
if (empty($_POST["name"])) {
    die("Name is required");
}
//check email
if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}
//check password
if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}
//hash password
$password_hash= password_hash($_POST["password"], PASSWORD_DEFAULT);
//connect to db
$mysqli = require __DIR__ . "/database.php";

//create user
$sql = "INSERT INTO users ( username,email,password)
        VALUES(?,?,?)";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}

$stmt->bind_param("sss",                 
                    $_POST["name"],
                    $_POST["email"],
                    $password_hash);

$stmt->execute();

if($_POST["role"]=="Admin"){
    $role = 1;
}
if($_POST["role"]=="Respondent"){
    $role = 2;
}
if($_POST["role"]=="Surveyor"){
    $role = 3;
}

$email= (isset($_POST["email"]) ? $_POST["email"] : '');

//collect user id
$sql = "select id from users where email = '". $email . "' ";

$result = $mysqli->query($sql);

$user = $result-> fetch_assoc();

$id = $user["id"];

//start user_roles insert
$sql = "INSERT INTO user_roles (user_id, role_id)
        VALUES(?,?)";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}

$stmt->bind_param("ss",
                    $id,
                    $role);

if ($stmt->execute()) {

    header("Location: newUser-success.php");
    exit;
    
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
