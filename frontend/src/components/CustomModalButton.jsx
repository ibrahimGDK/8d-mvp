import {
  IxButton,
  IxModalContent,
  IxModalFooter,
  IxModalHeader,
  Modal,
  ModalRef,
  showModal,
  IxInput,
} from "@siemens/ix-react";
import { useRef } from "react";


// Özel Modal bileşeni
function CustomModal() {
  const modalRef = useRef < ModalRef > null;
  
  // Modal'ı kapatma fonksiyonu, close payload gönderebilir
  const close = () => {
    modalRef.current?.close("close payload!");
  };
  
  // Modal'ı iptal etme
  const dismiss = () => {
    modalRef.current?.dismiss("dismiss payload");
  };

  return (
    <Modal ref={modalRef}>
      <IxModalHeader onCloseClick={() => dismiss()}>
        Create Resource
      </IxModalHeader>
      <IxModalContent>
        <form
          id="create-resource-form"
          onSubmit={(e) => {
            e.preventDefault();
            close();
          }}
        >
          <IxInput label="Name" type="text" id="name" name="name"></IxInput>
        </form>
      </IxModalContent>
      <IxModalFooter>
        <IxButton variant="subtle-primary" onClick={() => dismiss()}>
          Cancel
        </IxButton>
        <IxButton form="create-resource-form" type="submit">
          Submit
        </IxButton>
      </IxModalFooter>
    </Modal>
  );
}

// Modal'ı iptal etme / dismiss etme fonksiyonu, dismiss payload gönderebilir
export default () => {
  async function show() {
    await showModal({
      content: <CustomModal />,
    });
  }

  return <IxButton onClick={show}>Show modal</IxButton>;
};
