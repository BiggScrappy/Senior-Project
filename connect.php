<?php
    $email= $_POST['email'];
    $password= $_POST['password'];
    //echo $email;
    //db connection

    $con = new mysqli("localhost", "root", "", "test");
    //where its stored, sql username, password, db name
    if($con->connect_error){
        die("Connection failure : ".$con->connect_error);
    }else{
        //see if username is in db
        $stmt= $con->prepare(("select * from UserInfo where email=?"));
        $stmt= $bind_param("s", $email);
        $stmt->execute();
        $stmt_result=$stmt->get_result();
        //check if password from username matches the one in the db
        if($stmt_result->num_rows>0){
            $data=$stmt_result->fetch_assoc();
            if($data['password']===$password){
                header("Location: SampleForm.html");
            }else{
                echo "<h2>Invalid Email or Password</h2>";
            }
        }else{
            echo "<h2>Invalid Email or Password</h2>";
        }
    }
?>