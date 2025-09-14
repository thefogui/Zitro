import apiClient from './api';

export const getApps = async () => {
  return await apiClient.get(`/app/app/list`);
};

export const getApp = async (id) => {
  return apiClient.get(`/app/app/get/${id}`);
};

export const createApp = async (app) => {
  return apiClient.post(`/app/app/create`, app);
};

export const updateApp = async (id, app) => {
  return apiClient.post(`/app/app/update/${id}`, app);
};

export const deleteApp = async (id) => {
  return apiClient.delete(`/app/app/delete/${id}`);
};
