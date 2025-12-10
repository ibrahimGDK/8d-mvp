// src/hooks/useCauses.js
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { CausesApi } from "../api/causesApi";

export const useCausesByProblem = (problemId) => {
  return useQuery({
    queryKey: ["causes", problemId],
    queryFn: async () => {
      const res = await CausesApi.getByProblem(problemId);
      return res.data; // sadece array döndür
    },
    enabled: !!problemId,
  });
};

export const useCreateCause = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (data) => CausesApi.create(data),
    onSuccess: (response, variables) => {
      console.log("Mutation Response:", response);
      console.log("Mutation Variables:", variables);
      const problemId = variables?.problem_id; // backend'e gönderdiğin doğru alan
      if (problemId) {
        console.log("Invalidating Query for Problem ID:", problemId);
        queryClient.invalidateQueries(["causes", problemId]);
      }
    },
  });
};

export const useUpdateCause = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ id, data }) => CausesApi.update(id, data),
    onSuccess: (_, variables) => {
      const problemId = variables?.problemId;
      if (problemId) queryClient.invalidateQueries(["causes", problemId]);
    },
  });
};

export const useDeleteCause = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ id }) => CausesApi.delete(id),
    onSuccess: (_, variables) => {
      const problemId = variables?.problemId;
      if (problemId) queryClient.invalidateQueries(["causes", problemId]);
    },
  });
};

export const useMarkAsRoot = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ id, is_root_cause }) =>
      CausesApi.markRoot(id, is_root_cause),
    onSuccess: (_, variables) => {
      const problemId = variables?.problemId;
      if (problemId) queryClient.invalidateQueries(["causes", problemId]);
    },
  });
};

export const useSaveActionPlan = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ id, plan }) => CausesApi.saveActionPlan(id, plan),
    onSuccess: (_, variables) => {
      const problemId = variables?.problemId;
      if (problemId) queryClient.invalidateQueries(["causes", problemId]);
    },
  });
};
