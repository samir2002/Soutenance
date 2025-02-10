<?php
function notify($id_etudiant, $id_enseignant, $message) {
    global $conn;

    if ($id_etudiant) {
        $etudiant_email = $conn->query("SELECT email FROM utilisateurs WHERE id='$id_etudiant'")->fetch_assoc()['email'];
        mail($etudiant_email, "Notification de soutenance", $message);

        // Ajouter la notification à la base de données
        $conn->query("UPDATE utilisateurs SET notifications = CONCAT(IFNULL(notifications, ''), '$message\n') WHERE id='$id_etudiant'");
    }

    if ($id_enseignant) {
        $enseignant_email = $conn->query("SELECT email FROM utilisateurs WHERE id='$id_enseignant'")->fetch_assoc()['email'];
        mail($enseignant_email, "Notification de soutenance", $message);

        // Ajouter la notification à la base de données
        $conn->query("UPDATE utilisateurs SET notifications = CONCAT(IFNULL(notifications, ''), '$message\n') WHERE id='$id_enseignant'");
    }
}
?>
