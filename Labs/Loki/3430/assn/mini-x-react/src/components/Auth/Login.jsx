// Import necessary hooks and context
import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom'; // ✅ Added Link
import { useAuth } from '../../authContext';

export default function Login() {
  // Local form state
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const navigate = useNavigate();  // React Router navigation
  const { login } = useAuth();     // Auth context login method

  // Handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault(); // Prevent page refresh
    setError('');       // Reset error state

    try {
      // Send login request to the backend
      const res = await fetch('https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ username, password }),
      });

      const data = await res.json();

      if (data.success) {
        // ✅ Successful login — store user in context
        login({ user_id: data.user_id, username: data.username });

        // Redirect to feed
        navigate('/feed');
      } else {
        // Show server-side error message
        setError(data.message);
      }
    } catch (err) {
      // Show generic error if request fails
      setError('Login failed. Please try again.');
    }
  };

  return (
    <div className="auth-container">
      <h2>Login</h2>

      {/* Login form */}
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          placeholder="Username"
          value={username}
          onChange={e => setUsername(e.target.value)}
          required
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={e => setPassword(e.target.value)}
          required
        />

        {/* Error message display */}
        {error && <p className="text-muted" style={{ color: 'tomato' }}>{error}</p>}

        <button className="btn-primary" type="submit">Login</button>
      </form>

      {/* Link to registration */}
      <p>Don't have an account? <Link to="/register">Register</Link></p>
    </div>
  );
}
