// src/components/ProblemModal.jsx

import React, { useEffect, useState } from "react";
import {
  IxModal,
  IxModalHeader,
  IxModalContent,
  IxModalFooter,
  IxButton,
  IxInput,
  IxTextarea,
} from "@siemens/ix-react";

export default function ProblemModal({ editData = null, onSubmit, onClose }) {
  const [form, setForm] = useState({
    title: "",
    description: "",
    responsible_team: "",
  });

  const [loading, setLoading] = useState(false);

  const isEdit = !!editData;

  // Formu doldur
  useEffect(() => {
    if (isEdit) {
      setForm({
        title: editData.title || "",
        description: editData.description || "",
        responsible_team: editData.responsible_team || "",
      });
    } else {
      setForm({
        title: "",
        description: "",
        responsible_team: "",
      });
    }
  }, [isEdit, editData]);

  const handleSubmit = async () => {
    if (loading) return;

    setLoading(true);
    try {
      // Modal CRUD yapmaz → sadece formu parent’a gönderir
      if (typeof onSubmit === "function") {
        await onSubmit(form);
      }

      // success → modalı kapat
      if (typeof onClose === "function") onClose();
    } catch (err) {
      console.error("ProblemModal submit error:", err);
      alert("Kaydedilirken bir hata oluştu.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <IxModal onClose={onClose}>
      <IxModalHeader>
        {isEdit ? "Problemi Güncelle" : "Yeni Problem Ekle"}
      </IxModalHeader>

      <IxModalContent>
        <form style={{ display: "flex", flexDirection: "column", gap: "1rem" }}>
          <IxInput
            label="Başlık"
            value={form.title}
            onInput={(e) => setForm({ ...form, title: e.target.value })}
          />

          <IxTextarea
            label="Açıklama"
            value={form.description}
            onInput={(e) => setForm({ ...form, description: e.target.value })}
          />

          <IxInput
            label="Sorumlu Ekip"
            value={form.responsible_team}
            onInput={(e) =>
              setForm({ ...form, responsible_team: e.target.value })
            }
          />
        </form>
      </IxModalContent>

      <IxModalFooter>
        <IxButton outline onClick={onClose} disabled={loading}>
          İptal
        </IxButton>

        <IxButton onClick={handleSubmit} disabled={loading}>
          {loading
            ? isEdit
              ? "Güncelleniyor..."
              : "Kaydediliyor..."
            : isEdit
            ? "Güncelle"
            : "Kaydet"}
        </IxButton>
      </IxModalFooter>
    </IxModal>
  );
}
