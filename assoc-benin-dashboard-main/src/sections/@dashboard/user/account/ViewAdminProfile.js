import * as Yup from 'yup';
import { useSnackbar } from 'notistack';
import { useCallback, useEffect } from 'react';
// form
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
// @mui
import { Box, Grid, Card, Stack, Typography } from '@mui/material';
import { LoadingButton } from '@mui/lab';
// hooks
import { useNavigate } from 'react-router-dom';
import PropTypes from 'prop-types';
import useAuth from '../../../../hooks/useAuth';

// utils
import { fData } from '../../../../utils/formatNumber';
// components
import { FormProvider, RHFTextField, RHFUploadAvatar } from '../../../../components/hook-form';
import { PATH_DASHBOARD } from '../../../../routes/paths';
  
// ----------------------------------------------------------------------

ViewAdminProfile.propTypes = {
  adminData: PropTypes.object,
};

export default function ViewAdminProfile({adminData}) {
  const navigate = useNavigate();
  const { enqueueSnackbar } = useSnackbar();

  const UpdateUserSchema = Yup.object().shape({
    surname: Yup.string().required('Saisissez un nom'),
    firstname: Yup.string().required('Saisissez au moins un prénom'),
    email: Yup.string().required('Saisissez une adresse mail').email('Veuillez saisir une adresse email valide'),
    telephone: Yup.string().required('Saisissez un numéro de téléphone'),
  });

  const defaultValues = {
    firstname: adminData ? adminData.prenom : '',
    surname: adminData ? adminData.nom : '',
    email: adminData ? adminData.email : '',
    telephone: adminData ? adminData.telephone : '',
    matricule: adminData ? adminData.matricule : '',
    role: adminData ? adminData.role : '',
    // avatarUrl: '',
  };
  console.log(defaultValues);

  const methods = useForm({
    resolver: yupResolver(UpdateUserSchema),
    defaultValues,
  });

  const {
    reset,
    setValue,
    handleSubmit,
    formState: { isSubmitting },
  } = methods;

  const handleDrop = useCallback(
    (acceptedFiles) => {
      const file = acceptedFiles[0];

      if (file) {
        setValue(
          'avatarUrl',
          Object.assign(file, {
            preview: URL.createObjectURL(file),
          })
        );
      }
    },
    [setValue]
  );

  useEffect(() => reset(), [])

  return (
    <FormProvider methods={methods}>
      <Grid container spacing={3}>
        <Grid item xs={12} md={4}>
          <Card sx={{ py: 4.5, px: 3, textAlign: 'center' }}>
            <RHFUploadAvatar
              name="avatarUrl"
              accept="image/*"
              maxSize={3044253}
              onDrop={handleDrop}
              helperText={
                <Typography
                  variant="caption"
                  sx={{
                    mt: 2,
                    mx: 'auto',
                    display: 'block',
                    textAlign: 'center',
                    color: 'text.secondary',
                  }}
                >
                  Formats autorisés : *.jpeg, *.jpg, *.png, *.gif
                  <br /> Taille maximale de {fData(3044253)}
                </Typography>
              }
            />
          </Card>
        </Grid>

        <Grid item xs={12} md={8}>
          <Card sx={{ p: 3 }}>
            <Box
              sx={{
                display: 'grid',
                rowGap: 3,
                columnGap: 2,
                gridTemplateColumns: { xs: 'repeat(1, 1fr)', sm: 'repeat(2, 1fr)' },
              }}
            >
              <RHFTextField name="surname" label="Nom"/>
              <RHFTextField name="firstname" label="Prénoms" />

              <RHFTextField name="telephone" label="Numéro de téléphone" />
              <RHFTextField name="email" label="Adresse Email" />

              <RHFTextField name="matricule" label="Numéro Matricule" />
              <RHFTextField name="role" label="Rôle" />
            </Box>

          </Card>
        </Grid>
      </Grid>
    </FormProvider>
  );
}
