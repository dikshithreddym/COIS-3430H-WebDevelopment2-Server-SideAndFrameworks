// Import routing components from React Router
import { HashRouter as Router, Routes, Route, Navigate } from 'react-router-dom';

// Import app pages/components
import Login from './components/Auth/Login';
import Register from './components/Auth/Register';
import Feed from './components/Feed/Feed';
import { useAuth } from './authContext';
import UserPosts from './components/User/UserPosts';
import DirectMessages from './components/Messages/DirectMessages';

// Wrapper for private routes — redirects to login if user is not authenticated
function PrivateRoute({ children }) {
  const { user } = useAuth();
  return user ? children : <Navigate to="/login" />;
}

// Main application component that defines routing
function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />

        {/* Protected route: Feed (only accessible if logged in) */}
        <Route path="/feed" element={
          <PrivateRoute>
            <Feed />
          </PrivateRoute>
        } />

        {/* Redirect root URL to /feed */}
        <Route path="/" element={<Navigate to="/feed" />} />

        {/* Catch-all for undefined routes */}
        <Route path="*" element={<h2 style={{ textAlign: 'center' }}>404 - Page Not Found</h2>} />

        {/* Protected route: User posts page */}
        <Route path="/user/:username" element={
          <PrivateRoute>
            <UserPosts />
          </PrivateRoute>
        } />

        {/* Protected route: Direct messages with a specific user */}
        <Route path="/messages/:username" element={
          <PrivateRoute>
            <DirectMessages />
          </PrivateRoute>
        } />
      </Routes>
    </Router>
  );
}

// Export the main app component
export default App;
