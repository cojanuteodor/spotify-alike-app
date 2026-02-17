<?php
session_start();
include 'config.php';

// Funcție creare playlist (dacă s-a trimis formularul)
if (!empty($_SESSION['user_id']) && isset($_POST['create_playlist'])) {
    $userId = $_SESSION['user_id'];
    $title = trim($_POST['new_playlist_title']);

    if (!empty($title)) {
        $stmtInsert = $conn->prepare("INSERT INTO Playlisturi_tbl (id_utilizator, titlu, data_creare) VALUES (?, ?, CURDATE())");
        $stmtInsert->execute([$userId, $title]);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Căutare (dacă s-a trimis un termen)
$searchResults = [
    'artists' => [],
    'songs' => [],
    'users' => [],
    'albums' => [],
];

$searchTerm = $_GET['q'] ?? '';
if (!empty($searchTerm)) {
    $term = '%' . $searchTerm . '%';

    $stmtArtists = $conn->prepare("SELECT id_artist, nume FROM Artisti_tbl WHERE nume LIKE ?");
    $stmtArtists->execute([$term]);
    $searchResults['artists'] = $stmtArtists->fetchAll(PDO::FETCH_ASSOC);

    $stmtSongs = $conn->prepare("SELECT id_piesa, titlu FROM Piese_tbl WHERE titlu LIKE ?");
    $stmtSongs->execute([$term]);
    $searchResults['songs'] = $stmtSongs->fetchAll(PDO::FETCH_ASSOC);

    $stmtUsers = $conn->prepare("SELECT id_utilizator, nume_utilizator FROM Utilizatori_tbl WHERE nume_utilizator LIKE ?");
    $stmtUsers->execute([$term]);
    $searchResults['users'] = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    $stmtAlbums = $conn->prepare("SELECT id_album, titlu FROM Albume_tbl WHERE titlu LIKE ?");
    $stmtAlbums->execute([$term]);
    $searchResults['albums'] = $stmtAlbums->fetchAll(PDO::FETCH_ASSOC);
}

// Preluăm artiști
$stmtArtistsAll = $conn->query("SELECT * FROM Artisti_tbl LIMIT 10");
$artists = $stmtArtistsAll->fetchAll(PDO::FETCH_ASSOC);

// Preluăm playlisturi recomandate (toate)
$stmtPlaylists = $conn->query("SELECT Playlisturi_tbl.*, Utilizatori_tbl.nume_utilizator FROM Playlisturi_tbl JOIN Utilizatori_tbl ON Playlisturi_tbl.id_utilizator = Utilizatori_tbl.id_utilizator LIMIT 10");
$playlists = $stmtPlaylists->fetchAll(PDO::FETCH_ASSOC);

// Dacă ești logat, preluăm și playlisturile tale personale
$myPlaylists = [];
if (!empty($_SESSION['user_id'])) {
    $stmtMyPlaylists = $conn->prepare("SELECT * FROM Playlisturi_tbl WHERE id_utilizator = ? ORDER BY data_creare DESC");
    $stmtMyPlaylists->execute([$_SESSION['user_id']]);
    $myPlaylists = $stmtMyPlaylists->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8" />
    <title>Mini Spotify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="logo.jpg" />

    <style>
        body {
            background: linear-gradient(135deg, #1DB954 0%, #191414 100%);
            color: white;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .logo-container {
            background-color: black;
            max-width: 400px;
            margin: 20px auto 10px auto;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }
        .logo-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .login-bar {
            max-width: 400px;
            margin: 10px auto 20px auto;
            background-color: rgba(0,0,0,0.5);
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            color: #1ed760;
        }
        .login-bar a {
            color: #1ed760;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .login-bar a:hover {
            color: #12b347;
            text-decoration: underline;
        }
        form.search-form {
            max-width: 600px;
            margin: 0 auto 30px auto;
        }
        form.search-form input[type="text"] {
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1.2rem;
            border: none;
            width: 100%;
        }
        form.search-form input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 8px #1ed760;
        }
        .artist-card, .playlist-card {
            background-color: #282828;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .artist-card:hover, .playlist-card:hover {
            background-color: #1ed760;
            color: black;
            transform: scale(1.05);
            cursor: pointer;
        }
        a {
            color: inherit;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .search-section ul {
            padding-left: 0;
            list-style: none;
        }
    </style>
</head>
<body>

    <!-- Logo sus -->
    <div class="logo-container">
        <img src="logo.jpg" alt="Mini Spotify Logo" />
    </div>

    <!-- Bara login / profil -->
    <div class="login-bar">
        <?php if (!empty($_SESSION['user_id']) && !empty($_SESSION['username'])): ?>
            <span>Bun venit, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span> |
            <a href="logout.php">Deconectare</a>
        <?php else: ?>
            <a href="login.php">Intră în cont</a>
        <?php endif; ?>
    </div>

    <!-- Bara cautare -->
    <form method="GET" class="search-form">
        <input type="text" name="q" placeholder="Caută piese, artiști, utilizatori sau albume..." value="<?= htmlspecialchars($searchTerm) ?>">
    </form>

    <?php if (!empty($searchTerm)): ?>
        <section class="mb-5 search-section container">
            <h2>Rezultate căutare pentru "<?= htmlspecialchars($searchTerm) ?>"</h2>

            <h3>Artiști</h3>
            <?php if (count($searchResults['artists']) > 0): ?>
                <ul>
                    <?php foreach ($searchResults['artists'] as $artist): ?>
                        <li>
                            <a href="artist.php?id=<?= $artist['id_artist'] ?>"><?= htmlspecialchars($artist['nume']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Nu s-au găsit artiști.</em></p>
            <?php endif; ?>

            <h3>Piese</h3>
            <?php if (count($searchResults['songs']) > 0): ?>
                <ul>
                    <?php foreach ($searchResults['songs'] as $song): ?>
                        <li><?= htmlspecialchars($song['titlu']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Nu s-au găsit piese.</em></p>
            <?php endif; ?>

            <h3>Utilizatori</h3>
            <?php if (count($searchResults['users']) > 0): ?>
                <ul>
                    <?php foreach ($searchResults['users'] as $user): ?>
                        <li>
                            <a href="user.php?id=<?= $user['id_utilizator'] ?>"><?= htmlspecialchars($user['nume_utilizator']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Nu s-au găsit utilizatori.</em></p>
            <?php endif; ?>

            <h3>Albume</h3>
            <?php if (count($searchResults['albums']) > 0): ?>
                <ul>
                    <?php foreach ($searchResults['albums'] as $album): ?>
                        <li>
                            <a href="album.php?id=<?= $album['id_album'] ?>"><?= htmlspecialchars($album['titlu']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Nu s-au găsit albume.</em></p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="container">
        <h2>Artiști Populari</h2>
        <div class="row">
            <?php foreach ($artists as $artist): ?>
                <div class="col-md-4 artist-card">
                    <a href="artist.php?id=<?= $artist['id_artist'] ?>">
                        <h3><?= htmlspecialchars($artist['nume']) ?></h3>
                        <p>Gen: <?= htmlspecialchars($artist['gen_muzical']) ?></p>
                        <p>Țara: <?= htmlspecialchars($artist['tara_origine']) ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container mt-5">
        <h2>Playlisturi Recomandate</h2>
        <div class="row">
            <?php foreach ($playlists as $playlist): ?>
                <div class="col-md-4 playlist-card">
                    <a href="playlist.php?id=<?= $playlist['id_playlist'] ?>">
                        <h4><?= htmlspecialchars($playlist['titlu']) ?></h4>
                        <p>De la: <?= htmlspecialchars($playlist['nume_utilizator']) ?></p>
                        <small>Creat: <?= htmlspecialchars($playlist['data_creare']) ?></small>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (!empty($_SESSION['user_id'])): ?>
    <section class="container mt-5">
        <h2>Playlisturile Mele</h2>

        <!-- Formular creare playlist nou -->
        <form method="POST" action="" class="mb-3">
            <div class="input-group">
                <input type="text" name="new_playlist_title" class="form-control" placeholder="Titlul noului playlist" required>
                <button type="submit" name="create_playlist" class="btn btn-success">Creează Playlist</button>
            </div>
        </form>

        <?php if (count($myPlaylists) === 0): ?>
            <p>Nu ai niciun playlist creat încă.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($myPlaylists as $pl): ?>
                    <li class="list-group-item bg-dark text-white">
                        <a href="playlist.php?id=<?= $pl['id_playlist'] ?>" style="color:#1ed760;"><?= htmlspecialchars($pl['titlu']) ?></a> 
                        <small>(Creat pe <?= htmlspecialchars($pl['data_creare']) ?>)</small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php endif; ?>

</body>
</html>
