const axios = require('axios');

const API_BASE_URL = 'http://127.0.0.1:8000';

async function testAPI() {
  console.log('Test de connectivité API...');
  
  try {
    // Test de base
    const response = await axios.get(`${API_BASE_URL}/api/get_in_progress`);
    console.log('✅ API accessible');
    console.log('Données reçues:', response.data);
    
    if (response.data && response.data.records) {
      console.log(`📊 Nombre de demandes en attente: ${response.data.records.length}`);
    }
    
    // Test des demandes traitées
    const treatedResponse = await axios.get(`${API_BASE_URL}/api/get_approved`);
    console.log('✅ API demandes traitées accessible');
    console.log('Données traitées reçues:', treatedResponse.data);
    
    if (treatedResponse.data && treatedResponse.data.records) {
      console.log(`📊 Nombre de demandes traitées: ${treatedResponse.data.records.length}`);
    }
    
  } catch (error) {
    console.error('❌ Erreur API:', error.message);
    if (error.response) {
      console.error('Status:', error.response.status);
      console.error('Data:', error.response.data);
    }
  }
}

testAPI(); 