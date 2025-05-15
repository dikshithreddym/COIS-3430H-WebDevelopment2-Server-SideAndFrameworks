// Import React's useState hook for state management
import { useState } from 'react';

export default function PostBox({ onPost }) {
  // Local state for the post content and error message
  const [content, setContent] = useState('');
  const [error, setError] = useState('');

  // Handle form submission to post new content
  const handlePost = async (e) => {
    e.preventDefault(); // Prevent default form behavior
    setError('');        // Reset any previous error

    // Do not allow empty or whitespace-only posts
    if (!content.trim()) {
      setError('Post cannot be empty');
      return;
    }

    try {
      // Send post content to the backend API
      const res = await fetch('https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ content }),
      });

      const data = await res.json();

      if (data.success) {
        setContent(''); // Clear input
        onPost();       // Refresh feed by calling the provided callback
      } else {
        setError(data.message); // Show API error
      }
    } catch (err) {
      setError('Failed to post. Try again.'); // Show generic error
    }
  };

  return (
    <div className="post-box">
      <form onSubmit={handlePost}>
        {/* Textarea for typing post */}
        <textarea
          rows="3"
          placeholder="What's on your mind?"
          value={content}
          onChange={(e) => setContent(e.target.value)}
        ></textarea>

        {/* Error display */}
        {error && <p className="text-muted">{error}</p>}

        {/* Submit button */}
        <button type="submit" className="btn-primary">Post</button>
      </form>
    </div>
  );
}
