// Import necessary hooks and utilities
import { useState } from 'react';
import { useAuth } from '../../authContext';
import { useNavigate } from 'react-router-dom';

export default function PostCard({ post, onPostChange }) {
  const { user } = useAuth();          // Get current logged-in user
  const navigate = useNavigate();      // Hook for navigation

  const postId = post.post_id;         // Consistent reference to post ID

  // Local UI state for likes, replies, and loading states
  const [likes, setLikes] = useState(post.likes);
  const [hasLiked, setHasLiked] = useState(post.user_liked);
  const [showReplies, setShowReplies] = useState(false);
  const [replies, setReplies] = useState([]);
  const [replyText, setReplyText] = useState('');
  const [loadingReplies, setLoadingReplies] = useState(false);

  // Fetch replies to this post from the backend
  const fetchReplies = async () => {
    setLoadingReplies(true);
    const res = await fetch(`https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/reply.php?post_id=${postId}`, {
      credentials: 'include',
    });
    const data = await res.json();
    if (data.success) setReplies(data.replies || []);
    setLoadingReplies(false);
  };

  // Toggle the reply section visibility
  const toggleReplies = () => {
    if (!showReplies) fetchReplies();
    setShowReplies(!showReplies);
  };

  // Handle reply submission
  const handleReplySubmit = async (e) => {
    e.preventDefault();
    if (!replyText.trim()) return;

    const res = await fetch(`https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/reply.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ content: replyText, post_id: postId }),
    });

    const data = await res.json();
    if (data.success) {
      setReplyText('');
      fetchReplies(); // Refresh the reply list after submitting
    }
  };

  // Handle like/unlike functionality
  const handleLike = async () => {
    const res = await fetch(`https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/like.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ post_id: postId }),
    });
    const data = await res.json();
    if (data.success) {
      setLikes(data.likes);            // Update like count
      setHasLiked(data.user_liked);    // Update liked status
      onPostChange?.();                // Optional refresh trigger for parent
    }
  };

  // Handle post deletion by owner
  const handleDelete = async () => {
    if (!window.confirm('Are you sure you want to delete this post?')) return;

    const res = await fetch(`https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/deletePost.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ post_id: postId }),
    });

    const data = await res.json();
    if (data.success && onPostChange) {
      onPostChange(); // Trigger post list refresh from parent
    }
  };

  return (
    <div className="card">
      {/* Username - clickable to go to profile */}
      <div
        className="card-title"
        style={{ cursor: 'pointer' }}
        onClick={() => navigate(`/user/${post.username}`)}
      >
        @{post.username}
      </div>

      {/* Post content */}
      <p>{post.content}</p>

      {/* Timestamp */}
      <div className="text-muted post-date">{new Date(post.created_at).toLocaleString()}</div>

      {/* Like count and reply count */}
      <div className="likes-count">❤️ {likes}</div>
      {post.replies > 0 && <div className="text-muted">{post.replies} replies</div>}

      {/* Like button */}
      <button className="btn-like" onClick={handleLike}>
        {hasLiked ? 'Unlike' : 'Like'}
      </button>

      {/* Delete button if this user owns the post */}
      {user?.user_id === post.user_id && (
        <button className="btn-delete" onClick={handleDelete}>
          Delete
        </button>
      )}

      {/* Message button if viewing someone else's post */}
      {user?.user_id !== post.user_id && (
        <button className="btn-link" onClick={() => navigate(`/messages/${post.username}`)}>
          Message
        </button>
      )}

      {/* Toggle reply section */}
      <button className="btn-link" onClick={toggleReplies}>
        {showReplies ? 'Hide Replies' : 'Reply'}
      </button>

      {/* Reply section */}
      {showReplies && (
        <div className="replies-section">
          {loadingReplies ? (
            <p>Loading...</p>
          ) : replies.length === 0 ? (
            <p className="text-muted">No replies yet.</p>
          ) : (
            replies.map((reply, idx) => (
              <div key={idx} className="reply card p-2 mb-1">
                <strong>@{reply.username}</strong>: {reply.content}
                <div className="text-muted" style={{ fontSize: '0.8em' }}>
                  {new Date(reply.created_at).toLocaleString()}
                </div>
              </div>
            ))
          )}

          {/* Reply input */}
          <form onSubmit={handleReplySubmit} style={{ marginTop: '10px' }}>
            <textarea
              rows="2"
              placeholder="Write a reply..."
              value={replyText}
              onChange={(e) => setReplyText(e.target.value)}
              style={{ width: '100%' }}
            ></textarea>
            <button type="submit" className="btn-primary" style={{ marginTop: '5px' }}>
              Reply
            </button>
          </form>
        </div>
      )}
    </div>
  );
}
