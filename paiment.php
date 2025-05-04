<?php
// Connexion à la base SQLite (le fichier sera créé s’il n’existe pas)
try {
    $pdo = new PDO('sqlite:paiment.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création automatique de la table si elle n'existe pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS paiements (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname TEXT,
        email TEXT,
        phone TEXT,
        cardname TEXT,
        expiry TEXT,
        date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données SQLite : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $fullname   = htmlspecialchars($_POST['fullname'] ?? '');
    $email      = htmlspecialchars($_POST['email'] ?? '');
    $phone      = htmlspecialchars($_POST['phone'] ?? '');
    $cardname   = htmlspecialchars($_POST['cardname'] ?? '');
    $expiry     = htmlspecialchars($_POST['expiry'] ?? '');

    try {
        // Enregistrement dans la base SQLite
        $stmt = $pdo->prepare("INSERT INTO paiements (fullname, email, phone, cardname, expiry)
                               VALUES (:fullname, :email, :phone, :cardname, :expiry)");
        $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':phone' => $phone,
            ':cardname' => $cardname,
            ':expiry' => $expiry
        ]);

        // Redirection vers une page de succès
        header("Location: success_paiement.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}
?>
