import React from 'react'

function Step3({ page, setPage }) {
  return (
    <div className='card'>
      <div className="step-title">Information Générale</div>
      <input
        type="text"
        placeholder="Address"
      />
      <input
        type="text"
        placeholder="Nationality"
      />
      <input
        type="text"
        placeholder="Zipcode"
      />

      <button
        onClick={() => {
          alert("You've successfully submitted this form");
        }}
      >
        Submit
      </button>
      <br />
      <button
        onClick={() => {
          setPage(page - 1);
        }}
      >
        Previous
      </button>
    </div>
  )
}

export default Step3
