/**
 * A random collection of emojis for all your element-creation needs :)
 *
 * @note You can make good use of _Destructuring Assignments_ with these objects, especially when
 * paired with the `getRandomEmoji` function down below. Check out that function's doc comment for
 * an example.
 *
 * @see {@link getRandomEmoji}
 */
const emojiList = [
  { id: 1, emoji: "🦈", category: "animal", name: "shark" },
  { id: 2, emoji: "🦔", category: "animal", name: "hedgehog" },
  { id: 3, emoji: "🐐", category: "animal", name: "goat" },
  { id: 4, emoji: "🐄", category: "animal", name: "cow" },
  { id: 5, emoji: "🔥", category: "nature", name: "fire" },
  { id: 6, emoji: "💄", category: "object", name: "lipstick" },
  { id: 7, emoji: "⚽", category: "object", name: "soccer-ball" },
  { id: 8, emoji: "🌯", category: "food", name: "burrito" },
  { id: 9, emoji: "✨", category: "nature", name: "sparkles" },
  { id: 10, emoji: "🦞", category: "animal", name: "lobster" },
  { id: 11, emoji: "🛁", category: "object", name: "bathtub" },
  { id: 12, emoji: "🌈", category: "nature", name: "rainbow" },
  { id: 13, emoji: "🌊", category: "nature", name: "water-wave" },
  { id: 14, emoji: "👁", category: "person", name: "eye" },
  { id: 15, emoji: "👀", category: "person", name: "eyes" },
  { id: 16, emoji: "🦀", category: "animal", name: "crab" },
  { id: 17, emoji: "🤩", category: "smiley", name: "star-struck" },
  { id: 18, emoji: "🥰", category: "smiley", name: "smiling-face-with-hearts" },
  { id: 19, emoji: "👄", category: "person", name: "mouth" },
  { id: 20, emoji: "🦅", category: "animal", name: "eagle" },
  { id: 21, emoji: "🌠", category: "nature", name: "shooting-star" },
  { id: 22, emoji: "🪑", category: "object", name: "chair" },
  { id: 23, emoji: "🛏", category: "object", name: "bed" },
  { id: 24, emoji: "🐖", category: "animal", name: "pig" },
  { id: 25, emoji: "🎨", category: "object", name: "artist-palette" },
  { id: 26, emoji: "🍟", category: "food", name: "french-fries" },
  { id: 27, emoji: "😂", category: "smiley", name: "face-with-tears-of-joy" },
  { id: 28, emoji: "🛋", category: "object", name: "couch-and-lamp" },
  {
    id: 29,
    emoji: "😎",
    category: "smiley",
    name: "smiling-face-with-sunglasses",
  },
  { id: 30, emoji: "🦆", category: "animal", name: "duck" },
];

/**
 * Randomly selects a new emoji from `emojiList`, for all your emoji-related needs :)
 *
 * @example This example uses _destructuring assignment_ to grab just the emoji and category of a
 * random emoji from `emojiList`, ignoring its name:
 *
 * ```js
 * const { id, emoji, category, name } = getRandomEmoji();
 * console.log(`The ${emoji} emoji is part of the ${category} category.`);
 * // => The 🐖 emoji is part of the animal category.
 * ```
 *
 * @see {@link emojiList}
 * @see {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Destructuring_assignment#object_destructuring}
 */
const getRandomEmoji = () =>
  emojiList[Math.floor(Math.random() * emojiList.length)];

// =================================================================================================
export { getRandomEmoji, emojiList };
