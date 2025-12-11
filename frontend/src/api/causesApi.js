import api from "./axiosInstance";

// --- CAUSE API FONKSİYONLARI ---
export const CausesApi = {
  // Belirli probleme ait tüm nedenleri getir
  getByProblem: (problemId) => api.get(`/causes?problem_id=${problemId}`),
  
  // Tek bir cause kaydını getir
  getById: (id) => api.get(`/causes/${id}`),
  
  // Yeni cause oluştur
  create: (data) => api.post("/causes", data),
  
  // Cause güncelle
  update: (id, data) => api.put(`/causes/${id}`, data),
  
  // Cause sil
  delete: (id) => api.delete(`/causes/${id}`),

  // root cause işaretleme
  markRoot: (id, is_root_cause) => api.put(`/causes/${id}`, { is_root_cause }),

  // action plan kaydetme
  saveActionPlan: (id, plan) => api.put(`/causes/${id}`, { action_plan: plan }),
};
