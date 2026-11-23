<?php

require_once 'common.php';
require_once 'db.php';

requireLogin();

$currentUserId = (int) $_SESSION['user_id'];


/*
 * LIVE SEARCH REQUEST
 *
 * JavaScript sends requests to this same file using:
 * people.php?ajax=1&search=kam
 *
 * This section returns matching users as JSON.
 */
if (isset($_GET['ajax'])) {

    $search = trim($_GET['search'] ?? '');

    // Do not return anybody if the search box is empty.
    if ($search === '') {

        header('Content-Type: application/json');

        echo json_encode([]);

        exit;
    }


    $stmt = $pdo->prepare(
        'SELECT id, name, username
         FROM users
         WHERE id != :current_user
         AND (
            name LIKE :name_search
            OR username LIKE :username_search
         )
         ORDER BY name ASC
         LIMIT 10'
    );

    $searchValue = '%' . $search . '%';

    $stmt->execute([
        ':current_user' => $currentUserId,
        ':name_search' => $searchValue,
        ':username_search' => $searchValue
    ]);

    $users = $stmt->fetchAll();

    header('Content-Type: application/json');

    echo json_encode($users);

    exit;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find People</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="scanlines"></div>

    <div class="container">


        <!-- Return to Home -->
        <div class="page-return">

            <a href="index.php" class="back-home">
                &larr; Home
            </a>

        </div>


        <header class="terminal-nav">

            <div class="nav-brand">

                <h2>
                    &gt; SOCIAL_NET::PEOPLE
                </h2>

            </div>

        </header>


        <main>

            <section class="card">

                <h1>
                    [ Find People ]
                </h1>


                <!--
                    Search field.
                    No submit button is needed because
                    JavaScript searches while the user types.
                -->
                <input type="text" id="people-search" placeholder="Search by name or username..." autocomplete="off">

            </section>


            <!-- Search results appear here -->
            <section class="card" id="results-section" style="display: none;">

                <h2>
                    [ Search Results ]
                </h2>

                <div id="search-results"></div>

            </section>

        </main>

    </div>


    <script>

        const searchInput = document.getElementById('people-search');

        const resultsSection =
            document.getElementById('results-section');

        const resultsContainer =
            document.getElementById('search-results');


        /*
         * Run whenever the user types in the search box.
         */
        searchInput.addEventListener('input', function () {

            const search = searchInput.value.trim();


            /*
             * Empty search = show no users.
             */
            if (search === '') {

                resultsSection.style.display = 'none';

                resultsContainer.innerHTML = '';

                return;
            }


            /*
             * Ask people.php for matching users.
             */
            fetch(
                'people.php?ajax=1&search='
                + encodeURIComponent(search)
            )

                .then(response => response.json())

                .then(users => {

                    resultsContainer.innerHTML = '';


                    /*
                     * Nothing matched the search.
                     */
                    if (users.length === 0) {

                        resultsSection.style.display = 'block';

                        resultsContainer.innerHTML =
                            '<p>&gt; No users found.</p>';

                        return;
                    }


                    /*
                     * Display matching users.
                     */
                    users.forEach(user => {

                        const userCard =
                            document.createElement('div');

                        userCard.className = 'card';


                        const name =
                            document.createElement('strong');

                        name.textContent = user.name;


                        const username =
                            document.createElement('p');

                        username.textContent =
                            '@' + user.username;


                        const profileLink =
                            document.createElement('a');

                        profileLink.href =
                            'profile.php?id=' + user.id;

                        profileLink.textContent =
                            'View Profile';


                        userCard.appendChild(name);

                        userCard.appendChild(username);

                        userCard.appendChild(profileLink);


                        resultsContainer.appendChild(
                            userCard
                        );

                    });


                    resultsSection.style.display = 'block';

                })

                .catch(error => {

                    console.error(
                        'Search error:',
                        error
                    );

                });

        });

    </script>

</body>

</html>