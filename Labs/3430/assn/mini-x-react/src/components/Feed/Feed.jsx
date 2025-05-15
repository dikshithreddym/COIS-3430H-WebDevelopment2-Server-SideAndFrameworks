// Import required hooks and components
import { useEffect, useState } from 'react';
import PostBox from './PostBox';
import PostCard from './PostCard';
import WeatherWidget from '../WeatherWidget';
import Navbar from '../Navbar';
import NewsHeadlines from '../NewsHeadlines';
import ThemeToggle from '../ThemeToggle';

export default function Feed() {
  // State to store the list of posts from the feed
  const [posts, setPosts] = useState([]);

  // Fetch the latest feed data from the backend API
  const fetchFeed = async () => {
    try {
      const res = await fetch('https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/feed.php', {
        credentials: 'include',
      });
      const data = await res.json();
      if (data.success) {
        setPosts(data.posts); // Store posts in state
      }
    } catch (err) {
      console.error('Error loading feed', err); // Log fetch error
    }
  };

  // Fetch feed once when component mounts
  useEffect(() => {
    fetchFeed();
  }, []);

  return (
    <div className="container">
      {/* Top navigation bar */}
      <Navbar />

      {/* Main layout: Feed on the left, widgets on the right */}
      <div style={{ display: 'flex', gap: '20px', alignItems: 'flex-start' }}>
        {/* Left column: Posting box and posts */}
        <div style={{ flex: 3 }}>
          {/* Input box for creating a new post */}
          <PostBox onPost={fetchFeed} />

          {/* Show message if no posts, otherwise render the list */}
          {posts.length === 0 ? (
            <p className="text-muted" style={{ textAlign: 'center' }}>No posts yet.</p>
          ) : (
            posts.map(post => (
              <PostCard key={post.post_id} post={post} onPostChange={fetchFeed} />
            ))
          )}
        </div>

        {/* Right column: Sidebar widgets */}
        <div style={{ flex: 1 }}>
          <WeatherWidget />
          <ThemeToggle />
          <NewsHeadlines />
        </div>
      </div>
    </div>
  );
}
