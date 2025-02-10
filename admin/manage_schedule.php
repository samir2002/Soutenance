<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit();
}

$title = "Gestion des Soutenances";
include '../includes/header.php';

// Récupérer les enseignants
$enseignants = $conn->query("SELECT id, nom_utilisateur FROM utilisateurs WHERE role='enseignant'");

// Récupérer les thèmes validés avec les étudiants associés
$themes_validated = $conn->query("SELECT t.id, t.titre, u.nom_utilisateur AS enseignant, e.nom_utilisateur AS etudiant, e.id AS etudiant_id
FROM themes t
JOIN utilisateurs u ON t.id_enseignant = u.id
JOIN demandes d ON t.id = d.id_theme
JOIN utilisateurs e ON d.id_etudiant = e.id
WHERE t.valide = 1");

// Récupérer les soutenances planifiées
$soutenances = $conn->query("SELECT p.id, t.titre, u.nom_utilisateur AS etudiant, p.date_soutenance, p.lieu, enc.nom_utilisateur AS encadrant 
FROM planning p
JOIN themes t ON p.id_theme = t.id
JOIN utilisateurs u ON p.id_etudiant = u.id
JOIN utilisateurs enc ON p.encadrant_id = enc.id");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
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
        <h1 class="mt-5">Gestion des Soutenances</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <h2>Thèmes Validés</h2>
        <div class="theme-list">
            <?php if ($themes_validated->num_rows > 0): ?>
                <?php while ($row = $themes_validated->fetch_assoc()): ?>
                    <div>
                        <strong><?php echo $row['titre']; ?></strong> par <?php echo $row['enseignant']; ?>
                        <button class="btn btn-primary select-theme" 
                            data-id="<?php echo $row['id']; ?>" 
                            data-title="<?php echo $row['titre']; ?>" 
                            data-etudiant="<?php echo $row['etudiant']; ?>"
                            data-etudiant-id="<?php echo $row['etudiant_id']; ?>">Sélectionner</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun thème validé</p>
            <?php endif; ?>
        </div>

        <h2 class="mt-5">Planifier une Soutenance</h2>
        <form action="add_schedule.php" method="post" class="mt-3">
            <input type="hidden" id="id_theme" name="id_theme" required>
            <input type="hidden" id="id_etudiant" name="id_etudiant" required>
            <div class="form-group">
                <label for="theme">Thème:</label>
                <input type="text" class="form-control" id="theme" name="theme" readonly required>
            </div>
            <div class="form-group">
                <label for="etudiant">Étudiant:</label>
                <input type="text" class="form-control" id="etudiant" name="etudiant" readonly required>
            </div>
            <div class="form-group">
                <label for="date_soutenance">Date de Soutenance:</label>
                <input type="datetime-local" class="form-control" id="date_soutenance" name="date_soutenance" required>
            </div>
            <div class="form-group">
                <label for="lieu">Lieu:</label>
                <input type="text" class="form-control" id="lieu" name="lieu" required>
            </div>
            <div class="form-group">
                <label for="encadrant">Enseignant Encadrant:</label>
                <select class="form-control" id="encadrant" name="encadrant" required>
                    <?php while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jury1">Jury 1:</label>
                <select class="form-control" id="jury1" name="jury1" required>
                    <?php
                    $enseignants->data_seek(0); // Rewind the result set
                    while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jury2">Jury 2:</label>
                <select class="form-control" id="jury2" name="jury2" required>
                    <?php
                    $enseignants->data_seek(0); // Rewind the result set
                    while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jury3">Jury 3:</label>
                <select class="form-control" id="jury3" name="jury3" required>
                    <?php
                    $enseignants->data_seek(0); // Rewind the result set
                    while ($row = $enseignants->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nom_utilisateur']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Planifier la Soutenance</button>
        </form>

        <h2 class="mt-5">Soutenances Planifiées</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thème</th>
                    <th>Étudiant</th>
                    <th>Date de Soutenance</th>
                    <th>Lieu</th>
                    <th>Encadrant</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($soutenances->num_rows > 0): ?>
                    <?php while ($row = $soutenances->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['titre']; ?></td>
                            <td><?php echo $row['etudiant']; ?></td>
                            <td><?php echo $row['date_soutenance']; ?></td>
                            <td><?php echo $row['lieu']; ?></td>
                            <td><?php echo $row['encadrant']; ?></td>
                            <td>
                                <a href="edit_schedule.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="delete_schedule.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette soutenance?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">Aucune soutenance planifiée</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script pour remplir automatiquement le formulaire avec le thème et l'étudiant sélectionnés
        document.querySelectorAll('.select-theme').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('id_theme').value = this.dataset.id;
                document.getElementById('theme').value = this.dataset.title;
                document.getElementById('etudiant').value = this.dataset.etudiant;
                document.getElementById('id_etudiant').value = this.dataset.etudiantId;
            });
        });
    </script>
</body>
</html>
