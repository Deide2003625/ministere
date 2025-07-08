import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as Yup from "yup";
import AddIcon from "@mui/icons-material/Add";
import Box from "@mui/material/Box";
import Modal from "@mui/material/Modal";
import Autocomplete from "@mui/material/Autocomplete";
import document from "../utils/document";
import { MuiFileInput } from "mui-file-input";
import validator from "validator";
import "../assets/scss/secondstep.scss";
import DeleteIcon from "@mui/icons-material/Delete";
import IconButton from "@mui/material/IconButton";
import {
  Typography,
  Button,
  TextField,
  List,
  ListItem,
  ListItemText,
} from "@mui/material";
import "../assets/scss/firststep.scss";
import { ListItemSecondaryAction } from "@mui/material";

const style = {
  position: "absolute",
  top: "50%",
  left: "50%",
  transform: "translate(-50%, -50%)",
  width: 400,
  bgcolor: "background.paper",
  border: "2px solid #000",
  boxShadow: 24,
  p: 4,
};

function FilesStep({ handleFormData, values, handleNext, handleBack }) {
  const [open, setOpen] = React.useState(false);
  const handleOpen = () => setOpen(true);
  const handleClose = () => setOpen(false);
  const [file, setFile] = React.useState(null);
  const [error, setError] = useState(false);
  const [counter, setCounter] = useState(1);

  const incrementCounter = () => setCounter(counter + 1);
  const [selectedFile, setSelectedFile] = useState({
    fileType: null,
    fileObject: [],
  });

  const { register, control, handleSubmit, formState } = useForm();

  const onSubmit = (data) => {
    console.log(data);
  };

  const addFiles = () => {
    handleFormData(selectedFile.fileType, selectedFile.fileObject);
    incrementCounter();
    handleClose();

  };

  const setFileType = (value) => {
    setSelectedFile((oldState) => ({
      ...oldState,
      fileType: value,
    }));
  };

  const setFileObject = (event) => {
    const files = event.target.files;
    const selectedFilesArray = [];

    for (let i = 0; i < files.length; i++) {
      selectedFilesArray.push(files[i]);
      console.log(files[i].name);
      let row = `<tr>
      <td>${counter} </td>
      <td>${files[i].name}</td>
    </tr>`;
      window.document.querySelector("tbody").innerHTML += row;
    }
    console.log("ook");
    console.log(selectedFile.fileObject);

    setSelectedFile((oldState) => ({
      ...oldState,
      fileObject: selectedFilesArray,
    }));
  };

  const submitFormData = (e) => {
    e.preventDefault();

    handleNext();
    console.log(values.procesVerbal);
  };

  /* const handleFileDelete = (index) => {
    setFiles((prevFiles) => {
      const newFiles = [...prevFiles];
      newFiles.splice(index, 1);
      return newFiles;
    });
  }; */

  const handleFileRead = (file) => {
    const reader = new FileReader();
    reader.onload = (event) => {
      const text = event.target.result;
      alert(text);
    };
    reader.readAsText(file);
  };

  return (
    <>
      <Typography variant="h5" className="title-form">
        <span className="number">2/3</span> <small>Pieces jointes</small>
      </Typography>
      <div className="register-file">
        <div className="btn-modal">
          <Button
            variant="contained"
            color="primary"
            type="submit"
            onClick={handleOpen}
          >
            {" "}
            <AddIcon></AddIcon>Fichier
          </Button>
          <Modal
            open={open}
            onClose={handleClose}
            aria-labelledby="modal-modal-title"
            aria-describedby="modal-modal-description"
          >
            <Box sx={style}>
              <Typography id="modal-modal-title" variant="h6" component="h2">
                Ajouter une Piece Jointe
              </Typography>
              <div
                className="document"
                id="modal-modal-description"
                sx={{ mt: 2 }}
              >
                <form onSubmit={handleSubmit(onSubmit)}>
                  <div
                    className="document-content"
                    style={{ padding: "20px 5px 60px ", with: "100%" }}
                  >
                    <Autocomplete
                      disablePortal
                      control={control}
                      variant="outlined"
                      id="combo-box-demo"
                      getOptionLabel={(document) => document.name}
                      options={document}
                      sx={{ width: 400, height: 10 }}
                      renderOption={(props, document) => (
                        <Box component="li" {...props} key={document.id}>
                          {document.name}
                        </Box>
                      )}
                      renderInput={(params) => (
                        <TextField
                          {...params}
                          label="Fichier"
                          variant="outlined"
                          required
                        />
                      )}
                      onChange={(event, val) => setFileType(val.fileType)}
                    />
                  </div>
                  <div
                    className="doc"
                    style={{ padding: "20px 0px 60px ", with: "100%" }}
                  >
                    <Button
                      variant="contained"
                      component="label"
                      style={{
                        width: "100%",
                        backgroundColor: "#3F51B5",
                        color: "white",
                      }}
                    >
                      Ajouter un ficher
                      <input
                        required
                        type="file"
                        hidden
                        multiple
                        onChange={(event) => setFileObject(event)}
                      />
                    </Button>
                  </div>
                  <div className="submit-dov">
                    <Button
                      variant="contained"
                      color="primary"
                      type="submit"
                      onClick={addFiles}
                    >
                      Ajouter
                    </Button>
                  </div>
                </form>
              </div>
            </Box>
          </Modal>
        </div>
        {/* <List>
        {files.map((file, index) => (
          <ListItem key={index}>
            <ListItemText
              primary={file.name}
              secondary={file.size + " bytes"}
            />
            <ListItemSecondaryAction>
              <IconButton onClick={() => handleFileRead(file.file)}>
                <Typography variant="srOnly">Lire le fichier</Typography>
                <span role="img" aria-label="Lire">
                  📖
                </span>
              </IconButton>
              <IconButton onClick={() => handleFileDelete(index)}>
                <Typography variant="srOnly">Supprimer le fichier</Typography>
                <DeleteIcon />
              </IconButton>
            </ListItemSecondaryAction>
          </ListItem>
        ))}
      </List> */}
        <div id="tab-information">
          <table>
            <thead>
              <tr>
                <td className="title-table">N°</td>
                <td
                  className="title-table"
                  style={{ display: "flex", justifyContent: "center" }}
                >
                  Noms des Documents Ajoutés
                </td>
                {/* <td className="title-table">Action</td> */}
              </tr>
            </thead>
            <tbody>
              {/* {tableFile
                ? tableFile.map((item) => {
                    return (
                      <tr>
                        <td>1</td>
                        <td>{item}</td>
                        <td>Supprimer</td>
                      </tr>
                    );
                  })
                : null} */}
            </tbody>
          </table>
        </div>
        <div className="btn">
          <Button type="submit" onClick={handleBack}>
            PRECEDENT
          </Button>
          <Button type="submit" onClick={submitFormData}>
            SUIVANT
          </Button>
        </div>
      </div>
    </>
  );
}

export default FilesStep;
