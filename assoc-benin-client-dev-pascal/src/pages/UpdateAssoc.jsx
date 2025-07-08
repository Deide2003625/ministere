import React, { useEffect, useState } from "react";
import Fetching from "../components/Fetching";
import barner from "../assets/images/head.png";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {
  getDepartments,
  getDistricts,
  getNeighborhoods,
  getTowns,
} from "../components/bases/areas_api";
import {
  NotificationContainer,
  NotificationManager,
} from "react-notifications";
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
  Select,
  MenuItem,
} from "@mui/material";
import { Stack } from "react-bootstrap";
import Axios from "axios";
import { apiBaseLink } from "../components/bases/config";

function UpdateAssoc() {
  const [code, setCode] = useState("");
  const [loading, setLoading] = useState(false);
  const [previousUserData, setPreviousUserData] = useState({});
  const [departments, setDepartments] = useState();
  const [towns, setTowns] = useState();
  const [districts, setDistricts] = useState();
  const [neighborhoods, setNeighborhoods] = useState();
  const axiosInstance = Axios.create({
    baseURL: apiBaseLink,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
      "Content-Type": "multipart/form-data",
      withCredentials: true,
    },
  });
  const [donnees, setDonnees] = useState({
    statut_legal: "",
    denomination: "",
    code_requete: localStorage.getItem("code"),
    a_telephone: "",
    a_email: "",
    acronyme: "",
    date_ag: "2022-04-16",
    departement: "",
    commune: "",
    arrondissement: "",
    quartier: "",
    numero_lot: "",
    immeuble: "",
    boite_postale: "",
    premier_responsable: "",
    email_premier_responsable: "",
    objectifs: "",
  });

  useEffect(() => {
    // Récupérer le code depuis le stockage local
    const storedCode = localStorage.getItem("code");
    setCode(storedCode);
    console.log(storedCode);
    // Récupérer les informations liées au code depuis l'API
    // et stocker les données dans le state donnees
    // Exemple d'utilisation d'Axios pour récupérer les données
    axiosInstance
      .get(`${apiBaseLink}/get_record/${storedCode}`)
      .then((response) => {
        console.log(response.data.record);
        setDonnees(response.data.record);
        /* setPreviousUserData(response.data.record); */
      })
      .catch((error) => {});
  }, []);

  function handleInputChange(event) {
    const { name, value } = event.target;
    setDonnees((prevState) => ({
      ...prevState,
      [name]: value,
    }));
    /* setDonnees({
      ...donnees,
      [name]: value,
    }); */
  }

  const handleUpdateSubmit = (event) => {
    event.preventDefault();
    setLoading(true);
    NotificationManager.success(
      "Association modifier avec success",
      "DAIC/MISP"
    );
    axiosInstance
      .post("/update_request/", donnees)
      .then((response) => {
        setLoading(false);
        console.log(response.data);
      })
      .catch((error) => {
        console.log(error);
      });
  };
  const getUserDepartment = (e) => {
    setDonnees({ ...donnees, departement: e.target.value });
    getTowns(`/departments/${e.target.value}/towns`)
      .then((data) => setTowns(data.data.towns))
      .catch((error) => console.log(error));
  };
  const getUserTown = (e) => {
    setDonnees({ ...donnees, commune: e.target.value });

    getDistricts(`/towns/${e.target.value}/districts`)
      .then((data) => setDistricts(data.data.districts))
      .catch((error) => console.log(error));
  };

  const getUserDistrict = (e) => {
    setDonnees({ ...donnees, arrondissement: e.target.value });

    getNeighborhoods(`/districts/${e.target.value}/neighborhoods`)
      .then((data) => setNeighborhoods(data.data.neighborhoods))
      .catch((error) => console.log(error));
  };
  const getUserNeighborhood = (e) => {
    setDonnees({ ...donnees, quartier: e.target.value });
  };

  useEffect(() => {
    getDepartments()
      .then((response) => setDepartments(response.data.departments))
      .catch((error) => console.log(error));
  }, []);
  return (
    <>
      <div className="baner-image">
        <img src={barner} alt="img-barner" />
      </div>
      <div className="update">
        <div className="code">
          <h1>Modification des Informations votre Association. </h1>
        </div>
        <div>
        <div className="register">
        <form onSubmit={handleUpdateSubmit}>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Dénomination de l'association"
                variant="outlined"
                className="input-type"
                value={donnees.denomination}
                required
                onChange={(e) => {
                  setDonnees({ ...donnees, denomination: e.target.value });
                }}
              />
            </div>
            <div className="validation">
              <TextField
                label="Sigle de l'association"
                value={donnees.acronyme}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({ ...donnees, acronyme: e.target.value });
                }}
              />
              <p>{/* {errors.sigle?.message} */}</p>
            </div>
          </div>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Maison"
                value={donnees.immeuble}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({ ...donnees, immeuble: e.target.value });
                }}
                /* {...register("maison") }  */
              />
              <p>{/* {errors.maison?.message} */}</p>
            </div>
            <div className="validation">
              <TextField
                label="Lot/carré"
                value={donnees.numero_lot}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({ ...donnees, numero_lot: e.target.value });
                }}
                /*  {...register("lot") } */
              />
              <p>{/*  {errors.lot?.message} */}</p>
            </div>
          </div>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Boîte Postale"
                value={donnees.boite_postale}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({ ...donnees, boite_postale: e.target.value });
                }}
                /* {...register("boitePostale") } */
              />
              <p>{/* {errors.boitePostale?.message} */}</p>
            </div>
            <div className="validation">
              <TextField
                label="Numero de téléphone de l'association"
                value={donnees.a_telephone}
                variant="outlined"
                type="number"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({ ...donnees, a_telephone: e.target.value });
                }}
              />
              <p>{/* {errors.phone?.message} */}</p>
            </div>
          </div>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Nom et prénom du 1er responsable"
                value={donnees.premier_responsable}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({
                    ...donnees,
                    premier_responsable: e.target.value,
                  });
                }}
                /* {...register("boitePostale") } */
              />
              <p>{/* {errors.boitePostale?.message} */}</p>
            </div>
            <div className="validation">
              <TextField
                label="email du 1er responsable"
                value={donnees.email_premier_responsable}
                variant="outlined"
                type="email"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({
                    ...donnees,
                    email_premier_responsable: e.target.value,
                  });
                }}
                /* {...register("phone") } */
              />
              <p>{/* {errors.phone?.message} */}</p>
            </div>
          </div>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Email de l'association"
                value={donnees.a_email}
                variant="outlined"
                type="email"
                required
                className="input-type"
                onChange={(e) => {
                  setDonnees({ ...donnees, a_email: e.target.value });
                }}
                /* {...register("boitePostale") } */
              />
              <p>{/* {errors.boitePostale?.message} */}</p>
            </div>
            <div className="validation">
              <Stack component="form" noValidate spacing={3}>
                <TextField
                  className="input-type"
                  id="date"
                  variant="outlined"
                  label="date de la tenue de l'AG constitutive"
                  type="date"
                  value={donnees.date_ag}
                  onChange={(e) => {
                    setDonnees({ ...donnees, date_ag: e.target.value });
                  }}
                />
              </Stack>
              <p>{/* {errors.phone?.message} */}</p>
            </div>
          </div>
          <div className="localisation">
            <div className="departement-commune">
              <FormControl fullWidth>
                <InputLabel id="demo-simple-select-label">Département</InputLabel>
                <Select
                  labelId="demo-simple-select-label"
                  id="demo-simple-select"
                  value={donnees.departement}
                  variant="outlined"
                  onChange={getUserDepartment}
                  label="Département"
                >
                   {departments
                    ? departments.map((department, index) => {
                        return (
                          <MenuItem key={index} value={department.name}>
                            {department.name}
                          </MenuItem>
                        );
                      })
                    : null}
                </Select>
              </FormControl>
              <FormControl fullWidth>
                <InputLabel id="demo-simple-select-label">Commune</InputLabel>
                <Select
                  labelId="demo-simple-select-label"
                  id="demo-simple-select"
                  value={donnees.commune}
                  label="Commune"
                  variant="outlined"
                  onChange={getUserTown}
                >
                  {towns
                    ? towns.map((town, index) => {
                        return (
                          <MenuItem key={index} value={town.name}>
                            {town.name}
                          </MenuItem>
                        );
                      })
                    : null}
                </Select>
              </FormControl>
            </div>
          </div>
          <div className="localisation">
            <div className="departement-commune">
              <FormControl fullWidth>
                <InputLabel id="demo-simple-select-label">Arrondissement</InputLabel>
                <Select
                  labelId="demo-simple-select-label"
                  id="demo-simple-select"
                  value={donnees.arrondissement}
                  variant="outlined"
                  onChange={getUserDistrict}
                  label="Arrondissement"
                >
                   {districts
                    ? districts.map((district, index) => {
                        return (
                          <MenuItem key={index} value={district.name}>
                            {district.name}
                          </MenuItem>
                        );
                      })
                    : null}
                </Select>
              </FormControl>
              <FormControl fullWidth>
                <InputLabel id="demo-simple-select-label">Quartier</InputLabel>
                <Select
                  labelId="demo-simple-select-label"
                  id="demo-simple-select"
                   value={donnees.quartier}
                  label="Quartier"
                  variant="outlined"
                  onChange={getUserNeighborhood}
                >
                  {neighborhoods
                    ? neighborhoods.map((neighborhood, index) => {
                        return (
                          <MenuItem key={index} value={neighborhood.name}>
                            {neighborhood.name}
                          </MenuItem>
                        );
                      })
                    : null}
                </Select>
              </FormControl>
            </div>
          </div>
          <div className="objectif">
            <TextField
              fullWidth
              multiline
              variant="outlined"
              label="Objectif de l'association"
              required
              minRows={5}
              value={donnees.objectifs}
              onChange={(e) => {
                setDonnees({ ...donnees, objectifs: e.target.value });
              }}
            />
          </div>
          <div className="btn">
            <Button type="submit">ENVOYER</Button>
          </div>
        </form>
      </div>
        </div>
        {loading ? <Fetching /> : null}
      </div>
    </>
  );
}

export default UpdateAssoc;
