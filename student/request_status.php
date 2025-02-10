<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est étudiant
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'etudiant') {
    header("Location: ../login.html");
    exit;
}

$title = "Statut des Demandes";

// Récupérer les demandes de l'étudiant
$id_etudiant = $_SESSION['id'];
$sql = "SELECT r.id, t.titre, r.statut 
        FROM demandes r
        JOIN themes t ON r.id_theme = t.id
        WHERE r.id_etudiant = $id_etudiant";
$result = $conn->query($sql);
?>
<h1 class="mt-5">Statut des Demandes</h1>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thème</th>
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
                        <td>{$row['statut']}</td>
                      </tr>";
            }
        }
        ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
