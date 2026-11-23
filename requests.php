<?php

// Load authentication/session helpers.
require_once 'common.php';

// Load database connection.
require_once 'db.php';

// Friend requests can only be viewed
// by an authenticated user.
requireLogin();

$currentUserId = (int) $_SESSION['user_id'];

$message = '';
$error = '';


// --------------------------------------------------
// HANDLE ACCEPT / DECLINE ACTIONS
// --------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the friendship/request ID.
    $requestId = (int) ($_POST['request_id'] ?? 0);

    // Get the requested action.
    $action = $_POST['action'] ?? '';


    // Make sure the request belongs to
    // the currently logged-in user.
    $requestStmt = $pdo->prepare(
        'SELECT id, requester_id, addressee_id, status
         FROM friendships
         WHERE id = :id
         AND addressee_id = :user_id
         AND status = "pending"
         LIMIT 1'
    );

    $requestStmt->execute([
        ':id' => $requestId,
        ':user_id' => $currentUserId
    ]);

    $friendRequest = $requestStmt->fetch();


    if (!$friendRequest) {

        $error = 'Friend request not found.';

    } elseif ($action === 'accept') {

        // Accepting changes the existing
        // pending relationship to accepted.
        $acceptStmt = $pdo->prepare(
            'UPDATE friendships
             SET status = "accepted"
             WHERE id = :id'
        );

        $acceptStmt->execute([
            ':id' => $requestId
        ]);

        $message = 'Friend request accepted.';


    } elseif ($action === 'decline') {

        // A declined request is removed
        // from the database.
        $declineStmt = $pdo->prepare(
            'DELETE FROM friendships
             WHERE id = :id'
        );

        $declineStmt->execute([
            ':id' => $requestId
        ]);

        $message = 'Friend request declined.';
    }
}


// --------------------------------------------------
// LOAD PENDING FRIEND REQUESTS
// --------------------------------------------------

/*
 * Only retrieve requests where:
 *
 * - current user is the recipient
 * - request is still pending
 *
 * Join users so we can display the sender's
 * name and username.
 */
$requestsStmt = $pdo->prepare(
    'SELECT
        f.id,
        f.requester_id,
        f.created_at,
        u.name,
        u.username
     FROM friendships f
     JOIN users u
        ON f.requester_id = u.id
     WHERE f.addressee_id = :user_id
     AND f.status = "pending"
     ORDER BY f.created_at DESC'
);

$requestsStmt->execute([
    ':user_id' => $currentUserId
]);

$friendRequests = $requestsStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Friend Requests</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="scanlines"></div>

    <div class="container">
        <a href="index.php" class="back-home">
            &larr; Home
        </a>
        <!-- Temporary navigation until
             the shared header.php is finished. -->
        <header class="terminal-nav">

            <div class="nav-brand">

                <h2>
                    &gt; SOCIAL_NET::REQUESTS
                </h2>

            </div>


            <nav class="nav-links">

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

            <section class="card">

                <h1>
                    [ Friend Requests ]
                </h1>


                <!-- Successful action message -->
                <?php if ($message !== ''): ?>

                    <p>
                        <?= e($message) ?>
                    </p>

                <?php endif; ?>


                <!-- Error message -->
                <?php if ($error !== ''): ?>

                    <p>
                        <?= e($error) ?>
                    </p>

                <?php endif; ?>


                <?php if (empty($friendRequests)): ?>

                    <p>
                        &gt; No pending friend requests.
                    </p>


                <?php else: ?>

                    <?php foreach ($friendRequests as $request): ?>

                        <div class="card">

                            <p>

                                <strong>
                                    <?= e($request['name']) ?>
                                </strong>

                                <br>

                                @
                                <?= e($request['username']) ?>

                            </p>


                            <!-- Link to sender's profile -->
                            <p>

                                <a href="profile.php?id=<?= (int) $request['requester_id'] ?>">
                                    View Profile
                                </a>

                            </p>


                            <!-- Accept / Decline -->
                            <form method="POST">

                                <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">


                                <button type="submit" name="action" value="accept">
                                    Accept
                                </button>


                                <button type="submit" name="action" value="decline">
                                    Decline
                                </button>

                            </form>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </section>

        </main>

    </div>

</body>

</html>