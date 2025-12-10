// hooks/useProblems.js
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { ProblemsApi } from "../api/problemsApi";

export const useProblemList = () => {
  return useQuery({
    queryKey: ["problems"],
    queryFn: async () => {
      const res = await ProblemsApi.getAll();
      // backend structure: res.data = { status: 'success', data: [...] }
      return res.data?.data || [];
    },
  });
};

export const useProblemQuery = (id) => {
  return useQuery({
    queryKey: ["problems", id],
    queryFn: async () => {
      const res = await ProblemsApi.getById(id);
      return res.data; // burası önemli: sadece problem objesini döndür
    },
    enabled: !!id,
  });
};

export const useCreateProblem = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ProblemsApi.create,
    onSuccess: () => {
      queryClient.invalidateQueries(["problems"]);
    },
  });
};

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

export const useDeleteProblem = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (id) => ProblemsApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries(["problems"]);
    },
  });
};
