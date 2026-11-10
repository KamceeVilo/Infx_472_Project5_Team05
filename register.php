<?php

// Load our shared session and helper functions.
require_once 'common.php';


// If the user is already logged in,
// they do not need to register again.
redirectIfLoggedIn();


// Array used to store any validation errors.
$errors = [];


// Variables are created so the form can remember
// what the user typed if validation fails.
$firstName = '';
$lastName = '';
$username = '';
$email = '';
$confirmEmail = '';


// Only process registration when the form is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
     * Get the submitted form values.
     *
     * trim() removes leading and trailing spaces,
     * which is required by the assignment.
     */
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $confirmEmail = trim($_POST['confirm_email'] ?? '');
    $password = trim($_POST['password'] ?? '');


    // ------------------------------
    // FIRST NAME VALIDATION
    // ------------------------------

    // First name must contain between 2 and 50 characters.
    if (
        strlen($firstName) < 2 ||
        strlen($firstName) > 50
    ) {
        $errors[] =
            'First name must be between 2 and 50 characters.';
    }


    // ------------------------------
    // LAST NAME VALIDATION
    // ------------------------------

    // Last name must contain between 2 and 50 characters.
    if (
        strlen($lastName) < 2 ||
        strlen($lastName) > 50
    ) {
        $errors[] =
            'Last name must be between 2 and 50 characters.';
    }


    // ------------------------------
    // USERNAME VALIDATION
    // ------------------------------

    // Username must contain between 2 and 50 characters.
    if (
        strlen($username) < 2 ||
        strlen($username) > 50
    ) {
        $errors[] =
            'Username must be between 2 and 50 characters.';
    }


    // ------------------------------
    // EMAIL VALIDATION
    // ------------------------------

    // Check that the email has a valid email format.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] =
            'Please enter a valid email address.';
    }

    // The assignment requires the email
    // to be entered twice for confirmation.
    if ($email !== $confirmEmail) {
        $errors[] =
            'Email addresses must match.';
    }


    // ------------------------------
    // PASSWORD VALIDATION
    // ------------------------------

    // Make sure a password was entered.
    if ($password === '') {
        $errors[] =
            'Password is required.';
    }


    /*
     * DATABASE REGISTRATION WILL GO HERE.
     *
     * Once Zach creates db.php and the users table,
     * we will:
     *
     * 1. Check that username is unique.
     * 2. Check that email is unique.
     * 3. Encrypt/hash the password.
     * 4. Randomly assign a profile picture.
     * 5. INSERT the new user into the database.
     * 6. Redirect the user to login.php.
     */
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Register</title>
</head>

<body>

    <main>

        <h1>Create Account</h1>


        <!-- Display validation errors if there are any -->
        <?php if (!empty($errors)): ?>

            <div>

                <?php foreach ($errors as $error): ?>

                    <p>
                        <?= e($error) ?>
                    </p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <!-- Registration Form -->
        <form method="POST">

            <!-- First Name -->
            <label for="first_name">
                First Name
            </label>

            <input type="text" id="first_name" name="first_name" value="<?= e($firstName) ?>" required>

            <br>


            <!-- Last Name -->
            <label for="last_name">
                Last Name
            </label>

            <input type="text" id="last_name" name="last_name" value="<?= e($lastName) ?>" required>

            <br>


            <!-- Username -->
            <label for="username">
                Username
            </label>

            <input type="text" id="username" name="username" value="<?= e($username) ?>" required>

            <br>


            <!-- Email -->
            <label for="email">
                Email
            </label>

            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <br>


            <!-- Confirm Email -->
            <label for="confirm_email">
                Confirm Email
            </label>

            <input type="email" id="confirm_email" name="confirm_email" value="<?= e($confirmEmail) ?>" required>

            <br>


            <!-- Password -->
            <!-- type="password" masks the characters entered -->
            <label for="password">
                Password
            </label>

            <input type="password" id="password" name="password" required>

            <br>


            <!-- Submit Registration -->
            <button type="submit">
                Register
            </button>

        </form>


        <!-- Link back to login page -->
        <p>
            Already have an account?

            <a href="login.php">
                Login
            </a>
        </p>

    </main>

</body>

</html>