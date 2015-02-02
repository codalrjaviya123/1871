<?php 
require_once 'dbHandler.php';
  /*
   * Collect all Details from Angular HTTP Request.
   */
    require_once 'passwordHash.php';    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
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
                echo json_encode($response);
            }
        } else {
        $response["status"] = "error";
            $response["message"] = "Failed to create user. Please try again";
            echo json_encode($response);
        }
    }
   else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided email exists!";
        echo json_encode($response);
    }
   