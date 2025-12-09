import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App.jsx';
import './styles/global.css';

import { IxApplication } from '@siemens/ix-react';
import '@siemens/ix/dist/siemens-ix/siemens-ix.css'; 

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <IxApplication>
      <App />
    </IxApplication>
  </React.StrictMode>
);