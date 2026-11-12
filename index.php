<?php

// Load shared session, authentication helpers, and database connection.
require_once 'common.php';
require_once 'db.php';

// Ensure the user is authenticated. If not, redirect to login.php.
requireLogin();

$userId = (int) $_SESSION['user_id'];

// Fetch the authenticated user's profile details.
$userStmt = $pdo->prepare('SELECT id, name, username, email, created_at FROM users WHERE id = :id LIMIT 1');
$userStmt->execute([':id' => $userId]);
$currentUser = $userStmt->fetch();

// If user does not exist in the database (e.g., account deleted), clear session and redirect.
if (!$currentUser) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

$errorMessage = '';
$successMessage = '';

// Handle creating a new post on the user's account.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_post') {
    $content = trim($_POST['content'] ?? '');

    if ($content === '') {
        $errorMessage = 'Post content cannot be empty.';
    } else {
        $insertStmt = $pdo->prepare('
            INSERT INTO posts (user_id, page_id, content)
            VALUES (:user_id, :page_id, :content)
        ');
        $insertStmt->execute([
            ':user_id' => $userId,
            ':page_id' => $userId,
            ':content' => $content,
        ]);

        header('Location: index.php?posted=1');
        exit;
    }
}

if (isset($_GET['posted']) && $_GET['posted'] === '1') {
    $successMessage = 'Post published successfully!';
}

// Fetch all posts for this user's account (authored by them or on their page).
$postsStmt = $pdo->prepare('
    SELECT p.id, p.content, p.created_at, u.name, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.page_id = :page_uid OR p.user_id = :author_uid
    ORDER BY p.created_at DESC
');
$postsStmt->execute([
    ':page_uid'   => $userId,
    ':author_uid' => $userId,
]);
$userPosts = $postsStmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($currentUser['name']) ?> - Feed</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="scanlines"></div>

    <div class="container">
        <!-- Navigation Header -->
        <header class="terminal-nav">
            <div class="nav-brand">
                <h2>&gt; SOCIAL_NET::FEED</h2>
            </div>
            <nav class="nav-links">
                <span class="user-badge">User: <strong>@<?= e($currentUser['username']) ?></strong></span>
                <a href="index.php" class="nav-link">[ Feed ]</a>
                <a href="logout.php" class="nav-link">[ Logout ]</a>
            </nav>
        </header>

        <main>
            <!-- User Profile Summary Card -->
            <section class="card profile-card">
                <h3>[ Account Information ]</h3>
                <p><strong>Name:</strong> <?= e($currentUser['name']) ?></p>
                <p><strong>Username:</strong> @<?= e($currentUser['username']) ?></p>
                <p><strong>Email:</strong> <?= e($currentUser['email']) ?></p>
                <p><strong>Total Posts:</strong> <?= count($userPosts) ?></p>
                <p><strong>Member Since:</strong> <?= e(date('F j, Y', strtotime($currentUser['created_at']))) ?></p>
            </section>

            <!-- Create Post Form -->
            <section class="card create-post-card">
                <h3>[ Create a New Post ]</h3>

                <?php if ($errorMessage !== ''): ?>
                    <div class="alert alert-error">
                        <p>&gt; Error: <?= e($errorMessage) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage !== ''): ?>
                    <div class="alert alert-success">
                        <p>&gt; Success: <?= e($successMessage) ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="create_post">
                    <div class="form-group">
                        <label for="content">What would you like to share?</label>
                        <textarea id="content" name="content" rows="4" placeholder="Type your post here..." required></textarea>
                    </div>
                    <button type="submit" class="btn">[ Publish Post ]</button>
                </form>
            </section>

            <!-- User's Posts Feed -->
            <section class="card posts-feed-card">
                <h3>[ Your Posts Feed ]</h3>

                <?php if (empty($userPosts)): ?>
                    <div class="no-posts">
                        <p>&gt; No posts found for your account yet.</p>
                        <p>Write something above to create your first post!</p>
                    </div>
                <?php else: ?>
                    <div class="posts-list">
                        <?php foreach ($userPosts as $post): ?>
                            <article class="post-item">
                                <div class="post-header">
                                    <span class="post-author"><?= e($post['name']) ?> (@<?= e($post['username']) ?>)</span>
                                    <time class="post-date"><?= e(date('M j, Y \a\t g:i A', strtotime($post['created_at']))) ?></time>
                                </div>
                                <div class="post-content">
                                    <?= nl2br(e($post['content'])) ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>

