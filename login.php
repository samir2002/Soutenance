<?php
include 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur='$nom_utilisateur'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'etudiant') {
                header("Location: student/dashboard.php");
            } else if ($user['role'] == 'enseignant') {
                header("Location: teacher/dashboard.php");
            } else if ($user['role'] == 'administrateur') {
                header("Location: admin/dashboard.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
        }
    } else {
        $_SESSION['error'] = "Nom d'utilisateur incorrect.";
    }
    header("Location: login.html");
    exit();
}
?>
