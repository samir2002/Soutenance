<?php
include 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, role) VALUES ('$nom_utilisateur', '$email', '$mot_de_passe', '$role')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Inscription réussie! Vous pouvez maintenant vous connecter.";
        header("Location: login.html");
    } else {
        $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
        header("Location: register.html");
    }
    $conn->close();
    exit();
}
?>
