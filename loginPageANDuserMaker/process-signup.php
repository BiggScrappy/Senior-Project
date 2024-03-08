<?php

if (empty($_POST["name"])) {
    die("Name is required");
}

if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash= password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO users (id, username,email,password)
        VALUES(?,?,?,?)";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}

$stmt->bind_param("ssss",
                    $_POST["userID"],
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

$sql = "INSERT INTO user_roles (user_id, role_id)
        VALUES(?,?)";

$stmt = $mysqli -> stmt_init();

if (! $stmt->prepare($sql)){
    die("sql womp womp" . $mysqli->error);
}

$stmt->bind_param("ss",
                    $_POST["userID"],
                    $role);

if ($stmt->execute()) {

    header("Location: signup-success.html");
    exit;
    
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
