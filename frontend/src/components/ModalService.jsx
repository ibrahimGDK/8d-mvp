// src/components/ModalService.jsx
import { showModal } from "@siemens/ix-react";
import ProblemModal from "./ProblemModal";

/**
 * openProblemModal(onSaved, editData)
 * - onSaved: listeyi yenilemek için callback (Dashboard.load)
 * - editData: (opsiyonel) mevcut kayıt objesi gönderilirse modal edit modunda açılır
 */
export function openProblemModal(onSaved, editData = null) {
  let modalRef = null;

  const closeModal = () => {
    try {
      if (modalRef?.close) modalRef.close();
      else if (modalRef?.dismiss) modalRef.dismiss();
      else if (typeof modalRef === "function") modalRef();
    } catch (e) {
      console.warn("Modal kapatılamadı:", e);
    }
  };

  modalRef = showModal({
    content: (
      <ProblemModal
        editData={editData}
        onSubmit={async (form) => {
          await onSaved(form); // önce kayıt işlemini bitir
          closeModal(); // sonra modalı kapat
        }}
        onClose={closeModal} // İPTAL → kapat
      />
    ),
    config: { size: "640" },
  });

  // modalRef.addEventListener("close", () => closeModal());

  return modalRef;
}
