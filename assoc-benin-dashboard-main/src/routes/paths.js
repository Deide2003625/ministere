// ----------------------------------------------------------------------

function path(root, sublink) {
  return `${root}${sublink}`;
}

const ROOTS_AUTH = '/auth';
const ROOTS_DASHBOARD = '/dashboard';

// ----------------------------------------------------------------------
const userInfos = JSON.parse(localStorage.getItem("user"))

export const PATH_AUTH = {
  root: ROOTS_AUTH,
  login: path(ROOTS_AUTH, '/login'),
  loginUnprotected: path(ROOTS_AUTH, '/login-unprotected'),
  register: path(ROOTS_AUTH, '/register'),
  registerUnprotected: path(ROOTS_AUTH, '/register-unprotected'),
  ForgotPassword: path(ROOTS_AUTH, '/forgot-password'),
  verify: path(ROOTS_AUTH, '/verify')
};

export const PATH_DASHBOARD = {
  root: ROOTS_DASHBOARD,
  account: path(ROOTS_DASHBOARD, '/profil-admin'),
  general: {
    app: path(ROOTS_DASHBOARD, '/app'),
    analytics: path(ROOTS_DASHBOARD, '/statistiques'),
  },
  demandes: {
    root: path(ROOTS_DASHBOARD, '/demandes'),
    enAttente: path(ROOTS_DASHBOARD, '/demandes/en-attente'),
    traitees: path(ROOTS_DASHBOARD, '/demandes/traitees'),
    
  },
  admins: {
    root: path(ROOTS_DASHBOARD, '/admins'),
    ajoutAdmin: path(ROOTS_DASHBOARD, '/admins/enregistrement'),
    listAdmins: path(ROOTS_DASHBOARD, '/admins/liste'),
  } 
};

export const PATH_DOCS = 'https://docs-minimals.vercel.app/introduction';
