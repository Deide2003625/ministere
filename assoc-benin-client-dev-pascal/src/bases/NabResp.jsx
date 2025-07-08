import React, { useState } from "react";
import { NavLink } from "react-router-dom";
import "../assets/css/nav.css";
import Hamburger from "hamburger-react";
import logomisp from "../assets/images/misp.png";

function NabResp() {
  const [click, setClick] = useState(false);
  const [isOpen, setOpen] = useState(false);
  const handleClick = () => setClick(!click);
  const Close = () => setClick(false);

  return (
    <div>
      <div className={click ? "main-container" : ""} onClick={() => Close()} />
      <nav className="navbar" onClick={(e) => e.stopPropagation()}>
        <div className="nav-container">
          <NavLink exact to="/" className="nav-logo">
            <img src={logomisp} alt="logo_ministere" />
          </NavLink>
          <ul className={click ? "nav-menu active" : "nav-menu"}>
            <li className="nav-item">
              <NavLink
                reloadDocument
                exact
                to="/"
                activeClassName="active"
                className="nav-links nav-home"
                onClick={click ? handleClick : null}
              >
                Faire une demande
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink
                reloadDocument
                exact
                to="/statut-demande"
                activeClassName="active"
                className="nav-links nav-status"
                onClick={click ? handleClick : null}
              >
                situation du dossier
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink 
                reloadDocument
                exact
                activeClassName="active"
                className="nav-links nav-update"
                onClick={click ? handleClick : null}
                to="/modification"
              >
                Modification
              </NavLink>
            </li>
          </ul>
          <div className="nav-icon" onClick={handleClick}>
            <Hamburger toggled={isOpen} toggle={setOpen} />
          </div>
        </div>
      </nav>
    </div>
  );
}

export default NabResp;
