<?php
// Id utilizator simulat
$user_id = 1;

// Preluăm playlisturile utilizatorului
$stmt = $conn->prepare("SELECT * FROM Playlisturi_tbl WHERE id_utilizator = ? ORDER BY data_creare DESC");
$stmt->execute([$user_id]);
$myPlaylists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="mt-5">
    <h2>Playlisturile Mele</h2>
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
