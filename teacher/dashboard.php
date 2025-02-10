<?php
include '../includes/db.php';
include '../includes/header.php';
include '../includes/functions.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est enseignant
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'enseignant') {
    header("Location: ../login.html");
    exit();
}

$title = "Tableau de Bord Enseignant";

// Récupérer les notifications
$notifications = $conn->query("SELECT notifications FROM utilisateurs WHERE id='{$_SESSION['id']}'")->fetch_assoc()['notifications'];
?>
<h1 class="mt-5">Tableau de Bord Enseignant</h1>
<?php
if ($notifications) {
    echo '<div class="alert alert-info"><strong>Notifications:</strong><br>' . nl2br($notifications) . '</div>';
}

// Récupérer les soutenances planifiées pour cet enseignant
$sql = "SELECT p.id, t.titre, u.nom_utilisateur AS etudiant, p.date_soutenance, p.lieu 
        FROM planning p
        JOIN themes t ON p.id_theme = t.id
        JOIN utilisateurs u ON p.id_etudiant = u.id
        WHERE p.id_enseignant = {$_SESSION['id']}";
$result = $conn->query($sql);
?>
<h2 class="mt-5">Soutenances Planifiées</h2>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thème</th>
            <th>Étudiant</th>
            <th>Date de Soutenance</th>
            <th>Lieu</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['titre']}</td>
                        <td>{$row['etudiant']}</td>
                        <td>{$row['date_soutenance']}</td>
                        <td>{$row['lieu']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucune soutenance planifiée</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Formulaire pour proposer un nouveau thème -->
<h2 class="mt-5">Proposer un Nouveau Thème</h2>
<form action="submit_theme.php" method="post" class="mt-3">
    <div class="form-group">
        <label for="titre">Titre du Thème:</label>
        <input type="text" class="form-control" id="titre" name="titre" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Proposer</button>
</form>

<!-- Afficher les demandes en attente de validation -->
<?php
$sql = "SELECT r.id, t.titre, u.nom_utilisateur AS etudiant, r.statut 
        FROM demandes r
        JOIN themes t ON r.id_theme = t.id
        JOIN utilisateurs u ON r.id_etudiant = u.id
        WHERE t.id_enseignant = {$_SESSION['id']} AND r.statut = ''";
$result = $conn->query($sql);
?>
<h2 class="mt-5">Demandes en Attente</h2>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thème</th>
            <th>Étudiant</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['titre']}</td>
                        <td>{$row['etudiant']}</td>
                        <td>{$row['statut']}</td>
                        <td>
                            <a href='approve_request.php?id={$row['id']}' class='btn btn-success'>Approuver</a>
                            <a href='reject_request.php?id={$row['id']}' class='btn btn-danger'>Rejeter</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucune demande en attente</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
