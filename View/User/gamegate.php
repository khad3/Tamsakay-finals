<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room Games</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0e68c;
        }
        .menu {
            position: absolute;
            top: 20%;
            text-align: center;
        }
        .menu button {
            margin: 10px;
            padding: 15px 25px;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
        }
        canvas {
            display: block;
            background: #f0e68c;
        }
        .info {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            text-align: center;
            color: white;
            font-family: Arial, sans-serif;
            font-size: 24px;
            background: rgba(0, 0, 0, 0.7);
            padding: 20px 40px;
            border-radius: 10px;
        }
        .input-area {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .input-area input {
            width: 120px;
            padding: 12px;
            font-size: 18px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }
        .input-area button {
            padding: 12px 18px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button {
            margin-top: 20px;
            padding: 12px 18px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            background-color: #ff6347;
            color: white;
        }
        @media (max-width: 600px) {
            .info {
                font-size: 20px;
                padding: 15px;
            }
            .menu button {
                padding: 12px 20px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="menu" id="gameMenu">
        <h1>Select a Game</h1>
        <button onclick="startFruitCatcher()">Fruit Catcher</button>
        <button onclick="startTicTacToe()">Tic Tac Toe</button>
        <button onclick="startLogicPuzzleGame()">Logic Puzzle</button>
        <button onclick="startMemoryGame()">Memory Game</button>
    </div>

    <div class="info" id="gameInfo" style="display: none;">
        <p>Score: <span id="score">0</span></p>
        <p id="questionText"></p>
        <p id="lives">Lives: 3</p>
    </div>

    <canvas id="gameCanvas" style="display: none;"></canvas>

    <div class="input-area" id="inputArea" style="display: none;">
        <input type="text" id="answerInput" placeholder="Your answer">
        <button onclick="submitAnswer()">Submit</button>
    </div>

    <button class="back-button" id="backButton" onclick="goBackToMenu()" style="display: none;">Back to Main Menu</button>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        let score = 0;
        let currentQuestion = {};
        let selectedGame = null;
        let flippedCards = [];
        let matchedCards = [];
        let currentShape = null;
       

          // Resize canvas based on screen size
          function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight * 0.8;
        }

        // Go back to the main menu
        function goBackToMenu() {
            document.getElementById("gameMenu").style.display = "block";
            document.getElementById("gameInfo").style.display = "none";
            document.getElementById("inputArea").style.display = "none";
            document.getElementById("backButton").style.display = "none";
            canvas.style.display = "none";
            resetGame();
        }

        const objects = [
            "🍎", "🍊", "🍌", "🍇", "🍓", "🥝", "🍍", "🥥", "💣" // Added bomb emoji to the objects list
        ];

        let lives = 3; // User starts with 3 lives

        function startFruitCatcher() {
            resetGame();
            document.getElementById("gameMenu").style.display = "none";
            document.getElementById("gameInfo").style.display = "block";
            document.getElementById("lives").style.display = "block"; // Show lives for Fruit Catcher
            document.getElementById("questionText").style.display = "none"; // Hide questions
            canvas.style.display = "block";
            resizeCanvas();
            runFruitCatcherGame();
        }

        function runFruitCatcherGame() {
            const fruits = [];
            const basket = { x: canvas.width / 2 - 50, y: canvas.height - 50, width: 100, height: 20 };

            function spawnFruit() {
                const x = Math.random() * (canvas.width - 20);
                const fruitIndex = Math.floor(Math.random() * objects.length);
                const fruit = {
                    x: x,
                    y: 0,
                    size: 20,
                    emoji: objects[fruitIndex], // Add the emoji to the fruit object
                    isBomb: objects[fruitIndex] === "💣" // Check if it's a bomb
                };
                fruits.push(fruit);
            }

            function drawBasket() {
                ctx.fillStyle = "brown";
                ctx.fillRect(basket.x, basket.y, basket.width, basket.height);
            }

            function drawFruits() {
                ctx.font = "30px Arial"; // Set font size for fruit emoji
                ctx.textAlign = "center"; // Align text to center for better positioning
                for (const fruit of fruits) {
                    ctx.fillText(fruit.emoji, fruit.x, fruit.y); // Draw the fruit emoji
                }
            }

            function updateFruits() {
                for (const fruit of fruits) {
                    fruit.y += 5;
                }

                for (let i = fruits.length - 1; i >= 0; i--) {
                    if (fruits[i].y > canvas.height) {
                        fruits.splice(i, 1); // Remove fruit when it falls off the screen
                    } else if (
                        fruits[i].y + fruits[i].size / 2 >= basket.y &&
                        fruits[i].x >= basket.x &&
                        fruits[i].x <= basket.x + basket.width
                    ) {
                        if (fruits[i].isBomb) {
                            lives--; // Reduce life if bomb is caught
                            document.getElementById("lives").textContent = `Lives: ${lives}`;
                            if (lives <= 0) {
                                endGame();
                            }
                        } else {
                            score++; // Increase score if it's a fruit
                            document.getElementById("score").textContent = `Score: ${score}`;
                        }
                        fruits.splice(i, 1); // Remove fruit after catching
                    }
                }
            }

            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                drawBasket();
                drawFruits();
            }

            function gameLoop() {
                draw();
                updateFruits();
                requestAnimationFrame(gameLoop); // Request next frame for smooth animation
            }

            window.addEventListener("mousemove", (e) => {
                basket.x = e.clientX - basket.width / 2;
            });

            setInterval(spawnFruit, 1000); // Spawn a fruit every second
            gameLoop();
        }

        function endGame() {
            alert("Game Over! Your score was: " + score);
            resetGame();
        }

        function resetGame() {
            lives = 3;
            score = 0;
            document.getElementById("score").textContent = `Score: ${score}`;
            document.getElementById("lives").textContent = `Lives: ${lives}`;
        }

        // Start Tic Tac Toe game
function startTicTacToe() {
    resetGame();  // Reset game state
    selectedGame = "ticTacToe";  // Mark the selected game
    document.getElementById("gameMenu").style.display = "none";
    document.getElementById("gameInfo").style.display = "none";
    document.getElementById("backButton").style.display = "block";
    canvas.style.display = "block";
    resizeCanvas();
    runTicTacToeGame();
}

// Run Tic Tac Toe game logic
function runTicTacToeGame() {
    const gridSize = 3;
    const cellSize = canvas.width / gridSize;
    let currentPlayer = "X";
    let gameBoard = Array(gridSize).fill().map(() => Array(gridSize).fill(null));

    function drawBoard() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let row = 0; row < gridSize; row++) {
            for (let col = 0; col < gridSize; col++) {
                ctx.strokeRect(col * cellSize, row * cellSize, cellSize, cellSize);
                if (gameBoard[row][col]) {
                    ctx.font = "40px Arial";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillText(gameBoard[row][col], col * cellSize + cellSize / 2, row * cellSize + cellSize / 2);
                }
            }
        }
    }

    function checkWinner() {
        for (let i = 0; i < gridSize; i++) {
            if (gameBoard[i][0] === gameBoard[i][1] && gameBoard[i][1] === gameBoard[i][2] && gameBoard[i][0]) {
                return true;
            }
            if (gameBoard[0][i] === gameBoard[1][i] && gameBoard[1][i] === gameBoard[2][i] && gameBoard[0][i]) {
                return true;
            }
        }
        if (gameBoard[0][0] === gameBoard[1][1] && gameBoard[1][1] === gameBoard[2][2] && gameBoard[0][0]) {
            return true;
        }
        if (gameBoard[0][2] === gameBoard[1][1] && gameBoard[1][1] === gameBoard[2][0] && gameBoard[0][2]) {
            return true;
        }
        return false;
    }

    canvas.addEventListener("click", (e) => {
        const row = Math.floor(e.clientY / cellSize);
        const col = Math.floor(e.clientX / cellSize);
        if (gameBoard[row][col] === null) {
            gameBoard[row][col] = currentPlayer;
            if (checkWinner()) {
                alert(currentPlayer + " wins!");
                resetGame(); // Reset after winning
            }
            currentPlayer = currentPlayer === "X" ? "O" : "X";
            drawBoard();
        }
    });

    drawBoard();
}

