<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($password) || $password !== $confirm) {
        die("Erreur : les mots de passe ne correspondent pas.");
    }

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Connexion SQLite (le fichier sera créé automatiquement)
        $pdo = new PDO('sqlite:utilisateurs.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Création de la table si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS utilisateurs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            mot_de_passe TEXT NOT NULL,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Insertion du mot de passe haché
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (mot_de_passe) VALUES (:mdp)");
        $stmt->execute([':mdp' => $hashedPassword]);

        // Redirection après succès
        header("Location: succée2.html");
        exit();
    } catch (PDOException $e) {
        die("Erreur base de données : " . $e->getMessage());
    }
}
?>
