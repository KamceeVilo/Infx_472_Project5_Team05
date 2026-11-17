<?php

require_once 'common.php';

// Unset all session variables
$_SESSION = [];

// Delete the session cookie if configured
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy session on server
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;

