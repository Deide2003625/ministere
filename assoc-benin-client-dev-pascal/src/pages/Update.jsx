import React, { useState } from "react";
import "../assets/scss/update.scss";
import Axios from "axios";
import { apiBaseLink } from "../components/bases/config";
import {
  Typography,
  Button,
  Box,
  Grid,
  Checkbox,
  TextField,
  OutlineInput,
  FormControl,
  InputLabel,
  TextareaAutosize,
} from "@mui/material";
import barner from "../assets/images/head.png";
import { useHistory, useNavigate } from "react-router-dom";
import Fetching from "../components/Fetching";

function Update() {
  const [code, setCode] = useState();
  const [loading, setLoading] = useState(false);
  const [validation, setValidation] = useState();
  const axiosInstance = Axios.create({
    baseURL: apiBaseLink,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
      "Content-Type": "multipart/form-data",
      withCredentials: true,
    },
  });
  let navigate = useNavigate();

  function handleFormSubmit(event) {
    event.preventDefault();
    setLoading(true);
    axiosInstance
      .get(`${apiBaseLink}/get_record/${code}`)
      .then((response) => {
        setLoading(false);
        setValidation(response.data.record);
        console.log(code);
        if (response.data.existence === "YES") {
          localStorage.setItem("code", code);
          navigate("/demande-de-modification/");
        } 
      })
      .catch((error) => console.log(error));
  }
  return (
    <>
     <div className="baner-image">
      <img src={barner} alt="img-barner" />
    </div>
      <div className="update">
        <div className="code">
          <h1>Modification des Informations votre Association. </h1>
        </div>
        <div className="code-update">
          <form onSubmit={handleFormSubmit}>
            <input
              type="text"
              required
              value={code}
              placeholder="Entrer votre code d'enregistement"
              onChange={(event) => setCode(event.target.value)}
            />
            <div className="btn">
              <Button disabled={!code} type="submit">
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
        {loading ? <Fetching /> : null}
      </div>
    </>
  );
}

export default Update;
