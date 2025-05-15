// Import necessary React hooks
import { useEffect, useState } from 'react';

export default function NewsHeadlines() {
  // State to hold the fetched news articles
  const [articles, setArticles] = useState([]);

  // State to hold any error message
  const [error, setError] = useState('');

  // Fetch news articles when component mounts
  useEffect(() => {
    const fetchNews = async () => {
      try {
        const res = await fetch('https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/news.php');
        const data = await res.json();

        if (data.success) {
          setArticles(data.articles); // Set articles if fetch is successful
        } else {
          setError(data.message || 'Could not load news.'); // Handle API error response
        }
      } catch {
        setError('Failed to fetch news.'); // Handle fetch/network error
      }
    };

    fetchNews();
  }, []);

  return (
    <div className="card" style={{ maxWidth: '600px', margin: '20px auto' }}>
      <h3 style={{ marginBottom: '10px' }}>📰 Top News in Canada</h3>

      {/* Display error message if there was a problem fetching */}
      {error && <p className="text-muted">{error}</p>}

      {/* Show fallback if no articles loaded and no error */}
      {articles.length === 0 && !error && (
        <p className="text-muted">No news articles found.</p>
      )}

      {/* Render list of news headlines */}
      <ul style={{ paddingLeft: '20px', textAlign: 'left' }}>
        {articles.map((article, index) => (
          <li key={index} style={{ marginBottom: '10px' }}>
            <a href={article.url} target="_blank" rel="noopener noreferrer">
              <strong>{article.title}</strong>
            </a><br />
            <small>
              {article.source} – {new Date(article.publishedAt).toLocaleString()}
            </small>
          </li>
        ))}
      </ul>
    </div>
  );
}
