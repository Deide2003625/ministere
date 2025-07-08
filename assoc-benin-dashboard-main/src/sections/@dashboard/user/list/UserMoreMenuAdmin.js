import PropTypes from 'prop-types';
import { useState } from 'react';
import { Link as RouterLink } from 'react-router-dom';
// @mui
import { MenuItem, IconButton } from '@mui/material';
// routes
import InfoIcon from '@mui/icons-material/Info';
import AutorenewIcon from '@mui/icons-material/Autorenew';
import { PATH_DASHBOARD } from '../../../../routes/paths';
// components
import Iconify from '../../../../components/Iconify';
import MenuPopover from '../../../../components/MenuPopover';
import { getRequest } from '../../../../utils/api';
// ----------------------------------------------------------------------

UserMoreMenuAdmin.propTypes = {
  regisNumber: PropTypes.string,
  currentStatus: PropTypes.string
};

export default function UserMoreMenuAdmin({regisNumber, currentStatus}) {

  const [open, setOpen] = useState(null);

  const futureStatus = () => {
    if(currentStatus === "actif")
    {
      return "inactif"
    }
    
      return "actif"
  };

  const status = futureStatus()

  const setStatus = async () => {
    try {
      const response = await getRequest(`/api/freeze_admin/${regisNumber}/${status}`);
      if (response.data.updated === "YES") {
        window.location.href = "/dashboard/admins/liste"
      }
    } catch (error) {
      console.log(error);
    }
  };

  const handleOpen = (event) => {
    setOpen(event.currentTarget);
  };

  const handleClose = () => {
  };

  const ICON = {
    mr: 2,
    width: 20,
    height: 20,
  };

  return (
    <>
      <IconButton onClick={handleOpen}>
        <Iconify icon={'eva:more-vertical-fill'} width={20} height={20} />
      </IconButton>

      <MenuPopover
        open={Boolean(open)}
        anchorEl={open}
        onClose={handleClose}
        anchorOrigin={{ vertical: 'top', horizontal: 'left' }}
        transformOrigin={{ vertical: 'top', horizontal: 'right' }}
        arrow="right-top"
        sx={{
          mt: -1,
          width: 180,
          '& .MuiMenuItem-root': { px: 1, typography: 'body2', borderRadius: 0.75 },
        }}
      >
        <MenuItem component={RouterLink} to={regisNumber}>
        <InfoIcon sx={{ ...ICON }}/>
          Voir le profil
        </MenuItem>

        <MenuItem onClick={setStatus} sx={{ color: 'blue' }}>
        <AutorenewIcon sx={{ ...ICON }} />
          Actualiser le statut
        </MenuItem>

      </MenuPopover>
    </>
  );
}
