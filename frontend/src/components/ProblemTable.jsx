// src/components/ProblemTable.jsx

import React, { useEffect, useMemo, useState } from "react";
import { AgGridReact } from "ag-grid-react";
import { ModuleRegistry, AllCommunityModule } from "ag-grid-community";
import * as agGrid from "ag-grid-community";
import { getIxTheme } from "@siemens/ix-aggrid";

import "ag-grid-community/styles/ag-theme-alpine.css";
import { IxButton } from "@siemens/ix-react";
import { useNavigate } from "react-router-dom";
import { useProblemList, useDeleteProblem } from "../hooks/useProblems";

ModuleRegistry.registerModules([AllCommunityModule]);

export default function ProblemTable({ openEditModal }) {
  const navigate = useNavigate();
  const [ixTheme, setIxTheme] = useState(null);

  const { data: problems = [], isLoading } = useProblemList();
  const deleteMutation = useDeleteProblem();

  useEffect(() => {
    try {
      const theme = getIxTheme(agGrid);
      setIxTheme(theme);
    } catch (e) {
      console.warn("IX theme yüklenemedi, fallback kullanılıyor.", e);
      setIxTheme(null);
    }
  }, []);

  const onView = (row) => navigate(`/problems/${row.id}`);
  const onEdit = (row) => openEditModal(row);
  const onDelete = async (row) => {
    if (!confirm("Bu kaydı silmek istediğinize emin misiniz?")) return;
    try {
      await deleteMutation.mutateAsync(row.id);
    } catch (err) {
      console.error("Silme hatası:", err);
      alert("Silme sırasında bir hata oluştu.");
    }
  };

  const ActionCell = (props) => {
    const row = props.data;
    return (
      <div style={{ display: "flex", gap: 8 }}>
        <IxButton
          icon="chevron-right-small"
          onClick={() => props.context.onView(row)}
          title="Detay"
        />
        <IxButton
          icon="edit"
          variant="primary"
          onClick={() => props.context.onEdit(row)}
          title="Güncelle"
        />
        <IxButton
          icon="delete"
          variant="danger"
          onClick={() => props.context.onDelete(row)}
          title="Sil"
        />
      </div>
    );
  };

  const columnDefs = useMemo(
    () => [
      { field: "id", headerName: "ID", minWidth: 90 },
      { field: "title", headerName: "Başlık", flex: 1, minWidth: 200 },
      { field: "responsible_team", headerName: "Sorumlu", minWidth: 150 },
      {
        field: "status",
        headerName: "Durum",
        minWidth: 120,
        valueFormatter: (params) => {
          switch (params.value?.toLowerCase()) {
            case "open":
              return "Açık";
            case "close":
              return "Kapalı";
            default:
              return params.value ?? "-";
          }
        },
      },
      {
        field: "priority",
        headerName: "Öncelik",
        minWidth: 120,
        valueFormatter: (params) => {
          switch (params.value?.toLowerCase()) {
            case "low":
              return "Düşük";
            case "medium":
              return "Orta";
            case "high":
              return "Yüksek";
            default:
              return params.value ?? "-";
          }
        },
      },
      { field: "created_at", headerName: "Oluşturulma Tarihi", minWidth: 150 },
      {
        headerName: "İşlemler",
        field: "actions",
        width: 300,
        sortable: false,
        filter: false,
        suppressMenu: true,
        cellRenderer: "actionCell",
      },
    ],
    []
  );

  const defaultColDef = useMemo(
    () => ({
      resizable: true,
      sortable: true,
      filter: true,
      tooltipValueGetter: (params) => params.value,
    }),
    []
  );

  const context = { onView, onEdit, onDelete };
  const gridClass = ixTheme || "ag-theme-alpine";

  return (
    <div style={{ width: "100%", height: "60vh" }} className={gridClass}>
      <AgGridReact
        rowData={problems}
        columnDefs={columnDefs}
        defaultColDef={defaultColDef}
        components={{ actionCell: ActionCell }}
        context={context}
        suppressCellFocus={true}
        domLayout="normal"
      />
    </div>
  );
}
