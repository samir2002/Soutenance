<?php
include '../includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_theme = $_POST['id_theme'];
    $etudiant_id = $_POST['id_etudiant'];
    $date_soutenance = $_POST['date_soutenance'];
    $lieu = $_POST['lieu'];
    $encadrant_id = $_POST['encadrant'];
    $jury1_id = $_POST['jury1'];
    $jury2_id = $_POST['jury2'];
    $jury3_id = $_POST['jury3'];

    // Vérifier que les IDs existent dans la base de données
    $theme_exists = $conn->query("SELECT id FROM themes WHERE id = '$id_theme'")->num_rows > 0;
    $etudiant_exists = $conn->query("SELECT id FROM utilisateurs WHERE id = '$etudiant_id' AND role = 'etudiant'")->num_rows > 0;
    $encadrant_exists = $conn->query("SELECT id FROM utilisateurs WHERE id = '$encadrant_id' AND role = 'enseignant'")->num_rows > 0;
    $jury1_exists = $conn->query("SELECT id FROM utilisateurs WHERE id = '$jury1_id' AND role = 'enseignant'")->num_rows > 0;
    $jury2_exists = $conn->query("SELECT id FROM utilisateurs WHERE id = '$jury2_id' AND role = 'enseignant'")->num_rows > 0;
    $jury3_exists = $conn->query("SELECT id FROM utilisateurs WHERE id = '$jury3_id' AND role = 'enseignant'")->num_rows > 0;

    if (!$theme_exists || !$etudiant_exists || !$encadrant_exists || !$jury1_exists || !$jury2_exists || !$jury3_exists) {
        $_SESSION['error'] = "L'un des identifiants fournis n'existe pas.";
        header("Location: manage_schedule.php");
        exit();
    }

    // Vérifier que les jurys ne sont pas encadrant
    if ($jury1_id == $encadrant_id || $jury2_id == $encadrant_id || $jury3_id == $encadrant_id) {
        $_SESSION['error'] = "Un enseignant encadrant ne peut pas être jury.";
        header("Location: manage_schedule.php");
        exit();
    }

    // Vérifier si les enseignants ne sont pas planifiés pour plus de 2 soutenances en tant que jury
    $query = "SELECT jury1_id, jury2_id, jury3_id, COUNT(*) as count FROM planning 
              WHERE (jury1_id = '$jury1_id' OR jury2_id = '$jury1_id' OR jury3_id = '$jury1_id' 
                     OR jury1_id = '$jury2_id' OR jury2_id = '$jury2_id' OR jury3_id = '$jury2_id' 
                     OR jury1_id = '$jury3_id' OR jury2_id = '$jury3_id' OR jury3_id = '$jury3_id') 
              AND DATE(date_soutenance) = DATE('$date_soutenance')
              GROUP BY jury1_id, jury2_id, jury3_id";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        if (($row['jury1_id'] == $jury1_id || $row['jury2_id'] == $jury1_id || $row['jury3_id'] == $jury1_id) && $row['count'] >= 2) {
            $_SESSION['error'] = "Le jury 1 est déjà planifié pour 2 soutenances ce jour-là.";
            header("Location: manage_schedule.php");
            exit();
        }
        if (($row['jury1_id'] == $jury2_id || $row['jury2_id'] == $jury2_id || $row['jury3_id'] == $jury2_id) && $row['count'] >= 2) {
            $_SESSION['error'] = "Le jury 2 est déjà planifié pour 2 soutenances ce jour-là.";
            header("Location: manage_schedule.php");
            exit();
        }
        if (($row['jury1_id'] == $jury3_id || $row['jury2_id'] == $jury3_id || $row['jury3_id'] == $jury3_id) && $row['count'] >= 2) {
            $_SESSION['error'] = "Le jury 3 est déjà planifié pour 2 soutenances ce jour-là.";
            header("Location: manage_schedule.php");
            exit();
        }
    }

    // Vérifier si les enseignants ne sont pas planifiés pour une autre soutenance à la même heure
    $query = "SELECT COUNT(*) as count FROM planning 
              WHERE (encadrant_id = '$encadrant_id' OR jury1_id = '$encadrant_id' OR jury2_id = '$encadrant_id' OR jury3_id = '$encadrant_id'
                     OR jury1_id = '$jury1_id' OR jury2_id = '$jury1_id' OR jury3_id = '$jury1_id' 
                     OR jury1_id = '$jury2_id' OR jury2_id = '$jury2_id' OR jury3_id = '$jury2_id' 
                     OR jury1_id = '$jury3_id' OR jury2_id = '$jury3_id' OR jury3_id = '$jury3_id') 
              AND date_soutenance = '$date_soutenance'";
    $result = $conn->query($query);
    $count = $result->fetch_assoc()['count'];

    if ($count > 0) {
        $_SESSION['error'] = "Un enseignant est déjà planifié pour une soutenance à cette heure.";
        header("Location: manage_schedule.php");
        exit();
    }

    // Insérer la soutenance dans la base de données
    $sql = "INSERT INTO planning (id_theme, id_etudiant, id_enseignant, date_soutenance, lieu, date_creation, encadrant_id, jury1_id, jury2_id, jury3_id)
        VALUES ('$id_theme', '$etudiant_id', '$encadrant_id', '$date_soutenance', '$lieu', NOW(), '$encadrant_id', '$jury1_id', '$jury2_id', '$jury3_id')";

if ($conn->query($sql) === TRUE) {
    $_SESSION['success'] = "Soutenance planifiée avec succès!";
    // Envoyer des notifications
    notify($etudiant_id, "Votre soutenance a été planifiée pour le $date_soutenance.");
    notify($encadrant_id, "Vous encadrez une soutenance planifiée pour le $date_soutenance.");
    notify($jury1_id, "Vous avez été assigné en tant que jury pour une soutenance planifiée pour le $date_soutenance.");
    notify($jury2_id, "Vous avez été assigné en tant que jury pour une soutenance planifiée pour le $date_soutenance.");
    notify($jury3_id, "Vous avez été assigné en tant que jury pour une soutenance planifiée pour le $date_soutenance.");
} else {
    $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
}

    $conn->close();
    header("Location: manage_schedule.php");
    exit();
}

// Fonction de notification
function notify($id_user, $message) {
    global $conn;
    $user_email = $conn->query("SELECT email FROM utilisateurs WHERE id='$id_user'")->fetch_assoc()['email'];
    mail($user_email, "Notification de soutenance", $message);
}
?>
