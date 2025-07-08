import React, { useEffect, useState } from "react";
import "../assets/scss/statutdemande.scss";
import { Button } from "@mui/material";
import Axios from "axios";
import { apiBaseLink } from "../components/bases/config";
import { string } from "yup/lib/locale";
import '../components/Fetching'
import barner from "../assets/images/head.png";
import Fetching from "../components/Fetching";
function StatutDemande() {
  const [status, setStatus] = useState();
  const [validation, setValidation] = useState();
  const [loading, setLoading] = useState(false);
  const axiosInstance = Axios.create({
    baseURL: apiBaseLink,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
      "Content-Type": "multipart/form-data",
      withCredentials: true,
    },
  });
  function handleStatus(e) {
    e.preventDefault();
    setLoading(true)
    console.log(status);
    axiosInstance(`${apiBaseLink}/get_record/${status}`)
      .then((response) => {
        
        setLoading(false)
        setValidation(response.data.record);
      })
      .catch((error) => console.log(error));
  }

  return (
    <>
    <div className="baner-image">
      <img src={barner} alt="img-barner" />
    </div>
      <div className="modification">
        <div className="code">
          <h1>Statut de votre demande </h1>
        </div>
        <div className="code-update">
          <form onSubmit={handleStatus}>
            <input
              type="text"
              required
              placeholder="Entrer votre code d'enregistement"
              onChange={(e) => {
                setStatus(e.target.value);
              }}
            />
            <div className="btn">
              <Button disabled={!status} type="submit">
                Statut
              </Button>
            </div>
          </form>
          {
            validation ? (
              <div id="status">
                {typeof validation === "string" ? (
                  <strong style={{ color: "red" }}>
                    Votre Identifiant n'est pas valide !
                  </strong>
                ) : (
                  <div>
                    <strong style={{ color: "green"}}>
                      {validation.statut_requete}
                    </strong >
                    <p>Obsevation: {validation.observations}</p>
                  </div>
                )}
              </div>
            ) : null
            
          }
        </div>
        {
            loading ? <Fetching /> : null
          }
      </div>
     </>
  );
}

export default StatutDemande;
