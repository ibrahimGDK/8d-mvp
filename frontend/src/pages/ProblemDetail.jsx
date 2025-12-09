// src/pages/ProblemDetail.jsx
import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getProblem } from "../api/api";
import RootCauseTree from "../components/RootCauseTree";
import { IxButton } from "@siemens/ix-react";

export default function ProblemDetail() {
  const { id } = useParams();
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedTab, setSelectedTab] = useState(0);

  const load = async () => {
    setLoading(true);
    try {
      const res = await getProblem(id);
      // backend yapısına göre res.data.data bekleniyor
      setData(res.data?.data || null);
    } catch (error) {
      console.error("Detay yüklenemedi", error);
      setData(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  if (loading) return <div style={{ padding: 20 }}>Yükleniyor...</div>;
  if (!data) return <div style={{ padding: 20 }}>Problem bulunamadı.</div>;

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
        <ix-typography format="h3">{data.problem.title}</ix-typography>
        <div
          style={{
            display: "flex",
            gap: "10px",
            alignItems: "center",
            marginTop: "5px",
          }}
        >
          <span className="ix-typography color-secondary">
            ID: #{data.problem.id}
          </span>
          <span className="ix-typography color-secondary">|</span>
          <span className="ix-typography color-secondary">
            Ekip: {data.problem.responsible_team}
          </span>
        </div>
      </div>

      {/* Sekmeler (Tabs) */}
      <ix-tabs>
        <ix-tab-item onClick={() => setSelectedTab(0)} icon="info">
          Genel Bakış
        </ix-tab-item>
        <ix-tab-item onClick={() => setSelectedTab(1)} icon="tree">
          D4: Kök Neden
        </ix-tab-item>
        <ix-tab-item onClick={() => setSelectedTab(2)} icon="task">
          D5: Aksiyonlar
        </ix-tab-item>
      </ix-tabs>

      {/* İçerik Alanı */}
      <div style={{ marginTop: "1.5rem" }}>
        {/* TAB 0: Genel Bakış */}
        {selectedTab === 0 && (
          <ix-card>
            <ix-card-content>
              <ix-typography format="h5" style={{ marginBottom: "10px" }}>
                Problem Tanımı
              </ix-typography>
              <p className="ix-typography">{data.problem.description}</p>
            </ix-card-content>
          </ix-card>
        )}

        {/* TAB 1: Kök Neden Analizi */}
        {selectedTab === 1 && (
          <ix-card style={{ width: "100%" }}>
            <ix-card-content>
              <ix-typography format="h5" style={{ marginBottom: "1rem" }}>
                Neden-Neden Analizi (Ishikawa / Root Cause Tree)
              </ix-typography>

              <RootCauseTree
                problemId={data.problem.id}
                causes={data.causes_tree || []}
                onChange={load} // tree'de değişiklik olursa tekrar yükle
              />
            </ix-card-content>
          </ix-card>
        )}

        {/* TAB 2: Aksiyonlar (Placeholder: burada daha detaylı listelenebilir) */}
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
