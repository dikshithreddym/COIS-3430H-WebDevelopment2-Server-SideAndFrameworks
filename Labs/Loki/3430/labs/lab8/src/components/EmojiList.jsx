import { useState } from "react";
import "../styles/EmojiList.css";
import { getRandomEmoji } from "./getEmoji.js"; 

export default function EmojiList({ lightsOn }) {
  const [emojis, setEmojis] = useState([getRandomEmoji()]);

  function addEmoji() {
    setEmojis((prevEmojis) => [...prevEmojis, getRandomEmoji()]);
  }

  return (
    <section className="EmojiList">
      <h2>Emoji List</h2>
      <ul>
        {emojis.map((emoji, index) => (
          <li
            key={`${emoji.id}-${index}`}
            className="emoji"
            style={{
              boxShadow: lightsOn ? "none" : "0 0 .3em .1em lime",
            }}
          >
            {emoji.emoji}
          </li>
        ))}
      </ul>
      <button onClick={addEmoji}>Add Emoji</button>
    </section>
  );
}
