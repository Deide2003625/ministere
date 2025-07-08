import React, { useContext, useState } from 'react'
import Step1 from '../components/steps/Step1';
import Step2 from '../components/steps/Step2';
import Step3 from '../components/steps/Step3';
import '../assets/scss/registration.scss'


function Registration() {
  const [page, setPage] = useState(0);
  const [formData, setFormData] = useState({
    denomination: "",
    sigle: "",
    departement: "",
    commune: "",
    arrondissement: "",
    quartier: "",
    maison: "",
    lot: "",
    boitePostale: "",
    phone: "", 
    document:"",
    file: "",

  });
  const componentList = [
    <Step1
      formData={formData}
      setFormData={setFormData}
      page={page}
      setPage={setPage} 
    />,
    <Step2 
      formData={formData}
      setFormData={setFormData}
      page={page}
      setPage={setPage} 
    />,
    <Step3
      formData={formData}
      setFormData={setFormData}
      page={page}
      setPage={setPage}  
    />,
  ];


  return(
    <div className='registration'>
        <div className="progress-bar">
        <div style={{width: page === 0? "25%": page === 1? "50%": page === 2? "75%" : "100%"}}></div>
        </div>
        <div>{componentList[page]}</div>
    </div>
  )
 
}

export default Registration