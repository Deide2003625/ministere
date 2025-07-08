import {BrowserRouter as Router, Routes, Route} from 'react-router-dom'
import Navbar from "./bases/NabResp";
import Footer from "./bases/Footer";
import Home from './pages/Home';
import Formulaire from './pages/Formulaire';
import Demand from './pages/Demand';
import StatutDemande from './pages/StatutDemande';
import Update from './pages/Update';
import Registration from './pages/Registration';
import UpdateAssoc from './pages/UpdateAssoc';
import './App.css'

function App() {
  return (
    <div className='App' style={{ boxSizin: 'border-box'}}>
      <Router>
        <Navbar />
          <Routes>
            <Route path='/*' element={<Home/>}></Route>
            <Route path='/formulaire' element={<Formulaire/>}></Route>
            <Route path='/demande/:sigle' element={< Demand/>}></Route>
            <Route path='/statut-demande' element={< StatutDemande/>}></Route>
            <Route path='/modification' element={<Update />}></Route>
            <Route path='/demande-de-modification' element={<UpdateAssoc />}></Route>
          </Routes>
      {/*  <Footer /> */}
      </Router>
   </div>
  );
}

export default App;