// Start Memory Match game
function startMemoryGame() {
    resetGame();  // Reset game state
    selectedGame = "memoryMatch";  // Mark the selected game
    document.getElementById("gameMenu").style.display = "none";
    document.getElementById("lives").style.display = "none"; // Hide lives for Math games
    document.getElementById("gameInfo").style.display = "block";
    document.getElementById("questionText").style.display = "none"; // hide question
    document.getElementById("backButton").style.display = "block";
    document.getElementById("inputArea").style.display = "none";
    canvas.style.display = "block";
    resizeCanvas();
    runMemoryMatchGame();
}

// Memory Match Game Logic
function runMemoryMatchGame() {
    const cardImages = ["🍎", "🍌", "🍒", "🍇", "🍓", "🍉", "🍊", "🍍"];
    let cards = [...cardImages, ...cardImages];
    cards = shuffleArray(cards);
    createCardGrid(cards);

    canvas.addEventListener("click", (event) => handleCardClick(event, cards));
}

// Shuffle the array
function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

// Create the card grid
function createCardGrid(cards) {
    const cols = 4;
    const rows = 4;
    const cardWidth = 100;
    const cardHeight = 100;
    const padding = 20;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";

    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < cols; j++) {
            const cardIndex = i * cols + j;
            const x = j * (cardWidth + padding) + padding;
            const y = i * (cardHeight + padding) + padding;
            const cardValue = cards[cardIndex];

            // Display flipped and matched cards
            if (flippedCards.includes(cardIndex) || matchedCards.includes(cardIndex)) {
                ctx.fillStyle = "#FFF";
                ctx.fillRect(x, y, cardWidth, cardHeight);
                ctx.fillStyle = "#000";
                ctx.font = "36px Arial";
                ctx.fillText(cardValue, x + cardWidth / 2, y + cardHeight / 2);
            } else {
                ctx.fillStyle = "#007BFF";
                ctx.fillRect(x, y, cardWidth, cardHeight);
            }
        }
    }

    // Check if the game is complete (all cards are matched)
    if (matchedCards.length === cards.length) {
        showShuttleButton();
    }
}

