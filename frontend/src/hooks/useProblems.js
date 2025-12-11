// React Query kullanarak Problem verilerini çekme ve yönetme hook'ları

import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { ProblemsApi } from "../api/problemsApi";

// Tüm problemleri çek
export const useProblemList = () => {
  return useQuery({
    queryKey: ["problems"],
    queryFn: async () => {
      const res = await ProblemsApi.getAll();
      return res.data?.data || [];
    },
  });
};

// Tek bir problem detayını çek
export const useProblemQuery = (id) => {
  return useQuery({
    queryKey: ["problems", id],
    queryFn: async () => {
      const res = await ProblemsApi.getById(id);
      return res.data;
    },
    enabled: !!id,
  });
};

// Yeni problem oluşturma
export const useCreateProblem = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ProblemsApi.create,
    onSuccess: () => {
      queryClient.invalidateQueries(["problems"]);
    },
  });
};

// Mevcut problemi güncelleme
export const useUpdateProblem = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ id, data }) => ProblemsApi.update(id, data),
    onSuccess: (data, variables) => {
      queryClient.invalidateQueries(["problems"]);
      queryClient.invalidateQueries(["problems", variables.id]);
    },
  });
};

// Problem silme
export const useDeleteProblem = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (id) => ProblemsApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries(["problems"]);
    },
  });
};
