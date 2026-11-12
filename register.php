<?php

// Load our shared session and helper functions.
require_once 'common.php';
require_once 'db.php';

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
    if (strlen($firstName) < 2 || strlen($firstName) > 50) {
        $errors[] = 'First name must be between 2 and 50 characters.';
    }

    // ------------------------------
    // LAST NAME VALIDATION
    // ------------------------------
    if (strlen($lastName) < 2 || strlen($lastName) > 50) {
        $errors[] = 'Last name must be between 2 and 50 characters.';
    }

    // ------------------------------
    // USERNAME VALIDATION
    // ------------------------------
    if (strlen($username) < 2 || strlen($username) > 50) {
        $errors[] = 'Username must be between 2 and 50 characters.';
    }

    // ------------------------------
    // EMAIL VALIDATION
    // ------------------------------
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($email !== $confirmEmail) {
        $errors[] = 'Email addresses must match.';
    }

    // ------------------------------
    // PASSWORD VALIDATION
    // ------------------------------
    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    // ------------------------------
    // DATABASE REGISTRATION
    // ------------------------------
    if (empty($errors)) {
        // 1. Check that username and email are unique.
        $checkStmt = $pdo->prepare('
            SELECT username, email 
            FROM users 
            WHERE username = :username OR email = :email 
            LIMIT 1
        ');
        $checkStmt->execute([
            ':username' => $username,
            ':email' => $email,
        ]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            if (strcasecmp($existing['username'], $username) === 0) {
                $errors[] = 'Username is already taken.';
            }
            if (strcasecmp($existing['email'], $email) === 0) {
                $errors[] = 'An account with that email already exists.';
            }
        } else {
            // 2. Hash the password.
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $fullName = $firstName . ' ' . $lastName;

            // 3. Insert new user into database.
            $insertStmt = $pdo->prepare('
                INSERT INTO users (name, username, email, password_hash)
                VALUES (:name, :username, :email, :password_hash)
            ');
            $insertStmt->execute([
                ':name' => $fullName,
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $passwordHash,
            ]);

            // 4. Redirect to login.php with success flag.
            header('Location: login.php?registered=1');
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="scanlines"></div>

    <div class="container auth-container">
        <header class="terminal-nav">
            <div class="nav-brand">
                <h2>&gt; CREATE_USER</h2>
            </div>
            <nav class="nav-links">
                <a href="login.php" class="nav-link">[ Login ]</a>
            </nav>
        </header>

        <main>
            <section class="card auth-card">
                <h1>Create Account</h1>

                <!-- Display validation errors if there are any -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p>&gt; <?= e($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form method="POST" action="register.php">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?= e($firstName) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?= e($lastName) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?= e($username) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_email">Confirm Email</label>
                        <input type="email" id="confirm_email" name="confirm_email" value="<?= e($confirmEmail) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn">[ Register ]</button>
                </form>

                <p class="auth-switch">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </section>
        </main>
    </div>
</body>
</html>
