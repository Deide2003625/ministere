import React from "react";
import "../assets/scss/home.scss";
import barner from "../assets/images/head.png";
import { useState } from "react";
import AssociationReligieux from "../components/AssociationReligieux";
import AssociationInternational from "../components/AssociationInternational";
import ConfederationNational from "../components/ConfederationNational";
import AssociationOngNational from "../components/AssociationOngNational";
import { Button } from "@mui/material";
import { Link, NavLink, Route, Routes, useNavigate } from "react-router-dom";
import associationName from "../utils/association";
import { FaAngleRight } from "react-icons/fa";

function Home() {
  const [choice, setChoice] = useState();
  let navigate = useNavigate();

  const sendParams = (e) => {
    console.log(choice);
    navigate("/demande/" + choice);
  };

  return (
    <div className="home">
      <div className="barner">
        <img src={barner} alt="" />
      </div>
      <div className="choose-association">Choisir le type d'association</div>
      <div className="association-container">
        <div className="assoc-list">
          <div className="associations">
            {associationName.map((association) => {
              return (
                <NavLink
                  className="asoc-name"
                  onClick={() => setChoice(association.sigle)}
                  to={association.sigle}
                >
                  {" "}
                  <FaAngleRight /> {association.name}{" "}
                </NavLink>
              );
            })}
          </div>
          <Button disabled={!choice} onClick={sendParams}>
            Demande
          </Button>
        </div>
        <div className="list">
          <p>Liste des Pièces à Fournir</p>
          <Routes>
            {associationName.map((association) => {
              return (
                <Route
                  path={association.sigle}
                  element={association.component}
                ></Route>
              );
            })}
          </Routes>
        </div>
      </div>
    </div>
  );
}

export default Home;
