import React from "react";
import Pokedex from "./components/Pokedex";

function App() {
  // Array of Pokémon National Dex numbers
  const pokemonList = [1, 4, 7, 25, 39, 43, 58, 92, 133, 150];

  return (
    <div>
      <h1>My Pokédex</h1>
      <Pokedex dexNumbers={pokemonList} />
    </div>
  );
}

export default App;
