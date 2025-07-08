import { useEffect, useState } from 'react';
import { Container, Tab, Box, Tabs } from '@mui/material';
import { useParams } from 'react-router';
import ViewAdminProfile from '../../sections/@dashboard/user/account/ViewAdminProfile';
import useSettings from '../../hooks/useSettings';
import Page from '../../components/Page';
import Iconify from '../../components/Iconify';
// sections
import RegisterAnAdmin from '../../sections/@dashboard/user/account/RegisterAnAdmin';
import { getRequest } from '../../utils/api';

// ----------------------------------------------------------------------

export default function UserAccount() {
  const [userInfos, setUserInfos] = useState();
  const { themeStretch } = useSettings();
  const { regCode } = useParams();

  const [currentTab, setCurrentTab] = useState('Informations Générales');

  const ACCOUNT_TABS = [
    {
      value: 'Informations Générales',
      icon: <Iconify icon={'ic:round-account-box'} width={20} height={20} />,
      component: <RegisterAnAdmin />,
    },
  ];

  const ACCOUNT_TABS_VIEW_ADMIN_PROFILE = [
    {
      value: 'Informations Générales',
      icon: <Iconify icon={'ic:round-account-box'} width={20} height={20} />,
      component: <ViewAdminProfile adminData={userInfos} />,
    },
  ];

  useEffect(() => {
    if (regCode) {
      const getAdminData = async () => {
        const adminData = await getRequest(`/api/get_admin/${regCode}`);
        setUserInfos(adminData.data.admin);
      };
      getAdminData();
    }
  }, []);

  return (
    <Page title="Enregistrer un admin">
      <Container maxWidth={themeStretch ? false : 'lg'}>
        <Tabs
          value={currentTab}
          scrollButtons="auto"
          variant="scrollable"
          allowScrollButtonsMobile
          onChange={(e, value) => setCurrentTab(value)}
        >
          {ACCOUNT_TABS.map((tab) => (
            <Tab disableRipple key={tab.value} label={tab.value} icon={tab.icon} value={tab.value} />
          ))}
        </Tabs>

        <Box sx={{ mb: 5 }} />

        {userInfos
          ? ACCOUNT_TABS_VIEW_ADMIN_PROFILE.map((tab) => {
              const isMatched = tab.value === currentTab;
              return (
                isMatched && (
                  <Box key={tab.value}>
                    {tab.component}
                  </Box>
                )
              );
            })
          : ACCOUNT_TABS.map((tab) => {
              const isMatched = tab.value === currentTab;
              return isMatched && <Box key={tab.value}>{tab.component}</Box>;
            })}
      </Container>
    </Page>
  );
}
