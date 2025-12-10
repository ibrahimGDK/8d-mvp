import api from "./axiosInstance";

export const ProblemsApi = {
  getAll: () => api.get("/problems"),
  getById: (id) => api.get(`/problems/${id}`),
  create: (data) => api.post("/problems", data),
  update: (id, data) => api.patch(`/problems/${id}`, data),
  delete: (id) => api.delete(`/problems/${id}`),
};
