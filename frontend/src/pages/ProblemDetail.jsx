// Belirli bir problemi ve ona ait nedenleri (cause) detaylı olarak gösterir
// Kullanıcı kök nedenleri yönetebilir, yeni neden ekleyebilir, silme ve aksiyon planı kaydedebilir
import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { useProblemQuery } from "../hooks/useProblems";
import {
  useCausesByProblem,
  useCreateCause,
  useUpdateCause,
  useDeleteCause,
  useMarkAsRoot,
  useSaveActionPlan,
} from "../hooks/useCauses";
import RootCauseTree from "../components/RootCauseTree";
import { IxButton } from "@siemens/ix-react";

export default function ProblemDetail() {
  const { id } = useParams();
  const { data: problemResponse, isLoading: problemLoading } =
    useProblemQuery(id);
  
    // Problem ile ilişkili causes çek
  const {
    data: causesResponse,
    isLoading: causesLoading,
    refetch: refetchCauses,
  } = useCausesByProblem(id);

  const createMutation = useCreateCause();
  const updateMutation = useUpdateCause();
  const deleteMutation = useDeleteCause();
  const markRootMutation = useMarkAsRoot();
  const saveActionMutation = useSaveActionPlan();

  const [selectedTab, setSelectedTab] = useState(0);

  if (problemLoading || causesLoading)
    return <div style={{ padding: 20 }}>Yükleniyor...</div>;

  const problem = problemResponse?.data?.problem;
  if (!problem) return <div style={{ padding: 20 }}>Problem bulunamadı.</div>;

  const causes = causesResponse?.data || [];

  const handleAddCause = async (parentId, title, problemId) => {
    if (!title || !title.trim()) {
      alert("Lütfen bir başlık giriniz.");
      return;
    }

    const payload = {
      title: title.trim(),
      problem_id: problem.id,
      parent_id: parentId === "root" ? null : parentId,
      problemId: problemId ?? problem.id,
    };
    await createMutation.mutateAsync(payload);
    await refetchCauses();
  };

  const handleDeleteCause = async (causeId) => {
    await deleteMutation.mutateAsync({ id: causeId, problemId: problem.id });
    await refetchCauses();
  };

  const handleMarkRoot = async (causeId, isRoot) => {
    await markRootMutation.mutateAsync({
      id: causeId,
      problemId: problem.id,
      is_root_cause: isRoot,
    });
    await refetchCauses();
  };

  const handleSaveAction = async (causeId, actionText) => {
    await saveActionMutation.mutateAsync({
      id: causeId,
      plan: actionText,
      problemId: problem.id,
    });
    await refetchCauses();
  };

  const handleUpdateCause = async (causeId, data) => {
    await updateMutation.mutateAsync({
      id: causeId,
      data,
      problemId: problem.id,
    });
    await refetchCauses();
  };

  return (
    <div style={{ padding: "20px" }}>
      {/* Başlık Alanı */}
      <div
        style={{
          marginBottom: "1.5rem",
          borderBottom: "1px solid #ccc",
          paddingBottom: "1rem",
        }}
      >
        <ix-typography format="h3">{problem.title}</ix-typography>
        <div
          style={{
            display: "flex",
            gap: "10px",
            alignItems: "center",
            marginTop: "5px",
          }}
        >
          <span className="ix-typography color-secondary">
            ID: #{problem.id}
          </span>
          <span className="ix-typography color-secondary">|</span>
          <span className="ix-typography color-secondary">
            Ekip: {problem.responsible_team}
          </span>
        </div>
      </div>

      {/* Sekmeler */}
      <ix-tabs>
        <ix-tab-item onClick={() => setSelectedTab(0)} icon="info">
          Genel Bakış
        </ix-tab-item>
        <ix-tab-item onClick={() => setSelectedTab(1)} icon="tree">
          D4: Kök Neden
        </ix-tab-item>
      </ix-tabs>

      <div style={{ marginTop: "1.5rem" }}>
        {selectedTab === 0 && (
          <ix-card>
            <ix-card-content>
              <ix-typography format="h5" style={{ marginBottom: "10px" }}>
                Problem Tanımı (D1-D2)
              </ix-typography>
              <p className="ix-typography">{problem.description}</p>
            </ix-card-content>
          </ix-card>
        )}

        {selectedTab === 1 && (
          <ix-card style={{ width: "100%" }}>
            <ix-card-content>
              <ix-typography format="h5" style={{ marginBottom: "1rem" }}>
                Neden Analizi - Kök Neden Ağacı
              </ix-typography>

              <RootCauseTree
                problemId={problem.id}
                causes={causes}
                onAddCause={(parentId, title) =>
                  handleAddCause(parentId, title, problem.id)
                }
                onDeleteCause={handleDeleteCause}
                onMarkRoot={handleMarkRoot}
                onSaveAction={handleSaveAction}
                onUpdateCause={handleUpdateCause}
              />
            </ix-card-content>
          </ix-card>
        )}

        {selectedTab === 2 && (
          <ix-card>
            <ix-card-content>
              <ix-typography format="h5">Aksiyon Planları</ix-typography>
              <p>Henüz aksiyon eklenmemiş.</p>
              <IxButton icon="plus" variant="secondary">
                Aksiyon Ekle
              </IxButton>
            </ix-card-content>
          </ix-card>
        )}
      </div>
    </div>
  );
}
