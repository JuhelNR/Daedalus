<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('db.php');
require_once('validation.php');
require_once('user.model.php');



$logDir = "../helios";
$logFile = $logDir . "/errors.log";

//CREATE DIRECTORY IF IT DOESN'T EXIST
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

//HELPER FUNCTION TO LOG MESSAGES INTO FILE
function logError($message, $endpoint)
{
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, $endpoint, "[$timestamp] $message\n", FILE_APPEND);
}

$errors = [];


$action = $_POST["action"];

switch ($action) {
    case 'login':
        loginuser();
        break;

    case 'sign-up':
        signupuser();
        break;
}

//LOGIN EXISTING USER
function loginuser()
{
    session_start();

    $endpoint = "login";

    //GET POST INPUTS
    $email = $_POST["email"];
    $pwd = $_POST["password"];
    
    //VALIDATE USER INPUT
    $errors = validate_login_input($email, $pwd);

    //VALIDATE LOGIN
    if(empty($errors)){
        $errors = validate_login($email, $pwd);
    }

    if(!empty($errors)){
        //GRAB ALL ERRORS AND THE PREVIOUS INPUT SAVE THEM IN SEPERATE SESSIONS
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;

        //REDIRECT TO VIEWS PAGE
        header('Location: ../views/auth/login.view.php');
        exit();
    }

    //LOGIN SUCCESSFUL, SAVE SESSION DATA AND REDIRECT TO BUILDERPAGE
    $user = get_user_by_email($email);

    $_SESSION['uid'] = $user['id'];
    $_SESSION['fname'] = $user['first_name'];
    $_SESSION['lname'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];

    header('Location: ../builder.php');
}

function signupuser(){

    session_start();
    $endpoint = "sign-up";

    //GET POST INPUTS
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $pwd = $_POST["password"];
    $cpwd = $_POST["confirm_password"];

    $errors = validate_register_input($fname, $lname, $email, $pwd, $cpwd);

    if(empty($errors)){
        if(!do_password_match($pwd, $cpwd)){
            $errors['Password Missmatch'] = 'Passwords do not match';
        }
    }

    if(!empty($errors)){
        //GRAB ALL ERRORS AND THE PREVIOUS INPUT SAVE THEM IN SEPERATE SESSIONS
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;

        //REDIRECT TO VIEWS PAGE
        header('Location: ../views/auth/login.view.php');
        exit();
    }

    //SIGNUP SUCCESSFUL, CREATE USER, SAVE SESSION DATA AND REDIRECT TO BUILDER PAGE
    create_new_user($fname, $lname, $email, $pwd);
    $user = get_user_by_email($email);

    $_SESSION['uid'] = $user['id'];
    $_SESSION['fname'] = $user['first_name'];
    $_SESSION['lname'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['login-time'] = time(); //TO IMPLEMENT LOGIN TIMEOUT. 

    header('Location: ../builder.php');
}
