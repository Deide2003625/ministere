// routes
import PendingActionsIcon from '@mui/icons-material/PendingActions';
import PersonAddIcon from '@mui/icons-material/PersonAdd';
import PeopleIcon from '@mui/icons-material/People';
import { PATH_DASHBOARD } from '../../../routes/paths';
// components
import SvgIconStyle from '../../../components/SvgIconStyle';

// ----------------------------------------------------------------------

const getIcon = (name) => <SvgIconStyle src={`/icons/${name}.svg`} sx={{ width: 1, height: 1 }} />;

const ICONS = {
  booking: getIcon('ic_booking'),
  analytics: getIcon('ic_analytics'),
};

const userInfos = JSON.parse(localStorage.getItem('user'));

const superAdminFeatures = [
  { title: 'Statistiques', path: PATH_DASHBOARD.general.analytics, icon: ICONS.analytics },
  { title: 'Demandes en attente', path: PATH_DASHBOARD.demandes.enAttente, icon: <PendingActionsIcon /> },
  { title: 'Demandes traitées', path: PATH_DASHBOARD.demandes.traitees, icon: ICONS.booking },
  { title: 'Enregistrer un admin', path: PATH_DASHBOARD.admins.ajoutAdmin, icon: <PersonAddIcon /> },
  { title: 'Liste des admins', path: PATH_DASHBOARD.admins.listAdmins, icon: <PeopleIcon /> },
];

const adminFeatures = [
  { title: 'Statistiques', path: PATH_DASHBOARD.general.analytics, icon: ICONS.analytics },
  { title: 'Demandes en attente', path: PATH_DASHBOARD.demandes.enAttente, icon: <PendingActionsIcon /> },
  { title: 'Demandes traitées', path: PATH_DASHBOARD.demandes.traitees, icon: ICONS.booking },
];

const navConfig = userInfos
  ? [
      // GENERAL
      // ----------------------------------------------------------------------
      {
        items: userInfos.role === 'super admin' ? superAdminFeatures : adminFeatures,
      },
    ]
  : [
      {
        items: adminFeatures,
      },
    ];

export default navConfig;
