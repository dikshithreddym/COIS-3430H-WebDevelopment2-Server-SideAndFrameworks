// Import core React libraries
import React from 'react';
import ReactDOM from 'react-dom/client';

// Import the root App component
import App from './App';

// Import the authentication context provider
import { AuthProvider } from './authContext';

// Import global styles
import './styles/styles.css'; // Assuming your styles.css is placed here

// Create a root and render the React application
ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    {/* Provide authentication context to the entire app */}
    <AuthProvider>
      {/* Main application component */}
      <App />
    </AuthProvider>
  </React.StrictMode>
);
