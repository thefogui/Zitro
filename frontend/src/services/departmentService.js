import apiClient from './api';

export const getDepartments = async () => {
  return apiClient.get(`/user/department/list`);
};

export const getDepartment = async (id) => {
  return apiClient.get(`/user/department/get/${id}`);
};

export const createDepartment = async (department) => {
  return apiClient.post(`/user/department/create`, department);
};

export const updateDepartment = async (id, department) => {
  return apiClient.post(`/user/department/update/${id}`, department);
};

export const deleteDepartment = async (id) => {
  return apiClient.delete(`/user/department/delete/${id}`);
};
