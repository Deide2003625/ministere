import { Suspense, lazy } from 'react';
import { Navigate, useRoutes, useLocation } from 'react-router-dom';
// layouts
import DashboardLayout from '../layouts/dashboard';
import LogoOnlyLayout from '../layouts/LogoOnlyLayout';
// guards
import GuestGuard from '../guards/GuestGuard';
import AuthGuard from '../guards/AuthGuard';
// import RoleBasedGuard from '../guards/RoleBasedGuard';
// config
import { PATH_AFTER_LOGIN } from '../config';
// components
import LoadingScreen from '../components/LoadingScreen';

// ----------------------------------------------------------------------

const Loadable = (Component) => (props) => {
  // eslint-disable-next-line react-hooks/rules-of-hooks
  const { pathname } = useLocation();

  return (
    <Suspense fallback={<LoadingScreen isDashboard={pathname.includes('/dashboard')} />}>
      <Component {...props} />
    </Suspense>
  );
};

export default function Router() {
  return useRoutes([
    {
      path: 'auth',
      children: [
        {
          path: 'login',
          element: (
            <Login />
            // <GuestGuard>
            // </GuestGuard>
          ),
        },
        { path: 'login-unprotected', element: <Login /> },
        { path: 'forgot-password', element: <ForgotPassword /> },
      ],
    },

    // Dashboard Routes
    {
      path: 'dashboard',
      element: (
        <DashboardLayout />
        // <AuthGuard>
        // </AuthGuard>
      ),
      children: [
        { element: <Navigate to={PATH_AFTER_LOGIN} replace />, index: true },
        { path: 'app', element: <GeneralAnalytics /> },
        { path: 'statistiques', element: <GeneralAnalytics /> },
        { path: 'profil-admin', element: <UserAccount /> },
        {
          path: 'demandes',
          children: [
            { path: 'en-attente', element: <PendingDemands /> },
            { path: 'en-attente/:assocCode', element: <EcommerceInvoice /> },
            { path: 'traitees', element: <TreatedDemands /> },
            { path: 'traitees/:assocCode', element: <EcommerceInvoice /> },
          ],
        },
        {
          path: 'admins',
          children: [
            { path: 'enregistrement', element: <AddAdmin reloadDocument /> },
            { path: 'liste', element: <AdminsList /> },
            { path: 'liste/:regCode', element: <AddAdmin /> },
          ],
        }
      ],
    },

    // Main Routes
    {
      path: '*',
      element: <LogoOnlyLayout />,
      children: [
        { path: '500', element: <Page500 /> },
        { path: '404', element: <NotFound /> },
        { path: '*', element: <Navigate to="/404" replace /> },
      ],
    },
    {
      path: '/',
      element: <Login />,
    },
    { path: '*', element: <Navigate to="/404" replace /> },
  ]);
}

// IMPORT COMPONENTS

const GeneralAnalytics = Loadable(lazy(() => import('../pages/dashboard/GeneralAnalytics')));
// Authentication
const Login = Loadable(lazy(() => import('../pages/auth/Login')));
const ForgotPassword = Loadable(lazy(() => import('../pages/auth/ForgotPassword')));
// Dashboard
const EcommerceInvoice = Loadable(lazy(() => import('../pages/dashboard/EcommerceInvoice')));
const BlogNewPost = Loadable(lazy(() => import('../pages/dashboard/BlogNewPost')));
const PendingDemands = Loadable(lazy(() => import('../pages/dashboard/PendingDemands')));
const TreatedDemands = Loadable(lazy(() => import('../pages/dashboard/TreatedDemands')));
const UserAccount = Loadable(lazy(() => import('../pages/dashboard/UserAccount')));
const AddAdmin = Loadable(lazy(() => import('../pages/dashboard/AddAdmin')));
const AdminsList = Loadable(lazy(() => import('../pages/dashboard/AdminsList')));
// Main
const Page500 = Loadable(lazy(() => import('../pages/Page500')));
const NotFound = Loadable(lazy(() => import('../pages/Page404')));
