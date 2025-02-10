<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est étudiant
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'etudiant') {
    header("Location: ../login.html");
    exit;
}

$title = "Choisir un Thème";

// Récupérer les thèmes disponibles
$sql = "SELECT * FROM themes";
$result = $conn->query($sql);
?>
<h1 class="mt-5">Choisir un Thème</h1>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Description</th>
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
                        <td><a href='submit_request.php?id={$row['id']}' class='btn btn-primary'>Choisir</a></td>
                      </tr>";
            }
        }
        ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
