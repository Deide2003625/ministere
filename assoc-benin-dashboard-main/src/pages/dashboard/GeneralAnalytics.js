/* eslint-disable no-unneeded-ternary */
// @mui
import { Grid, Container, Typography } from '@mui/material';
// hooks
import { useEffect, useState } from 'react';
import useSettings from '../../hooks/useSettings';
// components
import Page from '../../components/Page';
// sections
import {
  AnalyticsWidgetSummary,
} from '../../sections/@dashboard/general/analytics';
import { getRequest } from '../../utils/api';

// ----------------------------------------------------------------------

export default function GeneralAnalytics() {
  const { themeStretch } = useSettings();

  const [pending, setPending] = useState();
  const [completed, setCompleted] = useState();

  
  useEffect(() => {
    const fetchUsers = async () => {
      try{
        const response = await getRequest('/api/count_requests');
        // console.log(response)
        setPending(response.data ? response.data.in_progress : 0);
        setCompleted(response.data ? response.data.approved : 0);
      }
      catch(error){
        console.log(error)
      }
    };

    fetchUsers()
    
  }, []);

  return (
    <Page title="Statistiques">
      <Container maxWidth={themeStretch ? false : 'xl'}>
        <Typography variant="h4" sx={{ mb: 5 }}>
          Statistique des demandes
        </Typography>

        <Grid container spacing={3}>
          <Grid item xs={12} sm={12} md={6}>
            <AnalyticsWidgetSummary title="En attente" total={pending ? pending : null} icon={'material-symbols:pending-actions'} />
          </Grid>

          <Grid item xs={12} sm={12} md={6}>
            <AnalyticsWidgetSummary title="Déjà traités" total={completed ? completed : null} color="info" icon={'material-symbols:library-add-check'} />
          </Grid>

          
        </Grid>
      </Container>
    </Page>
  );
}
