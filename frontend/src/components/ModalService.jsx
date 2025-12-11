import { showModal } from "@siemens/ix-react";
import ProblemModal from "./ProblemModal";

// Problem Modal'ı açmak için yardımcı fonksiyon
// onSaved: form submit sonrası yapılacak işlem
// editData: mevcut veriyi düzenlemek için opsiyonel

export function openProblemModal(onSaved, editData = null) {
  let modalRef = null;
  
  // Modal'ı güvenli bir şekilde kapatma fonksiyonu
  const closeModal = () => {
    try {
      if (modalRef?.close) modalRef.close();
      else if (modalRef?.dismiss) modalRef.dismiss();
      else if (typeof modalRef === "function") modalRef();
    } catch (e) {
      console.warn("Modal kapatılamadı:", e);
    }
  };

  // Modal'ı aç
  modalRef = showModal({
    content: (
      <ProblemModal
        editData={editData}
        onSubmit={async (form) => {
          await onSaved(form);
          closeModal();
        }}
        onClose={closeModal}
      />
    ),
    config: { size: "640" },
  });


  return modalRef;
}
