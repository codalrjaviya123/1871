<?php 
require_once 'dbHandler.php';
   /*
    * Collect all Details from Angular HTTP Request.
    */   
    require_once 'passwordHash.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $response = array();
    $db = new DbHandler();
    $email = $request->email;
    $password = $request->password;
    
    $user = $db->getOneRecord("select id, password, email, user_type from users where email='$email' and pms_status = 1");
    if ($user != NULL) {
        
        //To-Do: Check for password hash match, as its not working for singin functionality
        if(passwordHash::validate_pw($password, $user['password'])) {
            $response['status'] = "success";
            $response['message'] = 'Logged in successfully.';
            $response['id'] = $user['id'];
            $response['email'] = $user['email'];
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = $user['user_type'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    } 
    else {
            $response['status'] = "error";
            $response['message'] = 'No such user is registered';
    }
    echo json_encode($response);