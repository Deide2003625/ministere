/* eslint-disable dot-notation */
import axios from "axios";

export const apiBaseURL = "http://127.0.0.1:8000"

const API = axios.create({
  baseURL: apiBaseURL,
  headers: {
    Accept: "application/json",
    // "Content-Type": "multipart/form-data",
  },
  withCredentials : true
  
});

export const authenticateApp = () => {
  API
    .get('/sanctum/csrf-cookie')
    .then((response) => console.log(response))
    .catch((error) => console.log(error));
};

const user = localStorage.getItem("user")
const userDataParsed = user ? JSON.parse(user) : null

API.interceptors.request.use(
  (request) => {
    if(userDataParsed && userDataParsed.token)
    {
      request.headers.Authorization = `Bearer ${userDataParsed.token}`
      // console.log(request)
    }
    return request
  }
    
  ,
  (error) => Promise.reject(error)
);


export const getRequest = (url) => API.get(url)

export const postRequest = (url, payload) => API.post(url, payload)

export const putRequest = (url, payload) => API.put(url, payload)

export const deleteRequest = (url) => API.delete(url)
