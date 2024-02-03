<?php
if (isset($_POST['user'])&&isset($_POST['user_pass']))
{

    function validate($data){
        $data= trim($data);
        $data = stripslashes($data);
        $data= htmlspecialchars($data);
        return $data;
    }

    $username=validate ($_POST['username']);
    $password=validate($_POST['user_pass']);
    
    //make sure username and password isnt blank
    if(empty($username)){
        header("Location: Login.html?error=Username required");
        exit();
    }elseif(empty($password)){
        header("Location: Login.html?error=Password required");
        exit();
    }else{
        echo "Valid";
    }
}
else
{
    header("Location: Login.html");
    exit();
}
?>