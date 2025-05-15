import { useState } from "react";
import "../styles/Lights.css";

export default function Lights({ lightsOn, toggleLights }) {
  return (
    <section className={`Lights ${lightsOn ? "light" : "dark"}`}>
      {!lightsOn && <h2>Hey, Who Turned Out the Lights?</h2>}
      <button onClick={toggleLights}>
        {lightsOn ? "Turn Off" : "Turn On"}
      </button>
    </section>
  );
}