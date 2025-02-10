<?php
include '../includes/db.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM utilisateurs WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Utilisateur supprimé avec succès!";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
}
header("Location: manage_accounts.php");
?>
