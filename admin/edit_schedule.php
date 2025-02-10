<?php
include '../includes/db.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
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

    // Mettre à jour la soutenance dans la base de données
    $sql = "UPDATE planning SET 
                id_theme='$id_theme', 
                id_etudiant='$etudiant_id', 
                date_soutenance='$date_soutenance', 
                lieu='$lieu', 
                encadrant_id='$encadrant_id', 
                jury1_id='$jury1_id', 
                jury2_id='$jury2_id', 
                jury3_id='$jury3_id'
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Soutenance modifiée avec succès!";
        // Envoyer des notifications
        notify($etudiant_id, "Votre soutenance a été modifiée pour le $date_soutenance.");
        notify($encadrant_id, "Vous encadrez une soutenance modifiée pour le $date_soutenance.");
        notify($jury1_id, "Vous avez été assigné en tant que jury pour une soutenance modifiée pour le $date_soutenance.");
        notify($jury2_id, "Vous avez été assigné en tant que jury pour une soutenance modifiée pour le $date_soutenance.");
        notify($jury3_id, "Vous avez été assigné en tant que jury pour une soutenance modifiée pour le $date_soutenance.");
    } else {
        $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    header("Location: manage_schedule.php");
    exit();
}

// Récupérer les données de la soutenance existante
$id = $_GET['id'];
$soutenance = $conn->query("SELECT * FROM planning WHERE id='$id'")->fetch_assoc();

// Récupérer les enseignants
$enseignants = $conn->query("SELECT id, nom_utilisateur FROM utilisateurs WHERE role='enseignant'");

// Récupérer les thèmes validés avec les étudiants associés
$themes_validated = $conn->query("SELECT t.id, t.titre, u.nom_utilisateur AS enseignant, e.nom_utilisateur AS etudiant, e.id AS etudiant_id
FROM themes t
JOIN utilisateurs u ON t.id_enseignant = u.id
JOIN demandes d ON t.id = d.id_theme
JOIN utilisateurs e ON d.id_etudiant = e.id
WHERE t.valide = 1");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Soutenance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .container {
            padding-top: 60px;
        }
        .theme-list {
            margin-bottom: 30px;
        }
        .theme-list .btn {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Modifier une Soutenance</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="edit_schedule.php" method="post" class="mt-3">
            <input type="hidden" id="id" name="id" value="<?php echo $soutenance['id']; ?>" required>
            <input type="hidden" id="id_theme" name="id_theme" value="<?php echo $soutenance['id_theme']; ?>" required>
            <input type="hidden" id="id_etudiant" name="id_etudiant" value="<?php echo $soutenance['id_etudiant']; ?>" required>
            <div class="form-group">
                <label for="theme">Thème:</label>
                <input type="text" class="form-control" id="theme" name="theme" value="<?php echo $soutenance['id_theme']; ?>" readonly required>
            </div>
            <div class="form-group">
                <label for="etudiant">Étudiant:</label>
                <input type="text" class="form-control" id="etudiant" name="etudiant" value="<?php echo $soutenance['id_etudiant']; ?>" readonly required>
            </div>
            <div class="form-group">
                <label for="date_soutenance">Date de Soutenance:</label>
                <input type="datetime-local" class="form-control" id="date_soutenance" name="date_soutenance" value="<?php echo $soutenance['date_soutenance']; ?>" required>
            </div>
            <div class="form-group">
                <label for="lieu">Lieu:</label>
                <input type="text" class="form-control" id="lieu" name="lieu" value="<?php echo $soutenance['lieu']; ?>" required>
            </div>
            <div class="form-group">
                <label for="encadrant">Enseignant Encadrant:</label>
                <select class="form-control" id="encadrant" name="encadrant" required>
                    <?php while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $soutenance['encadrant_id'] == $row['id'] ? 'selected' : ''; ?>><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jury1">Jury 1:</label>
                <select class="form-control" id="jury1" name="jury1" required>
                    <?php
                    $enseignants->data_seek(0); // Rewind the result set
                    while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $soutenance['jury1_id'] == $row['id'] ? 'selected' : ''; ?>><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jury2">Jury 2:</label>
                <select class="form-control" id="jury2" name="jury2" required>
                    <?php
                    $enseignants->data_seek(0); // Rewind the result set
                    while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $soutenance['jury2_id'] == $row['id'] ? 'selected' : ''; ?>><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jury3">Jury 3:</label>
                <select class="form-control" id="jury3" name="jury3" required>
                    <?php
                    $enseignants->data_seek(0); // Rewind the result set
                    while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $soutenance['jury3_id'] == $row['id'] ? 'selected' : ''; ?>><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Modifier la Soutenance</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
