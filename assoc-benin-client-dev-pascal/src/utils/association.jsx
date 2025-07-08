import AssociationCommunaute from "../components/AssociationCommunaute"
import AssociationInternational from "../components/AssociationInternational"
import AssociationOngNational from "../components/AssociationOngNational"
import AssociationReligieux from "../components/AssociationReligieux"
import ConfederationNational from "../components/ConfederationNational"
import Fondation from "../components/Fondation"
import Ong from "../components/Ong"
import PartiePolitique from "../components/PartiePolitique"
import Syndicat from "../components/Syndicat"

const associationName =[
  {
    id: 1,
    name: 'Association ou ONG à Caractère International',
    sigle: 'ACI',
    component : <AssociationInternational/>
  }, 
  {
    id: 2,
    name: 'Syndicat',
    sigle: 'AS',
    component : <Syndicat/>
  },
  {
    id: 3,
    name: 'Association ou ONG à Caractère National',
    sigle: 'ACN',
    component : <AssociationOngNational/> 
  },
  {
    id: 4,
    name: 'Fondation',
    sigle: 'FND',
    component : <Fondation/> 

  },
  {
    id: 5,
    name:'Mouvement ou Partie Politique ',
    sigle: 'MPP',
    component : <PartiePolitique/>
  },
  {
    id: 6,
    name: 'Association des Communautés Etrangères',
    sigle: 'ACE',
    component : <AssociationCommunaute/> 

  },
  {
    id: 7,
    name: 'Association ou ONG Nationale',
    sigle: 'AON',
    component : <AssociationOngNational/>

  },
  {
    id: 8,
    name: "Reconnaissance d'utilité Publique",
    sigle: 'RUP',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 9,
    name: "Organisation Non Gouvernementale",
    sigle: 'ONG',
    component : <Ong/>
  },
  {
    id: 10,
    name: "ONG (Accord Cardre)",
    sigle: 'ONGAC',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 11,
    name: "Association ou ONG de Communauté étrangère",
    sigle: 'ACE',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 12,
    name: " Association ou ONG de bonne Gouvernance ",
    sigle: 'ABG',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 13,
    name: " Alliances de Partis Politique",
    sigle: 'APP',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 14,
    name: " Organe de presse",
    sigle: 'OP',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 15,
    name: "Association ONG National de micro finance",
    sigle: 'ANMF',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 16,
    name: " Fédération National",
    sigle: 'FN',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 17,
    name: " Confédération National",
    sigle: 'CN',
    component : <ConfederationNational/>
  },
  {
    id: 18,
    name: "Mutuelle",
    sigle: 'Mutuelle',
    component : <AssociationInternational/> // à créer
  },
  {
    id: 19,
    name: 'Association à Caractère Réligieux',
    sigle: 'ACR',
    component : <AssociationReligieux/>
  }, 

]

export default associationName