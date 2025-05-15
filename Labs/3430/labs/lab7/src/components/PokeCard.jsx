import React from "react";
import "../styles/PokeCard.css"; // Import CSS file

const PokeCard = ({ dexNumber = 1 }) => { // Default to Bulbasaur (#1)
  return (
    <div className="PokeCard">
      <h2>Pokémon #{dexNumber}</h2>
      <img 
        src={`https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${dexNumber}.png`} 
        alt={`Pokémon ${dexNumber}`} 
      />
    </div>
  );
};

export default PokeCard;
