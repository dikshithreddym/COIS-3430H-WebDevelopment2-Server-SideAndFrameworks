// Import authentication context and navigation hook
import { useAuth } from '../authContext';
import { useNavigate } from 'react-router-dom';

export default function Navbar() {
  // Access current user and logout function from auth context
  const { user, logout } = useAuth();

  // React Router's navigation hook
  const navigate = useNavigate();

  // Logs the user out via API, clears local state, then redirects
  const handleLogout = async () => {
    await fetch('https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/logout.php', {
      method: 'POST',
      credentials: 'include',
    });
    logout();           // Clear context + localStorage
    navigate('/login'); // Redirect to login page
  };

  return (
    <div
      className="container"
      style={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingBottom: '10px'
      }}
    >
      {/* App title */}
      <div style={{ fontWeight: 'bold', fontSize: '1.2em' }}>
        Mini X
      </div>

      {/* Display username and logout button */}
      <div>
        <span style={{ marginRight: '15px' }}>@{user?.username}</span>
        <button className="btn-delete" onClick={handleLogout}>Logout</button>
      </div>
    </div>
  );
}
