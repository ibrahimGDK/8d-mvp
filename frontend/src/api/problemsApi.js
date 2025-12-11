import api from "./axiosInstance";
// --- PROBLEM API FONKSİYONLARI ---

export const ProblemsApi = {
  
  // Tüm problemleri getir
  getAll: () => api.get("/problems"),
  
  // Tek bir problemi getir
  getById: (id) => api.get(`/problems/${id}`),
  
  // Yeni problem oluştur
  create: (data) => api.post("/problems", data),

  // Problem güncelle (partial update - PATCH)
  update: (id, data) => api.patch(`/problems/${id}`, data),
  
  // Problem sil
  delete: (id) => api.delete(`/problems/${id}`),
};