// Handle card click
function handleCardClick(event, cards) {
    const cardWidth = 100;
    const cardHeight = 100;
    const padding = 20;

    const x = event.clientX - canvas.offsetLeft;
    const y = event.clientY - canvas.offsetTop;

    const col = Math.floor(x / (cardWidth + padding));
    const row = Math.floor(y / (cardHeight + padding));
    const cardIndex = row * 4 + col;

    // Ignore if the card is already flipped or matched
    if (flippedCards.includes(cardIndex) || matchedCards.includes(cardIndex)) {
        return;
    }

    flippedCards.push(cardIndex);
    createCardGrid(cards);

    // If two cards are flipped, check for match
    if (flippedCards.length === 2) {
        checkMatch(cards);
    }
}

// Check if the two flipped cards match
function checkMatch(cards) {
    setTimeout(() => {
        const [card1, card2] = flippedCards.map((index) => cards[index]);

        if (card1 === card2) {
            // Cards match, add to matchedCards and increment score
            matchedCards.push(...flippedCards);
            score++;
            document.getElementById("score").textContent = score;
        }

        // Reset flipped cards
        flippedCards = [];
        createCardGrid(cards);
    }, 1000);
}

// Reset the game state
function resetGame() {
    score = 0;
    flippedCards = [];
    matchedCards = [];
    document.getElementById("score").textContent = score;
    hideShuttleButton(); // Hide shuttle button when resetting game
}

