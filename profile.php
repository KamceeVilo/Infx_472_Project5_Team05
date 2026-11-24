<?php

// Load the database connection.
require_once 'db.php';

// Load the shared session and authentication helpers.
require_once 'common.php';


// --------------------------------------------------
// REQUIRE AUTHENTICATION
// --------------------------------------------------

// Users must be logged in before they can view profiles.
requireLogin();


// --------------------------------------------------
// DETERMINE WHICH PROFILE TO DISPLAY
// --------------------------------------------------

/*
 * If the URL contains an id:
 *
 * profile.php?id=3
 *
 * then display user #3's profile.
 *
 * If no id is provided, display the currently
 * logged-in user's own profile.
 */
$profileUserId = isset($_GET['id'])
    ? (int) $_GET['id']
    : (int) $_SESSION['user_id'];


// --------------------------------------------------
// LOAD THE PROFILE USER
// --------------------------------------------------

$userStmt = $pdo->prepare(
    'SELECT id, name, username, email, created_at
     FROM users
     WHERE id = :id
     LIMIT 1'
);

$userStmt->execute([
    ':id' => $profileUserId
]);

$profileUser = $userStmt->fetch();

// --------------------------------------------------
// HANDLE FRIEND REQUEST
// --------------------------------------------------

$friendMessage = '';

/*
 * Only allow a friend request when:
 *
 * - a user is logged in
 * - they are viewing someone else's profile
 * - the form was submitted using POST
 */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $profileUserId !== (int) $_SESSION['user_id']
) {

    $currentUserId = (int) $_SESSION['user_id'];


    // Check whether a relationship already exists
    // between these two users in either direction.
    $checkFriendStmt = $pdo->prepare(
        'SELECT id, status
         FROM friendships
         WHERE
            (
                requester_id = :current_user
                AND addressee_id = :profile_user
            )
            OR
            (
                requester_id = :profile_user
                AND addressee_id = :current_user
            )
         LIMIT 1'
    );

    $checkFriendStmt->execute([
        ':current_user' => $currentUserId,
        ':profile_user' => $profileUserId
    ]);

    $existingFriendship = $checkFriendStmt->fetch();


    if ($existingFriendship) {

        if ($existingFriendship['status'] === 'accepted') {

            $friendMessage =
                'You are already friends with this user.';

        } else {

            $friendMessage =
                'A friend request is already pending.';
        }

    } else {

        // No existing relationship exists,
        // so create a new pending friend request.
        $sendRequestStmt = $pdo->prepare(
            'INSERT INTO friendships
                (requester_id, addressee_id, status)
             VALUES
                (:requester_id, :addressee_id, "pending")'
        );

        $sendRequestStmt->execute([
            ':requester_id' => $currentUserId,
            ':addressee_id' => $profileUserId
        ]);

        $friendMessage =
            'Friend request sent.';
    }
}

// If the requested user does not exist,
// send the visitor back to the newsfeed.
if (!$profileUser) {
    header('Location: index.php');
    exit;
}


// --------------------------------------------------
// COUNT POSTS
// --------------------------------------------------

/*
 * Count the number of posts actually written
 * by this user.
 */
$postCountStmt = $pdo->prepare(
    'SELECT COUNT(*)
     FROM posts
     WHERE user_id = :user_id'
);

$postCountStmt->execute([
    ':user_id' => $profileUserId
]);

$postCount = (int) $postCountStmt->fetchColumn();


// --------------------------------------------------
// COUNT LIKES RECEIVED
// --------------------------------------------------

/*
 * Count the total number of likes received
 * across all posts written by this user.
 */
$likeCountStmt = $pdo->prepare(
    'SELECT COUNT(*)
     FROM likes l
     JOIN posts p
        ON l.post_id = p.id
     WHERE p.user_id = :user_id'
);

$likeCountStmt->execute([
    ':user_id' => $profileUserId
]);

$likeCount = (int) $likeCountStmt->fetchColumn();


// --------------------------------------------------
// FRIEND COUNT
// --------------------------------------------------

// --------------------------------------------------
// COUNT FRIENDS
// --------------------------------------------------

/*
 * A friendship counts when its status is accepted
 * and the profile user appears on either side
 * of the relationship.
 */
$friendCountStmt = $pdo->prepare(
    'SELECT COUNT(*)
     FROM friendships
     WHERE status = "accepted"
     AND (
        requester_id = :requester_id
        OR addressee_id = :addressee_id
     )'
);

$friendCountStmt->execute([
    ':requester_id' => $profileUserId,
    ':addressee_id' => $profileUserId
]);
$friendCount = (int) $friendCountStmt->fetchColumn();


