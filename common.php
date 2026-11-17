<?php

<<<<<<< HEAD
// Start a PHP session if one has not already been started.
// Sessions allow us to remember that a user is logged in
// while they move between different pages.
=======
// Start a session if one has not already been started.
>>>>>>> 69f8e87 (Add shared session helpers and logout functionality)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


/*
<<<<<<< HEAD
 * Protect pages that require authentication.
 *
 * If there is no user_id stored in the session,
 * the user is not logged in and is redirected
 * to the login page.
=======
 * Redirect users to the login page if they
 * try to access a protected page without
 * being authenticated.
>>>>>>> 69f8e87 (Add shared session helpers and logout functionality)
 */
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}


/*
<<<<<<< HEAD
 * Prevent an already logged-in user from
 * returning to login.php or register.php.
 *
 * If a user already has an active session,
 * send them to the main newsfeed.
=======
 * If a user is already logged in,
 * prevent them from returning to the
 * login or registration pages.
>>>>>>> 69f8e87 (Add shared session helpers and logout functionality)
 */
function redirectIfLoggedIn(): void
{
    if (!empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}


/*
<<<<<<< HEAD
 * Safely display user-entered information in HTML.
 *
 * htmlspecialchars() prevents HTML entered by a user
 * from being interpreted as actual HTML code.
=======
 * Safely display user-entered information
 * inside HTML.
>>>>>>> 69f8e87 (Add shared session helpers and logout functionality)
 */
function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
<<<<<<< HEAD
}

=======
}
>>>>>>> 69f8e87 (Add shared session helpers and logout functionality)
