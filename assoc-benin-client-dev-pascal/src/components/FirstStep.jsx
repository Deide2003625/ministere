import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { apiUnityadministrative } from "./bases/config";
import InputLabel from "@mui/material/InputLabel";
import MenuItem from "@mui/material/MenuItem";
import FormControl from "@mui/material/FormControl";
import Select from "@mui/material/Select";
import { DemoContainer } from "@mui/x-date-pickers/internals/demo";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { LocalizationProvider } from "@mui/x-date-pickers/LocalizationProvider";
import { DatePicker } from "@mui/x-date-pickers/DatePicker";
import {
  getDepartments,
  getDistricts,
  getNeighborhoods,
  getTowns,
} from "./bases/areas_api";

import axios from "axios";
import * as Yup from "yup";
import validator from "validator";
import Stack from "@mui/material/Stack";
import "../assets/scss/firststep.scss";
import Autocomplete from "@mui/material/Autocomplete";
import {
  Typography,
  Button,
  Box,
  Grid,
  Checkbox,
  TextField,
  OutlineInput,
  TextareaAutosize,
} from "@mui/material";
import "../assets/scss/firststep.scss";

const validationSchema = Yup.object().shape({
  departement: Yup.string().required("Ce champ est obligatoire"),
  commune: Yup.string().required("Ce champ est obligatoire"),
  arrondissement: Yup.string().required("Ce champ est obligatoire"),
  quartier: Yup.string().required("Ce champ est obligatoire"),
  maison: Yup.string().required("Ce champ est obligatoire"),
  lot: Yup.string().required("Ce champ est obligatoire"),
  boitePostale: Yup.string().required("Ce champ est obligatoire"),
  phone: Yup.string().required("Ce champ est obligatoire"),
  denomination: Yup.string().required("Ce champ est obligatoire"),
  sigle: Yup.string().required("Ce champ est obligatoire"),
});
function FirstStep({
  activeStep,
  steps,
  handleNext,
  handleFormData,
  values,
  setFormData,
  formData,
}) {
  const [error, setError] = useState(false);
  const [departments, setDepartments] = useState();
  const [towns, setTowns] = useState();
  const [districts, setDistricts] = useState();
  const [neighborhoods, setNeighborhoods] = useState();
  const { register, handleSubmit, formState } = useForm({
    resolver: yupResolver(validationSchema),
  });
  const { errors } = formState;

  const onSubmit = (data) => console.log(data);

  const submitFormData = (e) => {
    e.preventDefault();
    if (
      validator.isEmpty(formData.denomination) ||
      validator.isEmpty(formData.acronym) ||
      validator.isEmpty(formData.department) ||
      validator.isEmpty(formData.town) ||
      validator.isEmpty(formData.quarter) ||
      validator.isEmpty(formData.quarter) ||
      validator.isEmpty(formData.building) ||
      validator.isEmpty(formData.lot_number) ||
      validator.isEmpty(formData.po_box) ||
      validator.isEmpty(formData.telephone) ||
      validator.isEmpty(formData.email) ||
      validator.isEmpty(formData.first_person_in_charge_email) ||
      validator.isEmpty(formData.first_person_in_charge) ||
      validator.isEmpty(formData.goals) ||
      validator.isEmpty(formData.ag_date)
    ) {
      setError(true);
    } else {
      handleNext();
    }
  };
  const getUserDepartment = (e) => {
    setFormData({ ...formData, department: e.target.value });
    getTowns(`/departments/${e.target.value}/towns`)
      .then((data) => setTowns(data.data.towns))
      .catch((error) => console.log(error));
  };
  const getUserTown = (e) => {
    setFormData({ ...formData, town: e.target.value });

    getDistricts(`/towns/${e.target.value}/districts`)
      .then((data) => setDistricts(data.data.districts))
      .catch((error) => console.log(error));
  };

  const getUserDistrict = (e) => {
    setFormData({ ...formData, district: e.target.value });

    getNeighborhoods(`/districts/${e.target.value}/neighborhoods`)
      .then((data) => setNeighborhoods(data.data.neighborhoods))
      .catch((error) => console.log(error));
  };
  const getUserNeighborhood = (e) => {
    setFormData({ ...formData, quarter: e.target.value });
  };

  useEffect(() => {
    getDepartments()
      .then((response) => setDepartments(response.data.departments))
      .catch((error) => console.log(error));
  }, []);

  return (
    <>
      <Typography variant="h5" className="title-form">
        <span className="number">1/3</span>
        <div className="step-name">Informations Générales</div>
      </Typography>
      <div className="register">
        <form onSubmit={submitFormData}>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Dénomination de l'association"
                variant="outlined"
                className="input-type"
                defaultValue={formData.denomination}
                required
                onChange={(e) => {
                  setFormData({ ...formData, denomination: e.target.value });
                }}
              />
            </div>
            <div className="validation">
              <TextField
                label="Sigle de l'association"
                defaultValue={formData.acronym}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({ ...formData, acronym: e.target.value });
                }}
              />
              <p>{/* {errors.sigle?.message} */}</p>
            </div>
          </div>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Maison"
                defaultValue={formData.building}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({ ...formData, building: e.target.value });
                }}
                /* {...register("maison") }  */
              />
              <p>{/* {errors.maison?.message} */}</p>
            </div>
            <div className="validation">
              <TextField
                label="Lot/carré"
                defaultValue={formData.lot_number}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({ ...formData, lot_number: e.target.value });
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
                defaultValue={formData.po_box}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({ ...formData, po_box: e.target.value });
                }}
                /* {...register("boitePostale") } */
              />
              <p>{/* {errors.boitePostale?.message} */}</p>
            </div>
            <div className="validation">
              <TextField
                label="Numero de téléphone de l'association"
                defaultValue={formData.telephone}
                variant="outlined"
                type="number"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({ ...formData, telephone: e.target.value });
                }}
              />
              <p>{/* {errors.phone?.message} */}</p>
            </div>
          </div>
          <div className="style-input">
            <div className="validation">
              <TextField
                label="Nom et prénom du 1er responsable"
                defaultValue={formData.first_person_in_charge}
                variant="outlined"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({
                    ...formData,
                    first_person_in_charge: e.target.value,
                  });
                }}
                /* {...register("boitePostale") } */
              />
              <p>{/* {errors.boitePostale?.message} */}</p>
            </div>
            <div className="validation">
              <TextField
                label="email du 1er responsable"
                defaultValue={formData.first_person_in_charge_email}
                variant="outlined"
                type="email"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({
                    ...formData,
                    first_person_in_charge_email: e.target.value,
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
                defaultValue={formData.email}
                variant="outlined"
                type="email"
                required
                className="input-type"
                onChange={(e) => {
                  setFormData({ ...formData, email: e.target.value });
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
                  defaultValue={formData.ag_date}
                  onChange={(e) => {
                    setFormData({ ...formData, ag_date: e.target.value });
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
                  defaultValue={formData.department}
                 
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
                  defaultValue={formData.town}
                  label="Commune"
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
                  defaultValue={formData.district}
                 
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
                   defaultValue={formData.quarter}
                  label="Quartier"
                  
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
              defaultValue={formData.goals}
              onChange={(e) => {
                setFormData({ ...formData, goals: e.target.value });
              }}
            />
          </div>
          <div className="btn">
            <Button type="submit">SUIVANT</Button>
          </div>
        </form>
      </div>
    </>
  );
}

export default FirstStep;
