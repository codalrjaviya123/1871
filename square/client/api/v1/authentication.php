<?php header("Access-Control-Allow-Origin: *");

$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["user_type"] = $session['user_type'];
    echoResponse(200, $session);
});

$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $request = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'password'), $request);
    
    $response = array();
    $db = new DbHandler();
    $password = $request->password;
    $email = $request->email;
    
    $user = $db->getOneRecord("select id, password, email, user_type from users where email='$email' and pms_status = 1");
    if ($user != NULL) {
        if(passwordHash::validate_pw($password, $user['password'])) {
            $response['status'] = "success";
            $response['message'] = 'Logged in successfully.';
            $response['uid'] = $user['id'];
            $response['email'] = $user['email'];
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['uid'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = $user['user_type'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    }else {
            $response['status'] = "error";
            $response['message'] = 'No such user is registered';
        }
    echoResponse(200, $response);
});

$app->post('/signup', function() use ($app) {
    $response = array();
    $request = json_decode($app->request->getBody());
    //verifyRequiredParams(array('email', 'name', 'password'), $request);
    
    require_once 'passwordHash.php';
    $db = new DbHandler();
    
    //Data for users
    $u->email = $request->email;
    $u->password = $request->password;
    $u->user_type = $request->user_type;
    $u->pms_status = 1;
    $u->last_logged_in = null;
    $u->created_by = 0;

    //Data for user details
    $ud->full_name = $request->full_name;
    $ud->phone_number = $request->phone_number;
    $ud->company_name = $request->company_name;
    $ud->bill_account_no = $request->bill_account_no;    
    
    $db = new DbHandler();
    $isUserExists = $db->getOneRecord("select 1 from users where email='$request->email'");

    if (!$isUserExists) {
        $u->password = passwordHash::generate_hash($u->password);
        
        $table_name = "users";
        $column_names = array('email', 'password', 'pms_status', 'user_type', 'last_logged_in', 'created_by');
        $result = $db->insertIntoTable($u, $column_names, $table_name);
        
        if ($result != NULL) {
            //Add data in user_details table
            $ud->user_id = $result;
            $table_name = "user_details";
            $column_names = array('user_id', 'full_name', 'company_name', 'phone_number', 'bill_account_no');
            $result = $db->insertIntoTable($ud, $column_names, $table_name);
        
            //Use transaction/rollback here
            if ($result != NULL) {
                unset($u);                
                $response["status"] = "success";
                $response["message"] = "User account created successfully";
                $response["uid"] = $ud->user_id;
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['uid'] = $response["uid"];
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                unset($ud);
                echoResponse(200, $response);
            } else {
                $response["status"] = "error";
                $response["message"] = "Failed to create user.Please try again";
                echoResponse(201, $response);
            }            
        } else {
            $response["status"] = "error";
            $response["message"] = "An user with the provided email exists!";
            echoResponse(201, $response);
        }        
    }
});

$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});
?>