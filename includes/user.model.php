<?php

require_once('db.php');



function create_new_user($fname, $lname, $email, $pwd){
    global $conn;
    $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (first_name, last_name, email, password) 
         VALUES (:first_name, :last_name, :email, :password)"
    );

    $stmt->execute ([
        'first_name' => $fname,
        'last_name' => $lname,
        'email' => $email,
        'password' => $hashedPassword

    ]);

}

function get_user_by_email($email) : ? array {
    global $conn;
    
    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE email = :email LIMIT 1"
    );

    $stmt->execute([
        'email' => $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}








?>