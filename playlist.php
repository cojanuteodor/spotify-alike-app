<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Trebuie să fii logat pentru a vedea acest playlist și a adăuga piese.");
}

$id_playlist = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Preluăm playlist și user
$stmt = $conn->prepare("
    SELECT Playlisturi_tbl.*, Utilizatori_tbl.nume_utilizator 
    FROM Playlisturi_tbl 
    JOIN Utilizatori_tbl ON Playlisturi_tbl.id_utilizator = Utilizatori_tbl.id_utilizator 
    WHERE Playlisturi_tbl.id_playlist = ?");
$stmt->execute([$id_playlist]);
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Playlistul nu a fost găsit.");
}

// Verificăm dacă utilizatorul are permisiunea de modificare (doar creatorul)
$can_edit = ($playlist['id_utilizator'] == $user_id);

// Gestionare POST: adăugare sau ștergere piesă
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$can_edit) {
        die("Nu ai permisiunea să modifici acest playlist.");
    }

    if (isset($_POST['add_song'])) {
        $id_piesa = $_POST['id_piesa'] ?? null;

        if (!$id_piesa) {
            $_SESSION['error'] = "Selectează o piesă.";
        } else {
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM Piese_tbl WHERE id_piesa = ?");
            $stmtCheck->execute([$id_piesa]);
            if ($stmtCheck->fetchColumn() == 0) {
                $_SESSION['error'] = "Piesa selectată nu există.";
            } else {
                $stmtInsert = $conn->prepare("INSERT IGNORE INTO PiesePlaylist_tbl (id_playlist, id_piesa) VALUES (?, ?)");
                if ($stmtInsert->execute([$id_playlist, $id_piesa])) {
                    $_SESSION['success'] = "Piesa a fost adăugată în playlist!";
                } else {
                    $_SESSION['error'] = "A apărut o eroare la adăugare.";
                }
            }
        }
    }

    if (isset($_POST['delete_song'])) {
        $id_piesa_del = $_POST['id_piesa_del'] ?? null;
        if ($id_piesa_del) {
            $stmtDel = $conn->prepare("DELETE FROM PiesePlaylist_tbl WHERE id_playlist = ? AND id_piesa = ?");
            if ($stmtDel->execute([$id_playlist, $id_piesa_del])) {
                $_SESSION['success'] = "Piesa a fost eliminată din playlist!";
            } else {
                $_SESSION['error'] = "A apărut o eroare la eliminare.";
            }
        }
    }

    // Redirect pentru a evita resubmiteri
    header("Location: playlist.php?id=" . $id_playlist);
    exit;
}

// Preluăm piesele din playlist (după orice modificare)
$stmtSongs = $conn->prepare("
    SELECT Piese_tbl.* 
    FROM PiesePlaylist_tbl 
    JOIN Piese_tbl ON PiesePlaylist_tbl.id_piesa = Piese_tbl.id_piesa 
    WHERE PiesePlaylist_tbl.id_playlist = ?");
$stmtSongs->execute([$id_playlist]);
$songs = $stmtSongs->fetchAll(PDO::FETCH_ASSOC);

// Calculează durata totală a playlistului (secunde)
$totalDuration = 0;
foreach ($songs as $song) {
    $totalDuration += $song['durata_secunde'];
}

// Funcție pentru formatat durata
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    if ($hours > 0) {
        return sprintf("%d:%02d:%02d", $hours, $minutes, $secs);
    } else {
        return sprintf("%02d:%02d", $minutes, $secs);
    }
}

$formattedDuration = formatDuration($totalDuration);

// Preluăm toate piesele disponibile pentru dropdown (pentru adăugare)
$stmtAllSongs = $conn->query("SELECT id_piesa, titlu FROM Piese_tbl ORDER BY titlu");
$allSongs = $stmtAllSongs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($playlist['titlu']) ?> - Mini Spotify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #191414;
            color: white;
            min-height: 100vh;
        }
        .song-row:hover {
            background-color: #1DB954;
            cursor: pointer;
            color: black;
        }
        .btn-delete {
            color: #ff4d4d;
            border: none;
            background: transparent;
            font-weight: bold;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .btn-delete:hover {
            color: #ff0000;
        }
    </style>
</head>
<body class="container py-4">
    <a href="index.php" style="color: #1ed760;">&larr; Înapoi</a>
    <h1><?= htmlspecialchars($playlist['titlu']) ?></h1>
    <p>Creat de: <?= htmlspecialchars($playlist['nume_utilizator']) ?></p>
    <p>Data creare: <?= htmlspecialchars($playlist['data_creare']) ?></p>

    <!-- Afișare mesaje din sesiune -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <p><strong>Durata totală:</strong> <?= $formattedDuration ?></p>

    <?php if ($can_edit): ?>
        <h2 class="mt-4">Adaugă piesă în playlist</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="id_piesa" class="form-label">Selectează piesa</label>
                <select id="id_piesa" name="id_piesa" class="form-select" required>
                    <option value="">Alege o piesă</option>
                    <?php foreach ($allSongs as $p): ?>
                        <option value="<?= $p['id_piesa'] ?>"><?= htmlspecialchars($p['titlu']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="add_song" class="btn btn-success">Adaugă piesa</button>
        </form>
    <?php else: ?>
        <p><em>Acest playlist nu poate fi modificat de tine.</em></p>
    <?php endif; ?>

    <h2>Piese în playlist</h2>
    <table class="table table-dark table-striped align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Titlu</th>
                <th>Durata</th>
                <th>Redare</th>
                <?php if ($can_edit): ?><th>Acțiuni</th><?php endif; ?>
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
                    <?php if ($can_edit): ?>
                        <td>
                            <form method="POST" onsubmit="return confirm('Ești sigur că vrei să ștergi această piesă din playlist?');" style="margin:0;">
                                <input type="hidden" name="id_piesa_del" value="<?= $song['id_piesa'] ?>">
                                <button type="submit" name="delete_song" class="btn-delete" title="Șterge piesa">&#10006;</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
