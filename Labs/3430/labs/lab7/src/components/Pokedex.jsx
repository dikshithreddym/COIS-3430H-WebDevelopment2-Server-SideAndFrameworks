import React from "react";
import PokeCard from "./PokeCard";
import "../styles/Pokedex.css"; // Import Pokedex styles

const Pokedex = ({ dexNumbers }) => {
  return (
    <div className="Pokedex">
      {dexNumbers.map((num) => (
        <PokeCard key={num} dexNumber={num} />
      ))}
    </div>
  );
};

export default Pokedex;