// Show the shuttle button when the game is complete
function showShuttleButton() {
    const shuttleButton = document.getElementById("shuttleButton");
    shuttleButton.style.display = "block"; // Show the shuttle button
    shuttleButton.addEventListener("click", handleShuttleClick); // Add event listener
}

// Hide the shuttle button when game is not complete or is reset
function hideShuttleButton() {
    const shuttleButton = document.getElementById("shuttleButton");
    shuttleButton.style.display = "none"; // Hide shuttle button
}

// Handle shuttle button click (reshuffle cards and restart game)
function handleShuttleClick() {
    alert("Shuffling the cards for a new round!");
    resetGame();
    runMemoryMatchGame(); // Restart the game with shuffled cards
}

// Initialize the game on page load
window.onload = function () {
    resizeCanvas();

    // Create the shuttle button dynamically or ensure it's in your HTML
    const shuttleButton = document.createElement("button");
    shuttleButton.id = "shuttleButton";
    shuttleButton.style.display = "none"; // Initially hidden
    shuttleButton.textContent = "Shuttle Cards";
    document.body.appendChild(shuttleButton); // Append the button to the body
};

// Start Logic Puzzle game
function startLogicPuzzleGame() {
    resetGame();
    selectedGame = "logicPuzzle";
    document.getElementById("gameMenu").style.display = "none";
    document.getElementById("gameInfo").style.display = "block";
    document.getElementById("lives").style.display = "none"; // Hide lives for Math games
    document.getElementById("inputArea").style.display = "flex";
    document.getElementById("backButton").style.display = "block";
    document.getElementById("questionText").textContent = "Solve the logic puzzle!";
    canvas.style.display = "none";
    resizeCanvas();
    currentQuestion = generateLogicPuzzle();
    document.getElementById("questionText").textContent = currentQuestion.question;
}

function generateLogicPuzzle() {
    const puzzles = [
        {
            question: "I am not alive, but I grow; I do not have lungs, but I need air; I do not have a mouth, but water kills me. What am I?",
            answer: "Fire"
        },
        {
            question: "The more you take, the more you leave behind. What am I?",
            answer: "Footsteps"
        },
    
        {
            question: "I have keys but open no locks. I have space but no room. You can enter, but you can’t go outside. What am I?",
            answer: "keyboard"
        },
        {
            question: "What has many teeth, but can’t bite?",
            answer: "comb"
        },
        {
            question: "What can travel around the world while staying in the corner?",
            answer: "stamp"
        },
        {
            question: "What has a head, a tail, but no body?",
            answer: "coin"
        },
        {
            question: "The more of this there is, the less you see. What is it?",
            answer: "Darkness"
        },
        {
            question: "What comes down but never goes up?",
            answer: "Rain"
        },
        {
            question: "What gets wetter the more it dries?",
            answer: "towel"
        },
        {
            question: "I am tall when I am young, and I am short when I am old. What am I?",
            answer: "candle"
        },
        {
            question: "What has a face but can’t smile?",
            answer: "clock"
        },
        {
            question: "What has one eye but can’t see?",
            answer: "needle"
        },
        {
            question: "What can be cracked, made, told, and played?",
            answer: "joke"
        },
        {
            question: "What has an eye but can’t see?",
            answer: "storm"
        },

        {
            question: "Sinong taong nagkakagusto sa crush niya wala naman pag asa?",
            answer: "khad"
        },
    ];

    return puzzles[Math.floor(Math.random() * puzzles.length)];
}


// Submit the answer for the logic puzzle
function submitAnswer() {
    const input = document.getElementById("answerInput").value.trim().toLowerCase();
    const expectedAnswer = currentQuestion.answer.trim().toLowerCase();

    if (input === expectedAnswer) {
        score++;
        document.getElementById("score").textContent = score;
        currentQuestion = generateLogicPuzzle();
        document.getElementById("questionText").textContent = currentQuestion.question;
    } else {
        alert("Incorrect! Try again.");
    }
    document.getElementById("answerInput").value = "";
}


  

    </script>
</body>
</html>
