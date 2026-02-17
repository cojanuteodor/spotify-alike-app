<?php
include 'config.php';

$id_album = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Preluăm albumul și artistul
$stmt = $conn->prepare("
    SELECT Albume_tbl.*, Artisti_tbl.nume AS nume_artist 
    FROM Albume_tbl 
    JOIN Artisti_tbl ON Albume_tbl.id_artist = Artisti_tbl.id_artist 
    WHERE Albume_tbl.id_album = ?");
$stmt->execute([$id_album]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    die("Albumul nu a fost găsit.");
}

// Preluăm piesele albumului
$stmtSongs = $conn->prepare("SELECT * FROM Piese_tbl WHERE id_album = ?");
$stmtSongs->execute([$id_album]);
$songs = $stmtSongs->fetchAll(PDO::FETCH_ASSOC);

// Calculează durata totală în secunde
$totalDuration = 0;
foreach ($songs as $song) {
    $totalDuration += $song['durata_secunde'];
}

// Formatează durata totală
$hours = floor($totalDuration / 3600);
$minutes = floor(($totalDuration % 3600) / 60);
$seconds = $totalDuration % 60;

if ($hours > 0) {
    $formattedDuration = sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
} else {
    $formattedDuration = sprintf("%02d:%02d", $minutes, $seconds);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($album['titlu']) ?> - Mini Spotify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #121212;
            color: white;
            min-height: 100vh;
        }
        .song-row:hover {
            background-color: #3a7bd5; /* albastru pal calm */
            cursor: pointer;
            color: white;
        }
        a.back-link {
            color: #1ed760; /* verde Spotify */
            text-decoration: none;
            font-weight: 600;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="container py-4">
    <a href="artist.php?id=<?= $album['id_artist'] ?>" class="back-link">&larr; Înapoi la artist</a>
    <h1><?= htmlspecialchars($album['titlu']) ?></h1>
    <h3><?= htmlspecialchars($album['nume_artist']) ?></h3>
    <p>An lansare: <?= htmlspecialchars($album['an_lansare']) ?></p>
    <p>Durata totală album: <?= $formattedDuration ?></p>

    <h2 class="mt-4">Piese</h2>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Titlu</th>
                <th>Durata</th>
                <th>Redare</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($songs as $index => $song): ?>
                <tr class="song-row">
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($song['titlu']) ?></td>
                    <td><?= gmdate("i:s", $song['durata_secunde']) ?></td>
                    <td>
                        <audio controls>
                            <source src="audio/<?= $song['id_piesa'] ?>.mp3" type="audio/mpeg">
                            Browserul tău nu suportă audio HTML5.
                        </audio>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
