const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
const port = 3000;

// Connexion MongoDB
mongoose.connect('mongodb://127.0.0.1:27017/centre_formation', {
  useNewUrlParser: true,
  useUnifiedTopology: true
}).then(() => {
  console.log("✅ Connecté à MongoDB");
}).catch((err) => {
  console.error("❌ Erreur MongoDB :", err);
});

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(__dirname)); // Sert les fichiers HTML, CSS, JS

// Route d’accueil → affiche le formulaire
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'desktop23.html'));
});

// Schéma et modèle
const apprenantSchema = new mongoose.Schema({
  nom: String,
  prenom: String,
  date_naissance: String,
  lieu_naissance: String,
  genre: String,
  adresse: String,
  telephone: String,
  email: String
});
const Apprenant = mongoose.model('Apprenant', apprenantSchema);

// Route pour recevoir le formulaire
app.post('desktop23.html', async (req, res) => {
  try {
    const nouvelApprenant = new Apprenant(req.body);
    await nouvelApprenant.save();
    res.send("✅ Apprenant enregistré !");
  } catch (err) {
    res.status(500).send("❌ Erreur lors de l'inscription.");
  }
});

// Lancement serveur
app.listen(port, () => {
  console.log(`🚀 Serveur lancé sur http://localhost:${port}`);
});

