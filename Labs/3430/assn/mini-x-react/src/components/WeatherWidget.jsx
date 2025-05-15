import { useEffect, useState } from 'react';

export default function WeatherWidget() {
  const [weather, setWeather] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!navigator.geolocation) {
      setError('Geolocation not supported.');
      return;
    }

    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        const { latitude, longitude } = pos.coords;

        try {
          const res = await fetch(
            `https://loki.trentu.ca/~dmacherla/www_data/Assignment-3/api/weather.php?lat=${latitude}&lon=${longitude}`
          );
          const data = await res.json();
          if (data.success) {
            setWeather(data);
          } else {
            setError(data.message || 'Failed to fetch weather.');
          }
        } catch {
          setError('Weather request failed.');
        }
      },
      () => setError('Permission denied for geolocation.')
    );
  }, []);

  if (error) {
    return <p className="text-muted" style={{ textAlign: 'center' }}>{error}</p>;
  }

  if (!weather) {
    return <p className="text-muted" style={{ textAlign: 'center' }}>Loading weather...</p>;
  }

  return (
    <div className="card" style={{ maxWidth: 300, margin: '20px auto' }}>
      <h3 style={{ marginBottom: '10px' }}>Weather in {weather.location}</h3>
      <div style={{ fontSize: '1.5rem' }}>
        🌡️ {weather.temperature}°C
      </div>
      <div>{weather.weather_descriptions.join(', ')}</div>
      {weather.icon && (
        <img src={weather.icon} alt="weather icon" style={{ marginTop: '10px' }} />
      )}
    </div>
  );
}
