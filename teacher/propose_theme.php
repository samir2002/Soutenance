<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est enseignant
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'enseignant') {
    header("Location: ../login.html");
    exit;
}

$title = "Proposer un Thème";
?>
<h1 class="mt-5">Proposer un Thème</h1>
<form action="submit_theme.php" method="post" class="mt-3">
    <div class="form-group">
        <label for="titre">Titre:</label>
        <input type="text" class="form-control" id="titre" name="titre" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Soumettre</button>
</form>
<?php include '../includes/footer.php'; ?>
