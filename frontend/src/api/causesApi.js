import api from "./axiosInstance";

export const CausesApi = {
  getByProblem: (problemId) => api.get(`/causes?problem_id=${problemId}`),
  getById: (id) => api.get(`/causes/${id}`),

  create: (data) => api.post("/causes", data),
  update: (id, data) => api.put(`/causes/${id}`, data),
  delete: (id) => api.delete(`/causes/${id}`),

  // root cause işaretleme
  markRoot: (id, is_root_cause) => api.put(`/causes/${id}`, { is_root_cause }),

  // action plan kaydetme
  saveActionPlan: (id, plan) => api.put(`/causes/${id}`, { action_plan: plan }),
};
