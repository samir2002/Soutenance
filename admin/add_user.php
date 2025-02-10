<?php
include '../includes/db.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, role) VALUES ('$nom_utilisateur', '$email', '$mot_de_passe', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "Utilisateur ajouté avec succès!";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
header("Location: manage_accounts.php");
?>
