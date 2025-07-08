/* eslint-disable no-unneeded-ternary */
import * as Yup from 'yup';
import React, { createContext, useEffect, useState } from 'react';
import { Link as RouterLink } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import { postRequest } from '../utils/api';
import { setSession } from '../utils/jwt';
import useIsMountedRef from '../hooks/useIsMountedRef';
// form

export const UserDataContext = createContext();

const UserProvider = ({ children, payload }) => {
  const [user, setUser] = useState()

  const isMountedRef = useIsMountedRef();

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
    formState: { errors, isSubmitting },
  } = methods;


  useEffect(
    () => async () => {
      try {
        const response = await postRequest('/api/login', payload);
        console.log(response);
        if (response.data.granted === 'YES') {
          setSession(response.data.token);
          setUser(response.data)
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
    },
    []
  );

  return <UserDataContext.Provider value={user ? user : null}>{children}</UserDataContext.Provider>;
};

export default UserProvider