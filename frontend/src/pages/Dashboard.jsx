// src/pages/Dashboard.jsx
import { useEffect, useState } from "react";
import { getProblems } from "../api/api";
import { IxButton } from "@siemens/ix-react";
import { openProblemModal } from "../components/ModalService";
import ProblemTable from "../components/ProblemTable";

export default function Dashboard() {
  const [problems, setProblems] = useState([]);

  const load = async () => {
    try {
      const res = await getProblems();
      // backend response shape: res.data.data bekleniyor (senin koduna göre)
      setProblems(res.data?.data || []);
    } catch (e) {
      console.error("API Hatası:", e);
    }
  };

  useEffect(() => {
    load();
  }, []);

  // Yeni problem (create)
  const handleCreate = () => openProblemModal(load);

  // Var olan problemi düzenle (ProblemTable çağıracak editData ile)
  const handleEdit = (problem) => openProblemModal(load, problem);

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

      <ProblemTable
        problems={problems}
        reload={load}
        openEditModal={handleEdit}
      />
    </div>
  );
}
