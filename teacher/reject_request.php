<?php
include '../includes/db.php';
include '../includes/functions.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est enseignant
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'enseignant') {
    header("Location: ../login.html");
    exit;
}

if (isset($_GET['id'])) {
    $request_id = $_GET['id'];

    // Récupérer les détails de la demande
    $sql = "SELECT r.id, r.id_etudiant
            FROM demandes r
            JOIN themes t ON r.id_theme = t.id
            WHERE r.id = '$request_id' AND t.id_enseignant = {$_SESSION['id']}";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_etudiant = $row['id_etudiant'];

        // Mettre à jour la demande pour qu'elle soit rejetée
        $sql = "UPDATE demandes SET statut = 'rejeté' WHERE id = '$request_id'";
        if ($conn->query($sql) === TRUE) {
            // Envoyer une notification
            $message = "Votre demande de thème a été rejetée.";
            notify($id_etudiant, null, $message);
            $_SESSION['success'] = "Demande rejetée avec succès!";
        } else {
            $_SESSION['error'] = "Erreur lors du rejet de la demande: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Demande non trouvée ou non autorisée.";
    }
} else {
    $_SESSION['error'] = "ID de la demande manquant.";
}

header("Location: confirm_request.php");
exit();
?>
