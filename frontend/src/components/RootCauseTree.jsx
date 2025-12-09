// src/components/RootCauseTree.jsx

import React, { useState } from "react";
import {
  IxButton,
  IxIcon,
  IxInput,
  // IxBadge,
  // IxTag,
  IxChip,
  IxTypography,
} from "@siemens/ix-react";
import { createCause, updateCause, deleteCause } from "../api/api";

/**
 * Beklenen `causes` formatı:
 * [
 *   {
 *     id,
 *     title,
 *     is_root_cause, // 0 veya 1
 *     action_plan, // string veya null
 *     children: [ ... ] // aynı yapı
 *   },
 *   ...
 * ]
 *
 * Props:
 * - problemId: integer/string (gerekli, yeni cause eklemek için)
 * - causes: dizi (root seviye)
 * - onChange: fonksiyon (backend'den yeniden yükleme için çağrılır)
 */
export default function RootCauseTree({ problemId, causes = [], onChange }) {
  // Local UI state: hangi node için açık "add child" input var, hangi node action edit modunda vb.
  const [addingFor, setAddingFor] = useState(null); // node id veya null
  const [newTitle, setNewTitle] = useState("");
  const [expanded, setExpanded] = useState({}); // { [id]: true }
  const [editingActionFor, setEditingActionFor] = useState(null);
  const [actionText, setActionText] = useState("");

  const toggleExpand = (id) => {
    setExpanded((s) => ({ ...s, [id]: !s[id] }));
  };

  // Helper: rekürsif tree içinde mevcut root cause olan node'ları bul
  const collectAllNodes = (nodes, acc = []) => {
    for (const n of nodes) {
      acc.push(n);
      if (n.children && n.children.length) collectAllNodes(n.children, acc);
    }
    return acc;
  };

  const handleAddChild = async (parentId) => {
    if (!newTitle || newTitle.trim() === "") {
      alert("Lütfen bir sebep başlığı girin.");
      return;
    }
    try {
      await createCause({
        problem_id: problemId,
        parent_id: parentId,
        title: newTitle.trim(),
      });
      setNewTitle("");
      setAddingFor(null);
      if (typeof onChange === "function") onChange();
    } catch (e) {
      console.error("Alt sebep eklenirken hata:", e);
      alert("Alt sebep eklenemedi. Lütfen tekrar deneyin.");
    }
  };

  const handleDelete = async (nodeId) => {
    if (
      !confirm(
        "Bu nedeni silmek istediğinize emin misiniz? Alt nedenler de silinebilir."
      )
    )
      return;
    try {
      await deleteCause(nodeId);
      if (typeof onChange === "function") onChange();
    } catch (e) {
      console.error("Sebep silinirken hata:", e);
      alert("Silme sırasında hata oluştu.");
    }
  };

  const handleMarkRoot = async (node) => {
    try {
      // 1) Önce varsa başka root cause'ları unset et (frontend'de gördüğümüz tüm node'ları dolaşıp)
      const all = collectAllNodes(causes);
      const previousRoots = all.filter(
        (n) => n.is_root_cause === 1 && n.id !== node.id
      );

      // Kök neden olarak setlenecek olan node'u 1 yap
      await updateCause(node.id, { is_root_cause: 1 });

      // Diğerlerini 0 yap (opsiyonel - backend kısıtlıysa gerek olmayabilir)
      for (const pr of previousRoots) {
        try {
          await updateCause(pr.id, { is_root_cause: 0 });
        } catch (e) {
          console.warn("Önceki kök neden unset edilirken hata:", e);
        }
      }

      if (typeof onChange === "function") onChange();
    } catch (e) {
      console.error("Kök neden işaretlenirken hata:", e);
      alert("Kök neden olarak işaretleme sırasında hata oluştu.");
    }
  };

  const startEditAction = (node) => {
    setEditingActionFor(node.id);
    setActionText(node.action_plan || "");
  };

  const saveAction = async (node) => {
    try {
      await updateCause(node.id, { action_plan: actionText });
      setEditingActionFor(null);
      setActionText("");
      if (typeof onChange === "function") onChange();
    } catch (e) {
      console.error("Aksiyon kaydedilirken hata:", e);
      alert("Aksiyon kaydedilemedi.");
    }
  };

  const renderNode = (node, depth = 0) => {
    const hasChildren = node.children && node.children.length > 0;
    const isExpanded = !!expanded[node.id];

    return (
      <li key={node.id} style={{ marginBottom: 8 }}>
        <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
          <div
            style={{ display: "flex", alignItems: "center", gap: 8, flex: 1 }}
          >
            {hasChildren && (
              <IxButton
                icon={isExpanded ? "chevron-down" : "chevron-right-small"}
                outline
                size="small"
                onClick={() => toggleExpand(node.id)}
              />
            )}
            <div
              style={{ display: "flex", flexDirection: "column", minWidth: 0 }}
            >
              <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
                <IxTypography
                  size="small"
                  style={{
                    whiteSpace: "nowrap",
                    overflow: "hidden",
                    textOverflow: "ellipsis",
                  }}
                >
                  <strong>{node.title}</strong>
                </IxTypography>

                {node.is_root_cause === 1 && (
                  <IxChip variant="success" size="sm">
                    Kök Neden
                  </IxChip>
                )}
              </div>

              {node.action_plan && (
                <div style={{ fontSize: 12, color: "#555", marginTop: 4 }}>
                  💡 Aksiyon: {node.action_plan}
                </div>
              )}
            </div>
          </div>

          {/* İşlem Butonları */}
          <div style={{ display: "flex", gap: 6 }}>
            <IxButton
              icon="plus"
              variant="secondary"
              title="Alt Sebep Ekle"
              onClick={() => {
                setAddingFor(node.id);
                setNewTitle("");
                toggleExpand(node.id); // otomatik aç
              }}
            />
            <IxButton
              icon="target"
              variant={node.is_root_cause === 1 ? "primary" : "outline"}
              title="Kök Neden Olarak İşaretle"
              onClick={() => handleMarkRoot(node)}
            />
            <IxButton
              icon="trash"
              variant="danger"
              title="Sil"
              onClick={() => handleDelete(node.id)}
            />
          </div>
        </div>

        {/* Eğer bu node için action edit moduna girildiyse */}
        {node.is_root_cause === 1 && (
          <div style={{ marginTop: 6, marginLeft: 36 }}>
            {editingActionFor === node.id ? (
              <div style={{ display: "flex", gap: 8, alignItems: "center" }}>
                <IxInput
                  label="Kalıcı Çözüm / Aksiyon"
                  value={actionText}
                  onInput={(e) => setActionText(e.target.value)}
                />
                <IxButton onClick={() => saveAction(node)}>Kaydet</IxButton>
                <IxButton outline onClick={() => setEditingActionFor(null)}>
                  İptal
                </IxButton>
              </div>
            ) : (
              <div style={{ display: "flex", gap: 8, alignItems: "center" }}>
                <IxButton icon="edit" onClick={() => startEditAction(node)}>
                  Aksiyon Ekle / Düzenle
                </IxButton>
              </div>
            )}
          </div>
        )}

        {/* Alt sebep ekleme alanı (inline) */}
        {addingFor === node.id && (
          <div
            style={{ marginTop: 8, marginLeft: 36, display: "flex", gap: 8 }}
          >
            <IxInput
              label="Yeni Sebep"
              value={newTitle}
              onInput={(e) => setNewTitle(e.target.value)}
            />
            <IxButton onClick={() => handleAddChild(node.id)}>Ekle</IxButton>
            <IxButton outline onClick={() => setAddingFor(null)}>
              İptal
            </IxButton>
          </div>
        )}

        {/* Çocuklar (rekürsif) */}
        {hasChildren && isExpanded && (
          <ul style={{ marginLeft: 20, marginTop: 8 }}>
            {node.children.map((c) => renderNode(c, depth + 1))}
          </ul>
        )}
      </li>
    );
  };

  return (
    <div>
      {/* Genel seviyede "Yeni Ana Sebep" ekleme */}
      <div style={{ marginBottom: 12, display: "flex", gap: 8 }}>
        <IxButton
          icon="plus"
          onClick={() => {
            setAddingFor("root");
            setNewTitle("");
          }}
        >
          Yeni Ana Sebep Ekle
        </IxButton>
        {addingFor === "root" && (
          <div
            style={{
              display: "flex",
              gap: 8,
              alignItems: "center",
              marginLeft: 8,
            }}
          >
            <IxInput
              label="Başlık"
              value={newTitle}
              onInput={(e) => setNewTitle(e.target.value)}
            />
            <IxButton
              onClick={async () => {
                if (!newTitle.trim()) {
                  alert("Başlık boş olamaz.");
                  return;
                }
                try {
                  await createCause({
                    problem_id: problemId,
                    parent_id: null,
                    title: newTitle.trim(),
                  });
                  setNewTitle("");
                  setAddingFor(null);
                  if (typeof onChange === "function") onChange();
                } catch (e) {
                  console.error("Ana sebep eklenemedi:", e);
                  alert("Eklenemedi.");
                }
              }}
            >
              Ekle
            </IxButton>
            <IxButton outline onClick={() => setAddingFor(null)}>
              İptal
            </IxButton>
          </div>
        )}
      </div>

      {/* Tree */}
      <ul style={{ paddingLeft: 0 }}>
        {causes.length === 0 ? (
          <div style={{ color: "#666" }}>Henüz bir sebep eklenmemiş.</div>
        ) : (
          causes.map((n) => renderNode(n))
        )}
      </ul>
    </div>
  );
}
