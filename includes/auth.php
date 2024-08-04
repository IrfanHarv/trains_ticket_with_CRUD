<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    // else {
    //     $_SESSION['message'] = 'Login is Successful!';
    // }
}
?>
