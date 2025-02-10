<?php
include '../includes/db.php';
include '../includes/header.php';
include '../includes/functions.php';
session_start();

if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'etudiant') {
    header("Location: ../login.html");
    exit();
}

$title = "Tableau de Bord Étudiant";

// Récupérer les notifications
$notifications = $conn->query("SELECT notifications FROM utilisateurs WHERE id='{$_SESSION['id']}'")->fetch_assoc()['notifications'];
?>
<h1 class="mt-5">Tableau de Bord Étudiant</h1>
<?php
if ($notifications) {
    echo '<div class="alert alert-info"><strong>Notifications:</strong><br>' . nl2br($notifications) . '</div>';
}

// Récupérer les soutenances planifiées pour cet étudiant
$sql = "SELECT p.id, t.titre, u.nom_utilisateur AS enseignant, p.date_soutenance, p.lieu 
        FROM planning p
        JOIN themes t ON p.id_theme = t.id
        JOIN utilisateurs u ON p.id_enseignant = u.id
        WHERE p.id_etudiant = {$_SESSION['id']}";
$result = $conn->query($sql);
?>
<h2 class="mt-5">Soutenances Planifiées</h2>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thème</th>
            <th>Enseignant</th>
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
                        <td>{$row['enseignant']}</td>
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

<!-- Afficher les thèmes proposés par les enseignants -->
<?php
$sql = "SELECT t.id, t.titre, t.description, u.nom_utilisateur AS enseignant 
        FROM themes t
        JOIN utilisateurs u ON t.id_enseignant = u.id";
$result = $conn->query($sql);
?>
<h2 class="mt-5">Thèmes Proposés</h2>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thème</th>
            <th>Description</th>
            <th>Enseignant</th>
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
                        <td>{$row['description']}</td>
                        <td>{$row['enseignant']}</td>
                        <td>
                            <a href='submit_request.php?id_theme={$row['id']}' class='btn btn-primary'>Demander</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun thème proposé</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Afficher les demandes de thèmes -->
<?php
$sql = "SELECT r.id, t.titre, u.nom_utilisateur AS enseignant, r.statut 
        FROM demandes r
        JOIN themes t ON r.id_theme = t.id
        JOIN utilisateurs u ON t.id_enseignant = u.id
        WHERE r.id_etudiant = {$_SESSION['id']}";
$result = $conn->query($sql);
?>
<h2 class="mt-5">Demandes de Thèmes</h2>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thème</th>
            <th>Enseignant</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['titre']}</td>
                        <td>{$row['enseignant']}</td>
                        <td>{$row['statut']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Aucune demande de thème</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
