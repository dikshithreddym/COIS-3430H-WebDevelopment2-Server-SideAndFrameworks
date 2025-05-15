// Import required hooks and auth context
import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useAuth } from '../../authContext';

export default function DirectMessages() {
  // Extract the username of the person we're chatting with from the URL
  const { username: chatWith } = useParams();

  // Get the logged-in user's data
  const { user } = useAuth();

  // Local states for messages, new message input, and error handling
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [error, setError] = useState('');

  // Fetch existing messages from API
  const fetchMessages = async () => {
    try {
      const res = await fetch(
        `https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/direct_messages.php?with=${chatWith}`,
        { credentials: 'include' }
      );
      const data = await res.json();

      if (data.success) {
        setMessages(data.messages); // Load messages into state
      } else {
        setError(data.message); // Show error message from API
      }
    } catch {
      setError('Failed to load messages.'); // Network or fetch error
    }
  };

  // Handle sending a new message
  const sendMessage = async (e) => {
    e.preventDefault(); // Prevent page reload
    setError('');

    // Avoid sending empty messages
    if (!newMessage.trim()) return;

    try {
      const res = await fetch(
        'https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/direct_messages.php',
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({ to: chatWith, message: newMessage }),
        }
      );

      const data = await res.json();

      if (data.success) {
        setNewMessage('');  // Clear the input
        fetchMessages();    // Refresh the message list
      } else {
        setError(data.message); // API error
      }
    } catch {
      setError('Failed to send message.'); // Network or fetch error
    }
  };

  // Fetch messages when component loads or chat partner changes
  useEffect(() => {
    fetchMessages();
  }, [chatWith]);

  return (
    <div className="container">
      {/* Chat heading */}
      <h2 style={{ textAlign: 'center' }}>Chat with @{chatWith}</h2>

      {/* Display error if exists */}
      {error && <p className="text-muted">{error}</p>}

      {/* Message list container */}
      <div style={{ maxHeight: '400px', overflowY: 'auto', marginBottom: '1rem' }}>
        {messages.map((msg) => (
          <div
            key={msg.id}
            className="card"
            style={{
              textAlign: msg.sender === user.username ? 'right' : 'left',
              background: '#22303C'
            }}
          >
            <div style={{ fontSize: '0.9em', color: '#b0b8c0' }}>
              {msg.sender === user.username ? 'You' : `@${msg.sender}`}
            </div>
            <div>{msg.message}</div>
            <div className="text-muted post-date">
              {new Date(msg.timestamp).toLocaleString()}
            </div>
          </div>
        ))}
      </div>

      {/* Message input form */}
      <form onSubmit={sendMessage}>
        <textarea
          rows="3"
          placeholder="Type your message..."
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
        ></textarea>
        <button className="btn-primary" type="submit">Send</button>
      </form>
    </div>
  );
}
