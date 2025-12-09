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

  modalRef = showModal({
    content: (
      <ProblemModal
        editData={editData}
        onSaved={onSaved}
        onClose={() => {
          try {
            // showModal farklı versiyonlarda close/dismiss olabilir
            if (modalRef?.close) modalRef.close();
            else if (modalRef?.dismiss) modalRef.dismiss();
            else if (typeof modalRef === "function") modalRef();
          } catch (e) {
            console.warn("Modal kapatılamadı:", e);
          }
        }}
      />
    ),
    config: { size: "640" },
  });

  return modalRef;
}
