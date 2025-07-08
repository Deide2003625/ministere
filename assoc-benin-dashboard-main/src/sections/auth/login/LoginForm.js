import * as Yup from 'yup';
import { useEffect, useState } from 'react';
import { Link as RouterLink } from 'react-router-dom';
// form
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
// @mui
import { Link, Stack, Alert, IconButton, InputAdornment } from '@mui/material';
import { LoadingButton } from '@mui/lab';
// routes
import { authenticateApp } from '../../../utils/axios';
import { PATH_AUTH } from '../../../routes/paths';
// hooks
import useIsMountedRef from '../../../hooks/useIsMountedRef';
// components
import Iconify from '../../../components/Iconify';
import { FormProvider, RHFTextField, RHFCheckbox } from '../../../components/hook-form';
import { postRequest } from '../../../utils/api';
import { setSession } from '../../../utils/jwt';

// ----------------------------------------------------------------------

export default function LoginForm() {

  const isMountedRef = useIsMountedRef();

  const [showPassword, setShowPassword] = useState(false);

  const LoginSchema = Yup.object().shape({
    email: Yup.string().email('Veuillez entrer une adresse email valide').required("L'adresse email est requise"),
    password: Yup.string().required('Le mot de passe est requis'),
  });

  const defaultValues = {
    email: '',
    password: '',
    remember: true,
  };

  const methods = useForm({
    resolver: yupResolver(LoginSchema),
    defaultValues,
  });

  const {
    // reset,
    setError,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = methods;

  const onSubmit = async (data, e) => {
    e.preventDefault();
    // console.log(data);
    try {
      const response = await postRequest('/api/login', {
        email: data.email,
        password: data.password,
      });
      console.log(response);
      if (response.data.granted === 'YES') {
        setSession(response.data);
        window.location.href = '/dashboard/app';
      } else {
        setError('afterSubmit', 'error');
      }
    } catch (error) {
      console.error(error);
      if (isMountedRef.current) {
        setError('afterSubmit', error);
      }
    }
  };

  useEffect(() => {
    authenticateApp();
  }, []);

  return (
    <FormProvider methods={methods} onSubmit={handleSubmit(onSubmit)}>
      <Stack spacing={3}>
        {!!errors.afterSubmit && <Alert severity="error"> Adresse email ou mot de passe incorrect. </Alert>}

        <RHFTextField name="email" label="Adresse Email" />

        <RHFTextField
          name="password"
          label="Mot de passe"
          type={showPassword ? 'text' : 'password'}
          InputProps={{
            endAdornment: (
              <InputAdornment position="end">
                <IconButton onClick={() => setShowPassword(!showPassword)} edge="end">
                  <Iconify icon={showPassword ? 'eva:eye-fill' : 'eva:eye-off-fill'} />
                </IconButton>
              </InputAdornment>
            ),
          }}
        />
      </Stack>

      <Stack direction="row" alignItems="center" justifyContent="space-between" sx={{ my: 2 }}>
        <RHFCheckbox name="remember" label="Se souvenir de moi" />
        <Link component={RouterLink} variant="subtitle2" to={PATH_AUTH.ForgotPassword}>
          Mot de passe oublié ?
        </Link>
      </Stack>

      <LoadingButton fullWidth size="large" type="submit" variant="contained" loading={isSubmitting}>
        Se connecter
      </LoadingButton>
    </FormProvider>
  );
}
