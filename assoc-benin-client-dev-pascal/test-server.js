const express = require('express');
const cors = require('cors');
const app = express();
const port = 8000;

// Middleware
app.use(cors());
app.use(express.json());

// Route de test
app.get('/api/test', (req, res) => {
  res.json({ message: 'API locale fonctionne !' });
});

// Route pour la soumission
app.post('/api/make_request', (req, res) => {
  console.log('Données reçues:', req.body);
  
  // Simuler un délai de traitement
  setTimeout(() => {
    res.json({ 
      success: true, 
      message: 'Demande soumise avec succès',
      request_code: req.body.request_code,
      data: req.body
    });
  }, 1000);
});

app.listen(port, () => {
  console.log(`Serveur de test démarré sur http://localhost:${port}`);
  console.log(`API disponible sur http://localhost:${port}/api`);
}); 