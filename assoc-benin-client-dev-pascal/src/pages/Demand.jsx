import React, { useEffect } from "react";
import { useState } from "react";
import Box from "@mui/material/Box";
import Stepper from "@mui/material/Stepper";
import Step from "@mui/material/Step";
import StepLabel from "@mui/material/StepLabel";
import Button from "@mui/material/Button";
import Typography from "@mui/material/Typography";
import "../assets/scss/demand.scss";
import FirstStep from "../components/FirstStep";
import FilesStep from "../components/FilesStep";
import Thirsdstep from "../components/Thirsdstep";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import ConfirmationCode from "../components/ConfirmationCode";
import assoc from "../utils/association";
import { useNavigate, useParams } from "react-router-dom";
import barner from "../assets/images/head.png";
const steps = ["Information ", "Pièce Jointe", "Verification"];

function Demand() {
  const [activeStep, setActiveStep] = React.useState(0);
  const [skipped, setSkipped] = React.useState(new Set());
  const myForm = React.useRef(null);
  const { sigle } = useParams();
  const [idCode, setIdCode] = useState();
  let navigate = useNavigate();

  /* useEffect(() => {
    let assocIDCode = localStorage.getItem("idCode")
    setIdCode(assocIDCode);
  }, []);
   */
  const [formData, setFormData] = useState({
    denomination: "",
    acronym: "",
    building: "",
    lot_number: "",
    po_box: "",
    telephone: "",
    first_person_in_charge: "",
    first_person_in_charge_email: "",
    email: "",
    ag_date: "2023-01-01",
    department: "",
    town: "",
    district: "",
    quarter: "",
    goals: "",
    legal_status: "",
    membres_ag: "",
    insertion_liste_de_presence: null,
    rules_of_procedure: null,
    payment_receipt: null,
    recording_letter: null,
    statute: null,
    verbal_trial: null,
    criminal_record: null,
    representation_mandate: null,
    receipt_statement: null,
    newspaper: null,
    activities_report: null,
    benin_address: null,
    outlander_address: null,
    residence_certificate: null,
    cv: null,
    work_certificate: null,
    diploma: null,
    declaration: null,
    presence_list: null,
    society_project: null,
    inter: null,
    description_sheet: null,
    logo_and_emblem: null,
    ideology: null,
    indentifiant: null,
  });

  const handleInputData = (input, value) => {
    setFormData((prevState) => ({
      ...prevState,
      [input]: value,
    }));
   
  };

  const recupParams = (sigle) => {
    const resultTab = assoc.filter((item) => item.sigle === sigle);
   
    return resultTab[0].name;
  };

  function getStepsContent(stepIndex) {
    switch (stepIndex) {
      case 0:
        return (
          <FirstStep
            activeStep={activeStep}
            steps={steps}
            handleNext={handleNext}
            values={formData}
            setFormData={setFormData}
            formData={formData}
          />
        );
      case 1:
        return (
          <FilesStep
            activeStep={activeStep}
            steps={steps}
            handleNext={handleNext}
            handleFormData={handleInputData}
            handleBack={handleBack}
            values={formData}
            setFormData={setFormData}
          />
        );
      case 2:
        return (
          <Thirsdstep
            activeStep={activeStep}
            steps={steps}
            handleNext={handleNext}
            handleFormData={handleInputData}
            values={formData}
            setFormData={setFormData}
            handleBack={handleBack}
            recupParams={recupParams} /* idCode= {idCode}  */
          />
        );
      default:
        return "no step";
    }
  }

  const isStepOptional = (step) => {
    return step === 1;
  };

  const isStepSkipped = (step) => {
    return skipped.has(step);
  };

  const handleNext = (data) => {
    let newSkipped = skipped;

    if (isStepSkipped(activeStep)) {
      newSkipped = new Set(newSkipped.values());
      newSkipped.delete(activeStep);
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped(newSkipped);
  };

  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };

  const handleSkip = () => {
    if (!isStepOptional(activeStep)) {
      // You probably want to guard against something like this,
      // it should never occur unless someone's actively trying to break something.
      throw new Error("You can't skip a step that isn't optional.");
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped((prevSkipped) => {
      const newSkipped = new Set(prevSkipped.values());
      newSkipped.add(activeStep);
      return newSkipped;
    });
  };

  const handleReset = () => {
    navigate("/");
  };
  return (
    <>
    <div className="baner-image">
      <img src={barner} alt="img-barner" />
    </div>
    <div className="demand">
      <div className="demand-container">
        <div className="type-demande">
          ENREGISTREMENT: {recupParams(sigle)}{" "}
        </div>
      </div>
      <div className="information">
        <Box sx={{ width: "100%" }}>
          <div className="evolution">
            <Stepper activeStep={activeStep} style={{}}>
              {steps.map((label, index) => {
                const stepProps = {};
                const labelProps = {};
                if (isStepOptional(index)) {
                  labelProps.optional = (
                    <Typography variant="caption"></Typography>
                  );
                }
                if (isStepSkipped(index)) {
                  stepProps.completed = false;
                }
                return (
                  <Step key={label} {...stepProps}>
                    <StepLabel {...labelProps}>{label}</StepLabel>
                  </Step>
                );
              })}
            </Stepper>
          </div>
          {activeStep === steps.length ? (
            <React.Fragment>
              <Typography sx={{ alignJustify: "center" }}>
                <div className="validation-pop">
                  <ConfirmationCode idCode={idCode} />
                </div>
              </Typography>
              <Box sx={{ display: "flex", flexDirection: "row", pt: 2 }}>
                <Box sx={{ flex: "1 1 auto" }} />
                <Button onClick={handleReset}>RETOUR</Button>
              </Box>
            </React.Fragment>
          ) : (
            <React.Fragment>
              <div sx={{ mt: 2, mb: 1 }}>{getStepsContent(activeStep)}</div>
            </React.Fragment>
          )}
        </Box>
      </div>
    </div>
    </>
  );
}

export default Demand;
