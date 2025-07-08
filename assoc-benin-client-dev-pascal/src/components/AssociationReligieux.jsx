import React from "react";
import "../assets/scss/associationreligieux.scss";
function AssociationReligieux() {
  return (
    <div className="list-form">
      <ul>
        <li>Demande d'enrégistrememnt addressé au MISP</li>
        <li>Exemplaires du PV de l'Assemblée Générale Constitutif</li>
        <li>Exemplaires Des Status</li>
        <li>Casiers Judiciaires des principaux membres du Bureau executif</li>
        <li>Carte de séjour des étrangers membres du Bureau Exécutif </li>
        <li>
          L’acte fondant le pouvoir du Chargé spirituel (Diplôme ou tout acte
          conférant au responsable de l’église la qualité de Pasteur)
        </li>
        <li>
          Copie légalisée du contrat de bail ou de la convention de vente du
          domaine abritant le lieu de culte
        </li>
        <li>
          Autorisation d’installation du lieu de culte délivrée par le chef du
          quartier, de ville ou du village
        </li>
        <li>
          Copie de la version électronique des textes (Règlement Intérieur,
          statuts et Procès-verbal){" "}
        </li>
        <li>Quittance des frais d’étude de dossier</li>
      </ul>
    </div>
  );
}

export default AssociationReligieux;
