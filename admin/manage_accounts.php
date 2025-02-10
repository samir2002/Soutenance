<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit;
}

$title = "Gérer les Comptes";

// Récupérer les utilisateurs
$sql = "SELECT * FROM utilisateurs";
$result = $conn->query($sql);
?>
<h1 class="mt-5">Gestion des Comptes</h1>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nom_utilisateur']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['role']}</td>
                        <td><a href='delete_user.php?id={$row['id']}' class='btn btn-danger'>Supprimer</a></td>
                      </tr>";
            }
        }
        ?>
    </tbody>
</table>
<h2 class="mt-5">Ajouter un Utilisateur</h2>
<form action="add_user.php" method="post" class="mt-3">
    <div class="form-group">
        <label for="nom_utilisateur">Nom d'utilisateur:</label>
        <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" required>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="mot_de_passe">Mot de passe:</label>
        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
    </div>
    <div class="form-group">
        <label for="role">Rôle:</label>
        <select class="form-control" id="role" name="role" required>
            <option value="etudiant">Étudiant</option>
            <option value="enseignant">Enseignant</option>
            <option value="administrateur">Administrateur</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Ajouter</button>
</form>
<?php include '../includes/footer.php'; ?>
