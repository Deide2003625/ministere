/* eslint-disable camelcase */
import { createContext, useEffect, useReducer } from 'react';
import PropTypes from 'prop-types';
// utils
import axios from '../utils/axios';
import { isValidToken, setSession } from '../utils/jwt';

// ----------------------------------------------------------------------

const initialState = {
  isAuthenticated: false,
  isInitialized: false,
  user: null,
};

const handlers = {
  INITIALIZE: (state, action) => {
    const { isAuthenticated, user } = action.payload;
    return {
      ...state,
      isAuthenticated,
      isInitialized: true,
      user,
    };
  },
  LOGIN: (state, action) => {
    const { user } = action.payload;

    return {
      ...state,
      isAuthenticated: true,
      user,
    };
  },
  LOGOUT: (state) => ({
    ...state,
    isAuthenticated: false,
    user: null,
  }),
  SIGNUP: (state, action) => {
    const { user } = action.payload;

    return {
      ...state,
      isAuthenticated: true,
      user,
    };
  },
};

const reducer = (state, action) => (handlers[action.type] ? handlers[action.type](state, action) : state);

const AuthContext = createContext({
  ...initialState,
  login: () => Promise.resolve(),
  logout: () => Promise.resolve(),
  signUp: () => Promise.resolve(),
});

// ----------------------------------------------------------------------

AuthProvider.propTypes = {
  children: PropTypes.node,
};

function AuthProvider({ children }) {
  const [state, dispatch] = useReducer(reducer, initialState);

  useEffect(() => {
    const initialize = async () => {
      try {
        const token = window.localStorage.getItem('token');

        if (token) {
          setSession(token);

          /* const response = await axios.get('/api/account/my-account');
          const { user } = response.data; */

          dispatch({
            type: 'INITIALIZE',
            payload: {
              isAuthenticated: true,
              // user,
            },
          });
        } else {
          dispatch({
            type: 'INITIALIZE',
            payload: {
              isAuthenticated: false,
              user: null,
            },
          });
        }
      } catch (err) {
        console.error(err);
        dispatch({
          type: 'INITIALIZE',
          payload: {
            isAuthenticated: false,
            user: null,
          },
        });
      }
    };

    initialize();
  }, []);

  const login = async (email, password) => {
    const response = await axios.post('/api/login', {
      email,
      password,
    });
    const { token, ...user } = response.data;
      // console.log(response)
    // console.log(token);
    // console.log(user);
    if (token) {      
      setSession(token);
      alert(user.firstname)
      dispatch({
        type: 'LOGIN',
        payload: {
          user,
        },
      });
    }
    else{
      return "Error"
    }

    /*  */
  };

  const signUp = async (registration_number,firstname, surname, email, telephone, role, status) => {
    const response = await axios.post('/api/sign_up', {
      registration_number,
      firstname,
      surname,
      email,
      telephone,
      role,
      status
    });
    const { inserted } = response.data;

    if (inserted === "NO") {
      return "Error"
    }
  };

  const logout = async () => {
    setSession(null);
    dispatch({ type: 'LOGOUT' });
  };

  return (
    <AuthContext.Provider
      value={{
        ...state,
        login,
        logout,
        signUp,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export { AuthContext, AuthProvider };