// --------------------------------------------------
// LOAD THIS USER'S POSTS
// --------------------------------------------------

/*
 * Retrieve every post written by this user.
 *
 * Newest posts appear first.
 */
$postsStmt = $pdo->prepare(
    'SELECT id, content, created_at
     FROM posts
     WHERE user_id = :user_id
     ORDER BY created_at DESC'
);

$postsStmt->execute([
    ':user_id' => $profileUserId
]);

$userPosts = $postsStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= e($profileUser['name']) ?> - Profile
    </title>

    <!-- Use the same stylesheet as the current newsfeed -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <!-- Existing visual effect used by index.php -->
    <div class="scanlines"></div>


    <div class="container">
        <a href="index.php" class="back-home">
            &larr; Home
        </a>

        <!-- ==========================================
             PROFILE NAVIGATION
             ========================================== -->

        <header class="terminal-nav">

            <div class="nav-brand">

                <h2>
                    &gt; SOCIAL_NET::PROFILE
                </h2>

            </div>


            <nav class="nav-links">

                <span class="user-badge">

                    Viewing:
                    <strong>
                        @<?= e($profileUser['username']) ?>
                    </strong>

                </span>


                <a href="index.php" class="nav-link">
                    [ Feed ]
                </a>


                <a href="profile.php" class="nav-link">
                    [ My Profile ]
                </a>


                <a href="logout.php" class="nav-link">
                    [ Logout ]
                </a>

            </nav>

        </header>


        <main>

            <!-- ======================================
                 PROFILE INFORMATION
                 ====================================== -->

            <section class="card profile-card">

                <h3>
                    [ User Profile ]
                </h3>


                <p>
                    <strong>Name:</strong>

                    <?= e($profileUser['name']) ?>
                </p>


                <p>
                    <strong>Username:</strong>

                    @<?= e($profileUser['username']) ?>
                </p>


                <p>
                    <strong>Email:</strong>

                    <?= e($profileUser['email']) ?>
                </p>


                <p>
                    <strong>Member Since:</strong>

                    <?= e(
                        date(
                            'F j, Y',
                            strtotime(
                                $profileUser['created_at']
                            )
                        )
                    ) ?>
                </p>
                <?php if (
                    $profileUserId !== (int) $_SESSION['user_id']
                ): ?>

                    <form method="POST">

                        <button type="submit" class="btn-add-friend">
                            + Add Friend
                        </button>

                    </form>

                <?php endif; ?>
                <?php if (
                    $profileUserId !== (int) $_SESSION['user_id']
                ): ?>

                    <form method="POST">

                        <button type="submit">
                            Add Friend
                        </button>

                    </form>

                <?php endif; ?>
                <?php if ($friendMessage !== ''): ?>

                    <p>
                        <?= e($friendMessage) ?>
                    </p>

                <?php endif; ?>
            </section>


            <!-- ======================================
                 PROFILE STATISTICS
                 ====================================== -->

            <section class="card">

                <h3>
                    [ Profile Statistics ]
                </h3>


                <p>
                    <strong>Posts:</strong>

                    <?= $postCount ?>
                </p>


                <p>
                    <strong>Likes:</strong>

                    <?= $likeCount ?>
                </p>


                <p>
                    <strong>Friends:</strong>

                    <?= $friendCount ?>
                </p>

            </section>


            <!-- ======================================
                 USER POSTS
                 ====================================== -->

            <section class="card posts-feed-card">

                <h3>
                    [ <?= e($profileUser['name']) ?>'s Posts ]
                </h3>


                <?php if (empty($userPosts)): ?>

                    <!-- Display when the user has no posts -->

                    <div class="no-posts">

                        <p>
                            &gt; This user has not posted anything yet.
                        </p>

                    </div>


                <?php else: ?>

                    <div class="posts-list">


                        <?php foreach ($userPosts as $post): ?>

                            <article class="post-item">

                                <!-- Post information -->
                                <div class="post-header">

                                    <span class="post-author">

                                        <?= e($profileUser['name']) ?>

                                        (@<?= e($profileUser['username']) ?>)

                                    </span>


                                    <time class="post-date">

                                        <?= e(
                                            date(
                                                'M j, Y \a\t g:i A',
                                                strtotime(
                                                    $post['created_at']
                                                )
                                            )
                                        ) ?>

                                    </time>

                                </div>


                                <!-- Actual post text -->
                                <div class="post-content">

                                    <?= nl2br(
                                        e($post['content'])
                                    ) ?>

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