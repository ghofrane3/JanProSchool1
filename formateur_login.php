<?php
try {
    // 1. Connect to SQLite database
    $db = new PDO('formateur.db'); 

    // 2. Get submitted data
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // 3. Retrieve user from DB
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        // Connexion réussie
        echo "Bienvenue " . htmlspecialchars($utilisateur['email']) . " !";
        // Tu peux ici démarrer une session, rediriger, etc.
    } else {
        // Identifiants invalides
        echo "Email ou mot de passe incorrect.";
    }
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();

}
$mot_de_passe_hache = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);



session_start();

// Exemple fictif pour démo
$correctEmail = "formateur@example.com";
$correctPassword = "motdepasse";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if ($email === $correctEmail && $password === $correctPassword) {
        $_SESSION["formateur"] = $email;
        echo "Connexion réussie. Bienvenue formateur $email !";
        // header("Location: dashboard_formateur.php");
    } else {
        echo "Adresse e-mail ou mot de passe incorrect.";
    }
}
?>
