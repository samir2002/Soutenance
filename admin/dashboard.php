<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] != 'administrateur') {
    header("Location: ../login.html");
    exit();
}

$title = "Tableau de Bord Administrateur";
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 70px);
            text-align: center;
            padding: 20px;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
        }
        h1 {
            font-size: 3em;
            font-weight: 700;
            margin-bottom: 20px;
            color: #004080;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .btn-custom {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 18px;
            margin: 10px;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.3s ease;
            cursor: pointer;
            width: 250px;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue, Administrateur</h1>
        <div class="btn-container">
            <a href="manage_accounts.php" class="btn btn-custom">Gestion des comptes</a>
            <a href="manage_schedule.php" class="btn btn-custom">Gestion des soutenances</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
