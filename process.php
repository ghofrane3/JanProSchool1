<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $day = htmlspecialchars(trim($_POST['day']));
    $month = htmlspecialchars(trim($_POST['month']));
    $year = htmlspecialchars(trim($_POST['year']));
    $birthdate = "$year-$month-$day";
    $birthplace = htmlspecialchars(trim($_POST['birthplace']));
    $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
    $address = htmlspecialchars(trim($_POST['address']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));

    // Validation des données
    $errors = [];

    if (empty($fullname)) {
        $errors['fullname'] = 'Le nom complet est requis';
    }

    if (!checkdate($month, $day, $year)) {
        $errors['birthdate'] = 'Date de naissance invalide';
    }

    if (empty($birthplace)) {
        $errors['birthplace'] = 'Le lieu de naissance est requis';
    }

    if (empty($gender)) {
        $errors['gender'] = 'Le genre est requis';
    }

    if (empty($address)) {
        $errors['address'] = 'L\'adresse est requise';
    }

    if (!preg_match('/^[0-9]{8}$/', $phone)) {
        $errors['phone'] = 'Numéro de téléphone invalide';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email invalide';
    }

    // Si pas d'erreurs, traiter les données
    if (empty($errors)) {
        // Ici vous pourriez enregistrer en base de données ou envoyer par email
        // Exemple avec une connexion MySQL (à adapter)
        
        $pdo = new PDO('sqlite:database0.db')

        $stmt = $db->prepare("INSERT INTO users (fullname, birthdate, birthplace, gender, address, phone, email) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fullname, $birthdate, $birthplace, $gender, $address, $phone, $email]);
        

        // Redirection vers une page de succès
        header('Location: success.html');
        exit;
    } else {
        // Retourner les erreurs au formulaire
        session_start();
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: index.html'); // Retour au formulaire
        exit;
    }
} else {
    // Si quelqu'un essaie d'accéder directement au script
    header('Location: index.html');
    exit;
}
?>