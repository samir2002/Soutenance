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
    $sql = "SELECT r.id, r.id_theme, r.id_etudiant, t.id_enseignant 
            FROM demandes r
            JOIN themes t ON r.id_theme = t.id
            WHERE r.id = '$request_id' AND t.id_enseignant = {$_SESSION['id']}";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_theme = $row['id_theme'];
        $id_etudiant = $row['id_etudiant'];
        $id_enseignant = $row['id_enseignant'];

        // Mettre à jour la demande pour qu'elle soit approuvée
        $sql = "UPDATE demandes SET statut = 'approuvé' WHERE id = '$request_id'";
        if ($conn->query($sql) === TRUE) {
            // Mettre à jour le thème pour qu'il soit validé
            $sql = "UPDATE themes SET valide = TRUE WHERE id = '$id_theme'";
            if ($conn->query($sql) === TRUE) {
                // Insérer une entrée dans la table planning
                $sql = "INSERT INTO planning (id_theme, id_etudiant, id_enseignant) VALUES ('$id_theme', '$id_etudiant', '$id_enseignant')";
                if ($conn->query($sql) === TRUE) {
                    // Envoyer une notification
                    $message = "Votre thème a été approuvé et est en attente de planification.";
                    notify($id_etudiant, $id_enseignant, $message);
                    $_SESSION['success'] = "Demande approuvée avec succès!";
                } else {
                    $_SESSION['error'] = "Erreur lors de l'ajout à la planification: " . $conn->error;
                }
            } else {
                $_SESSION['error'] = "Erreur lors de la validation du thème: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "Erreur lors de l'approbation de la demande: " . $conn->error;
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
