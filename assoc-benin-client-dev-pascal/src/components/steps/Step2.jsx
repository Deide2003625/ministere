import React from 'react'

function Step2({page, setPage, formData, setFormData}) {
  return (
    <div className='card'>
      <div className="step-title">Pieces Jointes</div>
      <input 
        type="text" 
        placeholder='Document'
        value={formData.document} //setting the value of the form to the props value
        onChange={(e) =>
          setFormData({ ...formData, document: e.target.value })
        }
      />
      <input 
        type="text" 
        placeholder='file'
        value={formData.file} //setting the value of the form to the props value
        onChange={(e) =>
          setFormData({ ...formData, file: e.target.value })
        }
      />
      <button
        onClick={() => {
          setPage(page + 1);
        }}
      >
        NEXT
      </button>
        <br />

      <button
        onClick={() => {
          setPage(page - 1);
        }}
      >
        PREVIOUS
      </button>
    </div>
  )
}

export default Step2
