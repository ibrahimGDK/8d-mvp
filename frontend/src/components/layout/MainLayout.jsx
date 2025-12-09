import { Outlet, useNavigate, useLocation } from "react-router-dom";
import {
  IxBasicNavigation,
  IxMenu,
  IxMenuItem,
  IxMenuCategory,
} from "@siemens/ix-react";

export default function MainLayout() {
  const navigate = useNavigate();
  const location = useLocation();

  return (
    <IxBasicNavigation applicationName="8D Problem Solving">
      <div slot="logo">SIEMENS</div>

      <IxMenu>
        <IxMenuItem
          home
          tabIcon="home"
          slot="bottom"
          onClick={() => navigate("/")}
        >
          Ana Sayfa
        </IxMenuItem>

        <IxMenuItem
          icon="document"
          active={location.pathname === "/"}
          onClick={() => navigate("/")}
        >
          Problem Listesi
        </IxMenuItem>

        <IxMenuCategory label="Ayarlar" icon="settings">
          <IxMenuItem icon="user">Profil</IxMenuItem>
        </IxMenuCategory>
      </IxMenu>

      {/* Sayfaların render edileceği alan */}
      <div style={{ padding: "2rem", flexGrow: 1 }}>
        <Outlet />
      </div>
    </IxBasicNavigation>
  );
}
