<?php
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600, // Changed to use 3600 seconds for clarity
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
unset($_SESSION['login']);
session_destroy(); // destroy session
header("location:./index.php");
