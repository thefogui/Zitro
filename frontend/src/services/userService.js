import apiClient from './api';

export const getUsers = async () => {
  return await apiClient.get(`/user/user/list`);
};

export const getUser = async (id) => {
  return apiClient.get(`/user/user/get/${id}`);
};

export const createUser = async (user) => {
  return apiClient.post(`/user/user/create`, user);
};

export const updateUser = async (id, user) => {
  return apiClient.post(`/user/user/update/${id}`, user);
};

export const deleteUser = async (id) => {
  return apiClient.delete(`/user/user/delete/${id}`);
};
