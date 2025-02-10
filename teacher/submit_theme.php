<?php
include '../includes/db.php';
include '../includes/functions.php';
session_start();

if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'enseignant') {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $id_enseignant = $_SESSION['id'];

    $sql = "INSERT INTO themes (titre, description, id_enseignant) VALUES ('$titre', '$description', '$id_enseignant')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Thème proposé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    header("Location: dashboard.php");
    exit();
}
?>
