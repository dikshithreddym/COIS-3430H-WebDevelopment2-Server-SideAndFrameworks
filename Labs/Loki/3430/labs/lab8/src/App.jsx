import { useState } from "react";
import "./styles/App.css";
import Lights from "./components/Lights";
import EmojiList from "./components/EmojiList";

function App() {
  const [lightsOn, setLightsOn] = useState(true);

  function toggleLights() {
    setLightsOn((prev) => !prev);
  }

  return (
    <main className={lightsOn ? "light" : "dark"}>
      <Lights lightsOn={lightsOn} toggleLights={toggleLights} />
      <EmojiList lightsOn={lightsOn} />
    </main>
  );
}

export default App;
