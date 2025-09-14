import apiClient from './api';

export const getPositions = async () => {
  return apiClient.get(`/user/companyPosition/list`);
};

export const getPosition = async (id) => {
  return apiClient.get(`/user/companyPosition/get/${id}`);
};

export const createPosition = async (position) => {
  return apiClient.post(`/user/companyPosition/create`, position);
};

export const updatePosition = async (id, position) => {
  return apiClient.post(`/user/companyPosition/update/${id}`, position);
};

export const deletePosition = async (id) => {
  return apiClient.delete(`/user/companyPosition/delete/${id}`);
};