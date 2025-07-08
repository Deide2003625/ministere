import React from 'react'
import { NavLink } from 'react-router-dom'
import '../assets/scss/navbar.scss'
import logomisp  from '../assets/images/misp.png'


function Navbar() {
  return (
    <div className='navbar'>
        <img src={logomisp} alt="logo_ministere" />
      <ul>
        <li> <NavLink className="link" to='/'>Acceuil </NavLink> </li>
        <li> <NavLink className="link" to='/statut-demande'>Statut d'une demande </NavLink> </li>
        <li> <NavLink className="link" to="/modification">Modification  </NavLink> </li>
      </ul>
    </div>
  )
}

export default Navbar
