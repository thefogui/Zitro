import apiClient from './api';

export const register = async (user) => {
  return await apiClient.post(`/user/auth/register`, user);
};

export const login = async (user) => {
  return apiClient.post(`/user/auth/login`, user);
};

export const logout = async (user) => {
  return apiClient.post(`/user/auth/logout`, user);
};
