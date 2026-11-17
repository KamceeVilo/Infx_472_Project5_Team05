<?php

require_once 'common.php';
require_once 'db.php';

redirectIfLoggedIn();

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both your username and password.';
    } else {
        $stmt = $pdo->prepare('
            SELECT id, name, username, email, password_hash
            FROM users
            WHERE username = :login_user OR email = :login_email
            LIMIT 1
        ');
        $stmt->execute([
            ':login_user'  => $username,
            ':login_email' => $username,
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Social Media</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="scanlines"></div>

    <div class="container auth-container">
        <header class="terminal-nav">
            <div class="nav-brand">
                <h2>&gt; SYSTEM_LOGIN</h2>
            </div>
            <nav class="nav-links">
                <a href="register.php" class="nav-link">[ Register ]</a>
            </nav>
        </header>

        <main>
            <section class="card auth-card">
                <h1>Authenticate</h1>

                <?php if (isset($_GET['registered']) && $_GET['registered'] === '1'): ?>
                    <div class="alert alert-success">
                        <p>&gt; Account created successfully! Please log in.</p>
                    </div>
                <?php endif; ?>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-error">
                        <p>&gt; Error: <?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input type="text" id="username" name="username" value="<?= e($username) ?>" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn">[ Log In ]</button>
                </form>

                <p class="auth-switch">
                    Need an account? <a href="register.php">Register here</a>
                </p>
            </section>
        </main>
    </div>
</body>
</html>

