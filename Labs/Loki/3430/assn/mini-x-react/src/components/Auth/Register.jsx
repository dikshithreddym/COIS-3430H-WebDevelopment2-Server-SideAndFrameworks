// Import React hooks and custom auth context
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../authContext';

export default function Register() {
  // State to manage form data
  const [form, setForm] = useState({ username: '', email: '', password: '' });

  // State for handling error messages
  const [error, setError] = useState('');

  const navigate = useNavigate(); // React Router's navigation hook
  const { login } = useAuth();    // Access login function from auth context

  // Update form state when user types
  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  // Handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault();  // Prevent page reload
    setError('');         // Clear any previous errors

    try {
      // Send registration data to the backend
      const res = await fetch('https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(form),
      });

      const data = await res.json();
      console.log(data);

      if (data.success) {
        // Automatically log in the user after successful registration
        login({ user_id: data.user_id, username: data.username });

        // Redirect to the feed
        navigate('/feed');
      } else {
        // Display error message from server
        setError(data.message);
      }
    } catch (err) {
      // Generic error fallback
      setError('Registration failed. Please try again.');
    }
  };

  return (
    <div className="auth-container">
      <h2>Register</h2>

      {/* Registration form */}
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          name="username"
          placeholder="Username"
          value={form.username}
          onChange={handleChange}
          required
        />
        <input
          type="email"
          name="email"
          placeholder="Email"
          value={form.email}
          onChange={handleChange}
          required
        />
        <input
          type="password"
          name="password"
          placeholder="Password"
          value={form.password}
          onChange={handleChange}
          required
        />

        {/* Error message if any */}
        {error && <p className="text-muted" style={{ color: 'tomato' }}>{error}</p>}

        <button className="btn-primary" type="submit">Register</button>
      </form>

      {/* Link to login page */}
      <p>Already have an account? <a href="/login">Login</a></p>
    </div>
  );
}
