<?php

require_once('user.model.php');


function is_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


function validate_login_input($email, $password): array
{
    $errors = [];

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if(empty($errors)){
        if(!is_valid_email($email)){
            $errors['email'] = 'Enter a valid email';
        }
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    return $errors;
}

function validate_register_input($fname, $lname, $email, $pwd, $cpwd): array
{
    $errors = [];

    if (empty($fname)) {
        $errors['first_name'] = 'Please enter your first name';
    }

    if (empty($lname)) {
        $errors['last_name'] = 'Please enter your last name';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address';
    }

    if (empty($pwd)) {
        $errors['password'] = 'Please enter a password';
    } elseif (strlen($pwd) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    if (empty($cpwd)) {
        $errors['confirm_password'] = 'Please retype your password';
    } elseif ($pwd !== $cpwd) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    return $errors;
}



function do_password_match(string $pwd, string $cpwd): bool
{
    return $pwd === $cpwd;
}


function email_exists($email)
{

    if (get_user_by_email($email)) {
        return 1;
    }
}


function validate_login($email, $pwd): array {

    $errors = [];

    //GET USER FROM DATABASE THAT MATCHES THE EMAIL 
    $user = get_user_by_email($email);

    //CHECK IF USER EXISTS IN DB
    if(empty($user)){
        $errors['Invalid Credentials'] = 'Invalid login credentials';
    }

    //CHECK IF PASSWORDS MATCH
    if(empty($errors) && !password_verify($pwd, $user['password'])){
        $errors['Wrong password'] = 'Wrong Password';
    }

    return $errors;
}

