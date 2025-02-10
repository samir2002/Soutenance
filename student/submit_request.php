<?php
include '../includes/db.php';
include '../includes/functions.php';
session_start();

if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'etudiant') {
    header("Location: ../login.html");
    exit();
}

if (isset($_GET['id_theme'])) {
    $id_theme = $_GET['id_theme'];
    $id_etudiant = $_SESSION['id'];

    $sql = "INSERT INTO demandes (id_theme, id_etudiant, statut) VALUES ('$id_theme', '$id_etudiant', 'en attente')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Demande de thème envoyée avec succès!";
    } else {
        $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    header("Location: dashboard.php");
    exit();
}
?>
