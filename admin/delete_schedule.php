<?php
include '../includes/db.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM planning WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Soutenance supprimée avec succès!";
    } else {
        $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
}
}
header("Location: manage_schedule.php");
?>
