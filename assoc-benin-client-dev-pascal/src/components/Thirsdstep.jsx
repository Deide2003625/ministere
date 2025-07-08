import "../assets/scss/thirdstep.scss";
import { Typography, Button, TextField } from "@mui/material";
import { v4 as uuidv4 } from "uuid";
import { apiBaseLink } from "../components/bases/config";
import Axios from "axios";
import React, { useState } from "react";
import axios from "axios";
import { useParams } from "react-router-dom";
import Fetching from "./Fetching";

function Thirsdstep({ values, handleBack, handleNext, recupParams }) {
  const [uuid, setUuid] = useState("");
  const [loading, setLoading] = useState(false);
  const {sigle} = useParams();
  const axiosInstance = Axios.create({
    baseURL: apiBaseLink,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
      "Content-Type": "multipart/form-data",
      withCredentials: true
    },
  });

  const submitFormData = (e) => {
    setLoading(true)
    e.preventDefault();
    const code = uuidv4().slice(0, 8).toUpperCase()
    setUuid(code);
    localStorage.setItem("idCode", code);
    
    console.log("Tentative de soumission vers:", apiBaseLink);
    console.log("Données à envoyer:", { ...values, request_code: code, legal_status: recupParams(sigle) });
    
    axiosInstance
      .post("/make_request", {
        ...values,
        request_code: code,
        legal_status: recupParams(sigle),
        identify_route: "create_association"
      })
      .then((response) => {
        console.log("Soumission réussie:", response.data);
        setLoading(false)
        handleNext();
      })
      .catch((error) => {
        console.error("Erreur de soumission:", error);
        console.error("Détails de l'erreur:", error.response?.data);
        setLoading(false);
        alert("Erreur lors de la soumission: " + (error.response?.data?.message || error.message));
      });
  };

  
  return (
    <div className="verification">
      <p>
        <strong>Statut de la demande :</strong> {recupParams(sigle)}{" "}
      </p>
      <p>
        <strong>Dénomination :</strong> {values.denomination}{" "}
      </p>
      <p>
        <strong>Sigle :</strong> {values.acronym}{" "}
      </p>
      <p>
        <strong>Département :</strong> {values.department}{" "}
      </p>
      <p>
        <strong>Commune :</strong> {values.town}{" "}
      </p>
      <p>
        <strong>Arrondissement :</strong> {values.district}{" "}
      </p>
      <p>
        <strong>Quartier :</strong> {values.quarter}{" "}
      </p>
      <p>
        <strong>Maison :</strong> {values.building}{" "}
      </p>
      <p>
        <strong>Lot/Carré :</strong> {values.lot_number}{" "}
      </p>
      <p>
        <strong>Boîte Postale :</strong> {values.po_box}{" "}
      </p>
      <p>
        <strong>Numero de téléphone de l'Association :</strong>{" "}
        {values.telephone}{" "}
      </p>
      <p>
        <strong>Date de l'AG constitutif :</strong>{" "}
        {values.ag_date}{" "}
      </p>
      <p>
        <strong>Email de l'Association :</strong> {values.email}{" "}
      </p>
      <p>
        <strong>Nom du Premier responsable :</strong> {values.first_person_in_charge}{" "}
      </p>
      <p>
        <strong>Email du 1er Responsable :</strong> {values.first_person_in_charge_email}{" "}
      </p>
      <p>
        <strong>Objectif de l'association :</strong> {values.goals}{" "}
      </p>      
      <div className="btn">
        <Button type="submit" onClick={handleBack}>
          PRECEDENT
        </Button>
        <Button type="submit" onClick={submitFormData}>
          ENREGISTRER
        </Button>
      </div>
        {
          loading ? <Fetching /> : null
        }
    </div>
  );
}

export default Thirsdstep;
