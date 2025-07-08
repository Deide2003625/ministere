import * as Yup from 'yup';
import { useSnackbar } from 'notistack';
import { useCallback, useEffect, useState } from 'react';
// form
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
// @mui
import { Box, Grid, Card, Stack, Typography } from '@mui/material';
import { LoadingButton } from '@mui/lab';
// hooks
import { useNavigate } from 'react-router-dom';
import useAuth from '../../../../hooks/useAuth';

// utils
import { fData } from '../../../../utils/formatNumber';
// components
import { FormProvider, RHFTextField, RHFUploadAvatar } from '../../../../components/hook-form';
import { PATH_DASHBOARD } from '../../../../routes/paths';
import { postRequest } from '../../../../utils/api';

// ----------------------------------------------------------------------

export default function RegisterAnAdmin() {
  const [forceUpdate, setForceUpdate] = useState(false);

  const navigate = useNavigate();

  const { enqueueSnackbar } = useSnackbar();

  const UpdateUserSchema = Yup.object().shape({
    surname: Yup.string().required('Saisissez un nom'),
    firstname: Yup.string().required('Saisissez au moins un prénom'),
    email: Yup.string().required('Saisissez une adresse mail').email('Veuillez saisir une adresse email valide'),
    telephone: Yup.string().required('Saisissez un numéro de téléphone'),
  });

  const defaultValues = {
    firstname: '',
    surname: '',
    email: '',
    telephone: '',
    matricule: '',
    role: '',
    // avatarUrl: '',
  };

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

  const onSubmit = async (data) => {

    const dataObject = {
      firstname: data.firstname,
      surname: data.surname,
      telephone: data.telephone,
      email: data.email,
      registration_number: data.matricule,
      role: data.role,
    };
    // console.log(dataObject);
    try {
      const response = await postRequest("api/sign_up", dataObject)
      // console.log(response);
      if(response){
        reset()
        enqueueSnackbar('Enregistrement réussi !');
        navigate(PATH_DASHBOARD.admins.listAdmins);
      }
    } catch (error) {
      console.error(error);
    }
  };

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
    <FormProvider methods={methods} onSubmit={handleSubmit(onSubmit)}>
      <Grid container spacing={3}>
        <Grid item xs={12} md={4}>
          <Card sx={{ py: 8, px: 3, textAlign: 'center' }}>
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
              <RHFTextField name="surname" label="Nom" />
              <RHFTextField name="firstname" label="Prénoms" />

              <RHFTextField name="telephone" label="Numéro de téléphone" />
              <RHFTextField name="email" label="Adresse Email" />

              <RHFTextField name="matricule" label="Numéro Matricule" />
              <RHFTextField name="role" label="Rôle" />
            </Box>

            <Stack spacing={3} alignItems="flex-end" sx={{ mt: 3 }}>
              <LoadingButton type="submit" variant="contained" loading={isSubmitting}>
                Enregistrer
              </LoadingButton>
            </Stack>
          </Card>
        </Grid>
      </Grid>
    </FormProvider>
  );
}
