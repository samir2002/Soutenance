<?php
include '../includes/db.php';
include '../includes/header.php';
include '../includes/functions.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est enseignant
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'enseignant') {
    header("Location: ../login.html");
    exit;
}

$title = "Confirmer les Demandes";

// Récupérer les demandes en attente (statut vide) pour les thèmes de l'enseignant connecté
$enseignant_id = $_SESSION['id'];
$sql = "SELECT d.id, t.titre, u.nom_utilisateur AS etudiant, d.statut 
        FROM demandes d
        JOIN themes t ON d.id_theme = t.id
        JOIN utilisateurs u ON d.id_etudiant = u.id
        WHERE t.id_enseignant = $enseignant_id AND (d.statut = '' OR d.statut = 'en_attente')";
$result = $conn->query($sql);

// Débogage étendu
echo "Nombre de demandes trouvées: " . $result->num_rows . "<br>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Demande ID: " . $row['id'] . ", Thème: " . $row['titre'] . ", Étudiant: " . $row['etudiant'] . ", Statut: " . $row['statut'] . "<br>";
    }
} else {
    echo "Aucune demande trouvée pour l'enseignant ID: " . $enseignant_id . "<br>";
}

// Afficher la requête SQL pour débogage
echo "Requête SQL: " . $sql . "<br>";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .container {
            padding-top: 60px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Confirmer les Demandes</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
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
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
