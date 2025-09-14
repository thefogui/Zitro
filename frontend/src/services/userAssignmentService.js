import apiClient from './api';

export const assignDepartment= async (body) => {
  return apiClient.post(`/user/assignment/assign`, body);
};

export const revokeDepartment = async (assignmentId) => {
  return apiClient.delete(`/user/assignment/revoke/${assignmentId}`);
};

export const checkDepartment = async (body) => {
  return apiClient.get(`/user/assignment/check`, body);
};