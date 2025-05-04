<?php
// login.php

// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Valider les données
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'L\'adresse email est requise';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis';
    }
    
    // Si pas d'erreurs, vérifier les identifiants
    if (empty($errors)) {
        $pdo = new PDO('sqlite:admin.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ceci est un exemple avec des valeurs en dur (à ne pas faire en production)
        $valid_admin_email = 'admin@jps.com';
        $valid_admin_password = 'AdminJPS123';
        
        if ($email === $valid_admin_email && $password === $valid_admin_password) {
            // Authentification réussie
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            
            // Redirection vers le tableau de bord
            header('Location: admin-dashboard.php');
            exit;
        } else {
            $errors[] = 'Identifiants incorrects';
        }
    }
    
    // Si erreurs, les afficher
    if (!empty($errors)) {
        echo '<h2>Erreurs :</h2>';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '<p><a href="javascript:history.back()">Retour</a></p>';
    }
} else {
    // Si quelqu'un essaie d'accéder directement à login.php
    header('Location: formateurlogin.html');
    exit;
}
?>