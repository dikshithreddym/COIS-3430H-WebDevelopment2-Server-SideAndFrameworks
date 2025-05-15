// Import necessary React hooks
import { createContext, useContext, useState } from 'react';

// Create the context object
const AuthContext = createContext();

// Custom hook to use the AuthContext more easily throughout the app
export const useAuth = () => useContext(AuthContext);

// Context provider component that wraps the app
export const AuthProvider = ({ children }) => {
  // Initialize user state from localStorage (if available)
  const [user, setUser] = useState(() => {
    const stored = localStorage.getItem('user');
    return stored ? JSON.parse(stored) : null;
  });

  // Login function — stores user data in both localStorage and state
  const login = (userData) => {
    localStorage.setItem('user', JSON.stringify(userData));
    setUser(userData);
  };

  // Logout function — clears user from localStorage and state
  const logout = () => {
    localStorage.removeItem('user');
    setUser(null);
  };

  // Provide user data and auth functions to all children components
  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};
