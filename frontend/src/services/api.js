import axios from 'axios';
import qs from 'qs';

const API_URL = 'http://localhost:8081/api';

const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
  },
});

apiClient.interceptors.request.use(config => {
    if (config.data) config.data = qs.stringify(config.data);
    return config;
  },
  (error) => Promise.reject(error)
);

apiClient.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

apiClient.interceptors.response.use(
  response => {
    const body = response.data;
    if (body?.code === 200) {
      return body.data;
    } else if (body?.code === 401) {
      localStorage.removeItem('token');
      window.location.reload();
    } else if (body?.code === 500 || body?.code === 403) {
      alert(body?.data);
    } else {
      const message = body?.data || `API returned code ${body?.code}`;
      console.error(`API Error: ${message}`);
      return Promise.reject(new Error(body?.code));
    }
  },
  error => {
    if (error.response) {
      const message = error.response.data.message || error.response.statusText;
      console.error(`API Error: ${message}`);
      return Promise.reject(new Error(message));
    } else if (error.request) {
      console.error('No response from server');
      return Promise.reject(new Error('No response from server'));
    } else {
      return Promise.reject(new Error(error.message));
    }
  }
);

export default apiClient;