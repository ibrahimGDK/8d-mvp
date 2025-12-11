// src/components/RootCauseTree.jsx
import React, { useState } from "react";
import { IxButton, IxChip, IxInput, IxTypography } from "@siemens/ix-react";

/**
 * Props:
 * - problemId
 * - causes (array)
 * - onAddCause(parentId, title)
 * - onDeleteCause(causeId)
 * - onMarkRoot(causeId, newValue)
 * - onSaveAction(causeId, actionText)
 * - onUpdateCause(causeId, data)
 */
export default function RootCauseTree({
  problemId,
  causes = [],
  onAddCause,
  onDeleteCause,
  onMarkRoot,
  onSaveAction,
  onUpdateCause,
}) {
  const [addingFor, setAddingFor] = useState(null); // Hangi node için yeni sebep ekleniyor
  const [newTitle, setNewTitle] = useState(""); // Yeni sebep başlığı
  const [expanded, setExpanded] = useState({}); // Node genişletme durumu
  const [editingActionFor, setEditingActionFor] = useState(null); // Kök neden aksiyon düzenleme
  const [actionText, setActionText] = useState(""); // Düzenlenen aksiyon
  const [editingTitleFor, setEditingTitleFor] = useState(null); // Sebep başlığı düzenleme
  const [editedTitle, setEditedTitle] = useState(""); // Düzenlenen başlık

  // Node expand/collapse toggle
  const toggleExpand = (id) => setExpanded((s) => ({ ...s, [id]: !s[id] }));

  // Kök neden aksiyon düzenlemeyi başlat
  const startEditAction = (node) => {
    setEditingActionFor(node.id);
    setActionText(node.action_plan || "");
  };

  // Aksiyon kaydet
  const saveAction = async (node) => {
    if (typeof onSaveAction === "function") {
      await onSaveAction(node.id, actionText);
      setEditingActionFor(null);
      setActionText("");
    }
  };

  // Başlık düzenlemeyi başlat
  const startEditTitle = (node) => {
    setEditingTitleFor(node.id);
    setEditedTitle(node.title);
  };

  // Başlık kaydet
  const saveTitle = async (node) => {
    if (!editedTitle.trim()) {
      alert("Başlık boş olamaz.");
      return;
    }
    if (typeof onUpdateCause === "function") {
      await onUpdateCause(node.id, { title: editedTitle.trim() });
      setEditingTitleFor(null);
      setEditedTitle("");
    }
  };

  // Başlık kaydet
  const addChild = async (parentId) => {
    if (!newTitle.trim()) {
      alert("Lütfen bir başlık girin.");
      return;
    }
    if (typeof onAddCause === "function") {
      await onAddCause(parentId, newTitle.trim());
      setAddingFor(null);
      setNewTitle("");
    }
  };

  // Node silme
  const deleteNode = async (nodeId) => {
    if (
      !confirm(
        "Bu nedeni silmek istediğinize emin misiniz? Alt nedenler de etkilenebilir."
      )
    )
      return;
    if (typeof onDeleteCause === "function") {
      await onDeleteCause(nodeId);
    }
  };

  // Kök neden işaretle/kaldır
  const markRoot = async (node) => {
    if (typeof onMarkRoot === "function") {
      const newValue = node.is_root_cause === 1 ? 0 : 1;
      await onMarkRoot(node.id, newValue);
    }
  };

  // Her bir node'u render et
  const renderNode = (node) => {
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
                  <IxChip status="success" size="small">
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

          <div style={{ display: "flex", gap: 6 }}>
            <IxButton
              icon="plus"
              variant="secondary"
              title="Alt Sebep Ekle"
              onClick={() => {
                setAddingFor(node.id);
                setNewTitle("");
                toggleExpand(node.id);
              }}
            />
            <IxButton
              icon="edit-document"
              variant="outline"
              title="Sebepi Güncelle"
              onClick={() => startEditTitle(node)}
            />

            <IxButton
              icon="target"
              variant={node.is_root_cause === 1 ? "primary" : "outline"}
              title={
                node.is_root_cause === 1
                  ? "Kök Nedeni Kaldır"
                  : "Kök Neden Olarak İşaretle"
              }
              onClick={() => markRoot(node)}
            />

            <IxButton
              icon="trashcan"
              title="Sil"
              onClick={() => deleteNode(node.id)}
            />
          </div>
        </div>

        {/* Edit title input */}
        {editingTitleFor === node.id && (
          <div
            style={{ marginTop: 8, marginLeft: 36, display: "flex", gap: 8 }}
          >
            <IxInput
              label="Sebep Başlığı"
              value={editedTitle}
              onInput={(e) => setEditedTitle(e.target.value)}
            />
            <IxButton onClick={() => saveTitle(node)}>Kaydet</IxButton>
            <IxButton outline onClick={() => setEditingTitleFor(null)}>
              İptal
            </IxButton>
          </div>
        )}

        {/* Action Input */}
        {node.is_root_cause === 1 && editingActionFor === node.id && (
          <div
            style={{ marginTop: 6, marginLeft: 36, display: "flex", gap: 8 }}
          >
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
        )}
        {node.is_root_cause === 1 && editingActionFor !== node.id && (
          <div style={{ marginTop: 6, marginLeft: 36 }}>
            <IxButton icon="edit" onClick={() => startEditAction(node)}>
              Aksiyon Ekle / Düzenle (D5)
            </IxButton>
          </div>
        )}

        {/* Add child input */}
        {addingFor === node.id && (
          <div
            style={{ marginTop: 8, marginLeft: 36, display: "flex", gap: 8 }}
          >
            <IxInput
              label="Yeni Sebep"
              value={newTitle}
              onInput={(e) => setNewTitle(e.target.value)}
            />
            <IxButton onClick={() => addChild(node.id)}>Ekle</IxButton>
            <IxButton outline onClick={() => setAddingFor(null)}>
              İptal
            </IxButton>
          </div>
        )}

        {/* Children */}
        {hasChildren && isExpanded && (
          <ul style={{ marginLeft: 20, marginTop: 8 }}>
            {node.children.map((c) => renderNode(c))}
          </ul>
        )}
      </li>
    );
  };

  return (
    <div>
      {/* Root add button */}
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
            <IxButton onClick={() => addChild(null)}>Ekle</IxButton>
            <IxButton outline onClick={() => setAddingFor(null)}>
              İptal
            </IxButton>
          </div>
        )}
      </div>

      {/* Tree */}
      <ul style={{ paddingLeft: 0 }}>
        {(!causes || causes.length === 0) && (
          <div style={{ color: "#666" }}>Henüz bir sebep eklenmemiş.</div>
        )}
        {causes && causes.length > 0 && causes.map((n) => renderNode(n))}
      </ul>
    </div>
  );
}
