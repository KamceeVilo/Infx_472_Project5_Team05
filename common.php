<?php

// Start a PHP session if one has not already been started.
// Sessions allow us to remember that a user is logged in
// while they move between different pages.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


/*
 * Protect pages that require authentication.
 *
 * If there is no user_id stored in the session,
 * the user is not logged in and is redirected
 * to the login page.
 */
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}


/*
 * Prevent an already logged-in user from
 * returning to login.php or register.php.
 *
 * If a user already has an active session,
 * send them to the main newsfeed.
 */
function redirectIfLoggedIn(): void
{
    if (!empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}


/*
 * Safely display user-entered information in HTML.
 *
 * htmlspecialchars() prevents HTML entered by a user
 * from being interpreted as actual HTML code.
 */
function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

