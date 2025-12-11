// Ana dashboard sayfası, problem listesini gösterir ve CRUD işlemlerini başlatır

import { IxButton } from "@siemens/ix-react";
import ProblemTable from "../components/ProblemTable";
import { openProblemModal } from "../components/ModalService";
import {
  useProblemList,
  useCreateProblem,
  useUpdateProblem,
} from "../hooks/useProblems";

export default function Dashboard() {
  // Tüm problem listesini çek
  const { data: problems, isLoading: loading } = useProblemList();
  // Tüm problem listesini çek
  const createProblemMutation = useCreateProblem();

  // Mevcut problemi güncellemek için mutation
  const updateProblemMutation = useUpdateProblem();

  // "Yeni Problem" butonuna tıklandığında modal aç ve formu submit et
  const handleCreate = () =>
    openProblemModal(async (form) => {
      await createProblemMutation.mutateAsync(form);
    });

  // "Güncelle" işlemi: modal aç ve mevcut problem ile formu submit et
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
        <h2>8D Problem Listesi (D1-D2)</h2>
        <IxButton icon="plus" onClick={handleCreate}>
          Yeni Problem
        </IxButton>
      </div>

      <ProblemTable problems={problems} openEditModal={handleEdit} />

      {loading && <div>Yükleniyor...</div>}
    </div>
  );
}
