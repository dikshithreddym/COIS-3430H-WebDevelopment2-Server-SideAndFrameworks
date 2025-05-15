import { useEffect, useState } from 'react';

export default function ThemeToggle() {
  const [isLightMode, setIsLightMode] = useState(() => {
    return localStorage.getItem('theme') === 'light';
  });

  useEffect(() => {
    document.body.classList.toggle('light-mode', isLightMode);
    localStorage.setItem('theme', isLightMode ? 'light' : 'dark');
  }, [isLightMode]);

  const toggleTheme = () => setIsLightMode(prev => !prev);

  return (
    <button id="toggleTheme" onClick={toggleTheme}>
      {isLightMode ? '🌙 Dark Mode' : '☀️ Light Mode'}
    </button>
  );
}
