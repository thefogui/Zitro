import apiClient from './api';

export const needConfiguration = async () => {
  return await apiClient.get(`/user/admin/configure`);
};

export const makeUserAdmin = async (user) => {
  return await apiClient.post(`/user/admin/add`, user);
};

export const removeAdminFromUser = async (user) => {
  return await apiClient.post(`/user/admin/remove`, user);
};
