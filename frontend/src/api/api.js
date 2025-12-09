import axios from "axios";

// Vite proxy sayesinde artık sadece '/api' dememiz yeterli
const api = axios.create({
  baseURL: "/api",
  headers: {
    "Content-Type": "application/json",
  },
});

export const getProblems = () => api.get("/problems");
export const getProblem = (id) => api.get(`/problems/${id}`);
export const getCausesByProblem = (id) => api.get(`/causes?problem_id=${id}`);
export const createProblem = (data) => api.post("/problems", data);
export const updateProblem = (id, data) => api.put(`/problems/${id}`, data);
export const deleteProblem = (id) => api.delete(`/problems/${id}`);

export const createCause = (data) => api.post("/causes", data);
export const updateCause = (id, data) => api.put(`/causes/${id}`, data);
export const deleteCause = (id) => api.delete(`/causes/${id}`);

export default api;
