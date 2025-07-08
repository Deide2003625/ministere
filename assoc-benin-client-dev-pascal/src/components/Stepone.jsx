import React, { useState } from 'react'
import '../assets/scss/stepone.scss'
import {useForm} from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup';
import * as Yup from "yup";
import { Typography, Button, Grid, Checkbox, TextField, OutlineInput, FormControl, InputLabel } from '@mui/material';
import '../assets/scss/stepone.scss'
import validator from "validator";


function Stepone({ nextStep, handleFormData, values }) {

const [error, setError] = useState(false);

function submitFormData(e) {
  e.preventDefault();
   if(
     validator.isEmpty(values.denomination) ||
     validator.isEmpty(values.sigle) ||
     validator.isEmpty(values.denomination) ||
     validator.isEmpty(values.departement) ||
     validator.isEmpty(values.commune) ||
     validator.isEmpty(values.arrondissement) ||
     validator.isEmpty(values.quartier) ||
     validator.isEmpty(values.maison) ||
     validator.isEmpty(values.lot) ||
     validator.isEmpty(values.boitePostale) ||
     validator.isEmpty(values.phone)
   ){
     setError(true);
   } else {
    nextStep();
   }
  
}
 
  return (
    <div className='form1'>
      <div className="title-form">
        
      </div>
      <form onSubmit={submitFormData} >
        <div className='style-input'>
          <div className="validation">
            <TextField 
              label="Dénomination"
              variant='outlined'
              className='input-type'
              defaultValue={values.denomination}
              onChange={handleFormData("denomination")}
            /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
          </div>
          <div className="validation">
            <TextField 
              label="Sigle"
              variant='outlined'
              className='input-type'
              defaultValue={values.sigle}
              onChange={handleFormData("sigle")}
            /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
          </div>
        </div>
          <div className='style-input'>
            <div className="validation">
              <TextField 
                label="Département"
                variant='outlined'
                className='input-type'
                defaultValue={values.departement}
                onChange={handleFormData("departement")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
            <div className="validation">
              <TextField 
                label="Commune"
                variant='outlined'
                className='input-type'
                defaultValue={values.commune}
                onChange={handleFormData("commune")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
          </div>
          <div className='style-input'>
            <div className="validation">
              <TextField 
                label="Arrondissement"
                variant='outlined'
                className='input-type'
                defaultValue={values.arrondissement}
                onChange={handleFormData("arrondissement")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
            <div className="validation">
              <TextField 
                label="quartier"
                variant='outlined'
                className='input-type'
                defaultValue={values.quartier}
                onChange={handleFormData("quartier")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
          </div>
          <div className='style-input'>
            <div className="validation">
              <TextField 
                label="Maison"
                variant='outlined'
                className='input-type'
                defaultValue={values.maison}
                onChange={handleFormData("maison")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
            <div className="validation">
              <TextField 
                label="Lot/carré"
                variant='outlined'
                className='input-type'
                defaultValue={values.lot}
                onChange={handleFormData("lot")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
          </div>
          <div className='style-input'>
            <div className="validation">
              <TextField 
                label="Boîte Postale"
                variant='outlined'
                className='input-type'
                defaultValue={values.boitePostale}
                onChange={handleFormData("boitePostale")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
            <div className="validation">
            <TextField 
                label="Numero"
                variant='outlined'
                className='input-type'
                defaultValue={values.phone}
                onChange={handleFormData("phone")}
              /> 
              {error? (
              <p>
                Ce champ est obligatoire
              </p>
              ):(
                ""
              )}
            </div>
          </div>
          <div className='btn'>
            <Button type='submit'>SUIVANT</Button>
          </div>
      </form>
    </div>
  )
}

export default Stepone
