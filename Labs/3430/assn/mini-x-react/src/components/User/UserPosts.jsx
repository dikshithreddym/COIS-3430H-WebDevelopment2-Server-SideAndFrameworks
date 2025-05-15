// Import necessary hooks and components
import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import PostCard from '../Feed/PostCard';

export default function UserPosts() {
  // Extract the username from the route parameter
  const { username } = useParams();

  // Local state for storing fetched posts and potential error messages
  const [posts, setPosts] = useState([]);
  const [error, setError] = useState('');

  // Fetch posts by the given user from the API
  const fetchUserPosts = async () => {
    try {
      const res = await fetch(
        `https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/getUserPosts.php?user=${username}`,
        { credentials: 'include' }
      );
      const data = await res.json();

      if (data.success) {
        setPosts(data.posts); // Set posts if successful
      } else {
        setError(data.message || 'Could not load posts.'); // Handle API errors
      }
    } catch (err) {
      setError('Failed to fetch posts.'); // Handle fetch/network error
    }
  };

  // Trigger fetch when the component mounts or username changes
  useEffect(() => {
    fetchUserPosts();
  }, [username]);

  return (
    <div className="container">
      {/* Heading */}
      <h2 style={{ textAlign: 'center' }}>Posts by @{username}</h2>

      {/* Error message */}
      {error && <p className="text-muted" style={{ textAlign: 'center' }}>{error}</p>}

      {/* No posts message */}
      {posts.length === 0 && !error && (
        <p className="text-muted" style={{ textAlign: 'center' }}>No posts found.</p>
      )}

      {/* Render list of PostCard components */}
      {posts.map(post => (
        <PostCard key={post.id} post={{ ...post, username }} />
      ))}
    </div>
  );
}
