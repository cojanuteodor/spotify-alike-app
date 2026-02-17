<?php
include 'config.php';

$id_artist = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM Artisti_tbl WHERE id_artist = ?");
$stmt->execute([$id_artist]);
$artist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artist) {
    die("Artistul nu a fost găsit.");
}

// Preluăm albumele artistului
$stmtAlbums = $conn->prepare("SELECT * FROM Albume_tbl WHERE id_artist = ?");
$stmtAlbums->execute([$id_artist]);
$albums = $stmtAlbums->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($artist['nume']) ?> - Mini Spotify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #191414;
            color: white;
            min-height: 100vh;
        }
        .album-card {
            background-color: #282828;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .album-card:hover {
            background-color: #1ed760;
            color: black;
            transform: scale(1.05);
            cursor: pointer;
        }
        .descriere-box {
            background-color: #2e2e2e;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .artist-image {
            max-height: 300px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="container py-4">
    <a href="index.php" style="color: #1ed760;">&larr; Înapoi</a>
    <h1><?= htmlspecialchars($artist['nume']) ?></h1>

    <!-- Imaginea artistului -->
    <?php
        $imgPath = "poze/{$artist['id_artist']}.jpg";
        if (file_exists($imgPath)) {
            echo "<img src='$imgPath' alt='Poza artist' class='img-fluid artist-image'>";
        } else {
            echo "<p><em>Poza artistului nu este disponibilă.</em></p>";
        }
    ?>

    <p>Gen muzical: <?= htmlspecialchars($artist['gen_muzical']) ?></p>
    <p>Țara de origine: <?= htmlspecialchars($artist['tara_origine']) ?></p>

    <?php if (!empty($artist['descriere'])): ?>
        <div class="descriere-box">
            <h4>Despre artist:</h4>
            <p><?= nl2br(htmlspecialchars($artist['descriere'])) ?></p>
        </div>
    <?php endif; ?>

    <h2 class="mt-5">Albume</h2>
    <div class="row">
        <?php foreach($albums as $album): ?>
            <div class="col-md-4 album-card">
                <a href="album.php?id=<?= $album['id_album'] ?>">
                    <h4><?= htmlspecialchars($album['titlu']) ?></h4>
                    <small>An lansare: <?= htmlspecialchars($album['an_lansare']) ?></small>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
