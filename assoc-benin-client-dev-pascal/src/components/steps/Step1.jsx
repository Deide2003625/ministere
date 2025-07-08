import React from 'react'
import '../../assets/scss/stepone.scss'
function Step1({page, setPage, formData, setFormData}) {
  return (
    <div className='card'>
      <div className="step-title">Information générale</div>
      <input 
        type="text" 
        placeholder='Dénomination'
        className='form-group'
        value={formData.denomination}
        onChange={(e) =>
          setFormData({ ...formData, denomination: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='Sigle'
        className='form-group'
        value={formData.sigle}
        onChange={(e) =>
          setFormData({ ...formData, sigle: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='Département'
        className='form-group'
        value={formData.departement}
        onChange={(e) =>
          setFormData({ ...formData, departement: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='Commune'
        className='form-group'
        value={formData.commune}
        onChange={(e) =>
          setFormData({ ...formData, commune: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='Arrondissement'
        className='form-group'
        value={formData.arrondissement}
        onChange={(e) =>
          setFormData({ ...formData, arrondissement: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='quartier'
        className='form-group'
        value={formData.quartier}
        onChange={(e) =>
          setFormData({ ...formData, quartier: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='Maison'
        className='form-group'
        value={formData.maison}
        onChange={(e) =>
          setFormData({ ...formData, maison: e.target.value })
        }
      />
       <input 
        type="text" 
        placeholder='Lot'
        className='form-group'
        value={formData.lot} //setting the value of the form to the props value
        onChange={(e) =>
          setFormData({ ...formData, lot: e.target.value })
        }
      />
       <input 
        type="text" 
        placeholder='Boîte Postale'
        className='form-group'
        value={formData.boitePostale} //setting the value of the form to the props value
        onChange={(e) =>
          setFormData({ ...formData, boitePostale: e.target.value })
        }
      />
       <input 
        type="text" 
        placeholder='Numéro'
        className='form-group'
      />
     <button
          onClick={() => {
            setPage(page + 1);
          }}>
          Next
        </button>
    </div>
  )
}

export default Step1
