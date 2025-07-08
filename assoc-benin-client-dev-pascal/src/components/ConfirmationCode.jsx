import React, { useEffect, useState } from "react";
import "../assets/scss/ConfirmationCode.scss";
import { FaNewspaper } from "react-icons/fa";

function ConfirmationCode() {
  const [idCode, setIdCode] = useState();

   useEffect(() => {
    let assocIDCode = localStorage.getItem("idCode")
    setIdCode(assocIDCode);
  }, []);
  

  return (
    <div className="confirmationCode">
      <div className="end-registration">
        <div className="validation-text">
          <div className="block text-center">
            <div className="block-icon">
              {" "}
              <FaNewspaper />{" "}
            </div>
            <div className="block-text">
              <p className="block-link" to="/actualites">
                {" "}
                Enregistrement Effectuée.
              </p>
              <p>
                Votre code d'enregistrement est:
                <br />
                {idCode ? <strong>{idCode}</strong> : null}
              </p>
            </div>
          </div>
        </div>
        <div className="code"></div>
      </div>
    </div>
  );
}

export default ConfirmationCode;
