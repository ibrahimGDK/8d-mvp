// src/pages/Dashboard.jsx

import { IxButton } from "@siemens/ix-react";
import ProblemTable from "../components/ProblemTable";
import { openProblemModal } from "../components/ModalService";
import {
  useProblemList,
  useCreateProblem,
  useUpdateProblem,
} from "../hooks/useProblems";

export default function Dashboard() {
  const { data: problems, isLoading: loading } = useProblemList();
  const createProblemMutation = useCreateProblem();
  const updateProblemMutation = useUpdateProblem();

  const handleCreate = () =>
    openProblemModal(async (form) => {
      await createProblemMutation.mutateAsync(form);
    });

  const handleEdit = (existingProblem) =>
    openProblemModal(async (form) => {
      await updateProblemMutation.mutateAsync({
        id: existingProblem.id,
        data: form,
      });
    }, existingProblem);

  return (
    <div style={{ padding: "2rem" }}>
      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          marginBottom: "1.5rem",
        }}
      >
        <h2>8D Problem Listesi</h2>
        <IxButton icon="plus" onClick={handleCreate}>
          Yeni Problem
        </IxButton>
      </div>

      <ProblemTable problems={problems} openEditModal={handleEdit} />

      {loading && <div>Yükleniyor...</div>}
    </div>
  );
}
