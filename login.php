<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Toate câmpurile sunt obligatorii.";
    } else {
        $stmt = $conn->prepare("SELECT id_utilizator, nume_utilizator, parola FROM Utilizatori_tbl WHERE nume_utilizator = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($password === $user['parola'])  {
                $_SESSION['user_id'] = $user['id_utilizator'];
                $_SESSION['username'] = $user['nume_utilizator'];

                header('Location: index.php');
                exit;
            } else {
                $error = "Parola este incorectă.";
            }
        } else {
            $error = "Utilizatorul nu a fost găsit.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8" />
    <title>Autentificare - Mini Spotify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container py-4" style="background:#191414; color:white;">
    <h1>Autentificare</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Nume utilizator</label>
            <input type="text" id="username" name="username" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Parolă</label>
            <input type="password" id="password" name="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-success">Intră în cont</button>
    </form>
</body>
</html>
