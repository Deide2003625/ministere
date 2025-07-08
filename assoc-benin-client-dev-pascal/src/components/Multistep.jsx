import React from 'react'
import {makeStyles} from '@mui/material/styles'
import {Stepper, Step, StepLabel, Typography, Button} from '@mui/material'


const useStyles = makeStyles({
  root:{
    widht:'50%',
    margin:'6rem auto',
    border: '1px solid #999',
  }
})

const  Multistep = () => {

  function getSteps(){
    return ["Information sur le Siège", 'Information', 'verification']
  }
  const steps = getSteps();

  function getStepsContent(stepIndex){
    switch(stepIndex){
      case 0:
          return "Step one ";
      case 1:
          return "Step two ";
      case 2:
          return "Step three "
      default: return "Uncknow step"
    }
  }

  return (
    <div>
      <Stepper alternativeLabel>
        {
          steps.map(label =>{
            <Step key={label}>
              <StepLabel>
                {label}
              </StepLabel>
            </Step>
          })

        }
      </Stepper>
    </div>
  )
}

export default Multistep
