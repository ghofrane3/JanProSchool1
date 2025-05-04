<?php
// traitement_paiement.php

// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et valider les données
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $cardname = htmlspecialchars(trim($_POST['cardname']));
    $cardnumber = str_replace(' ', '', htmlspecialchars(trim($_POST['cardnumber'])));
    $expiry = htmlspecialchars(trim($_POST['expiry']));
    $cvv = htmlspecialchars(trim($_POST['cvv']));
    
    // Tableau pour stocker les erreurs
    $errors = [];
    
    // Validation des champs
    if (empty($fullname)) {
        $errors[] = "Le nom complet est requis";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    
    if (empty($phone)) {
        $errors[] = "Le numéro de téléphone est requis";
    }
    
    if (empty($cardname)) {
        $errors[] = "Le nom sur la carte est requis";
    }
    
    if (!preg_match('/^\d{16}$/', $cardnumber)) {
        $errors[] = "Le numéro de carte doit contenir 16 chiffres";
    }
    
    if (!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $expiry)) {
        $errors[] = "La date d'expiration doit être au format MM/AA";
    }
    
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors[] = "Le CVV doit contenir 3 ou 4 chiffres";
    }
    
    // Si aucune erreur, procéder au traitement
    if (empty($errors)) {
        try {
            // Connexion à la base de données
            $pdo = new PDO('sqlite:paiment.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Commencer une transaction
            $db->beginTransaction();
            
            // 1. Enregistrer l'étudiant
            $stmt = $db->prepare("INSERT INTO students (fullname, email, phone, registration_date) 
                                VALUES (?, ?, ?, NOW())");
            $stmt->execute([$fullname, $email, $phone]);
            $student_id = $db->lastInsertId();
            
            // 2. Enregistrer le paiement (en pratique, ne stockez pas les infos de carte complètes)
            $stmt = $db->prepare("INSERT INTO payments 
                                (student_id, amount, card_last_four, payment_date, status) 
                                VALUES (?, 1200, ?, NOW(), 'completed')");
            $stmt->execute([$student_id, substr($cardnumber, -4)]);
            
            // 3. Inscrire à la formation Android
            $stmt = $db->prepare("INSERT INTO registrations 
                                (student_id, course_id, registration_date) 
                                VALUES (?, 1, NOW())"); // 1 = ID de la formation Android
            $stmt->execute([$student_id]);
            
            // Valider la transaction
            $db->commit();
            
            // Envoyer un email de confirmation
            $to = $email;
            $subject = "Confirmation d'inscription à la formation Android";
            $message = "Bonjour $fullname,\n\n";
            $message .= "Votre inscription à la formation Développement Android a bien été enregistrée.\n";
            $message .= "Montant payé : 1200 TND\n\n";
            $message .= "Nous vous contacterons bientôt avec les détails de la formation.\n\n";
            $message .= "Cordialement,\nL'équipe de formation";
            $headers = "From: jampro.school@hotmail.com";
            
            mail($to, $subject, $message, $headers);
            
            // Rediriger vers la page de confirmation
            $_SESSION['payment_success'] = true;
            $_SESSION['student_name'] = $fullname;
            header("Location: confirmation.php");
            exit();
            
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $db->rollBack();
            $errors[] = "Une erreur est survenue lors du traitement de votre paiement: " . $e->getMessage();
            $_SESSION['errors'] = $errors;
            header("Location: paiement.html");
            exit();
        }
    } else {
        // Stocker les erreurs en session et rediriger
        $_SESSION['errors'] = $errors;
        header("Location: paiement.html");
        exit();
    }
} else {
    // Rediriger si accès direct au fichier
    header("Location: formation.html");
    exit();
}
?>