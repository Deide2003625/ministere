import axios from 'axios';

axios.defaults.withCredentials = true

const axiosInstance = axios.create({
  baseURL: 'http://127.0.0.1:8000',
  headers : {
    'X-Requested-With' : 'XMLHttpRequest'
  }
});

export const authenticateApp = () => {
  axios
    .get('http://127.0.0.1:8000/sanctum/csrf-cookie',{
      headers : {
        'X-Requested-With' : 'XMLHttpRequest'
      }
    })
    .then((response) => console.log(response))
    .catch((error) => console.log(error));
};

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => Promise.reject((error.response && error.response.data) || 'Something went wrong')
);

export default axiosInstance;
