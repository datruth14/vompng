<?php 
require "header.php";
require "saveUserData.php";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GunGame</title>
    <script src="https://cdn.jsdelivr.net/npm/vant@3.0.0/lib/vant.min.js"></script>
    <style>
        canvas#birdBackground {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #000000, #1a0033);
            overflow: hidden;
            padding-top: 10px;
        }

        #gameContainer {
            position: relative;
            width: min(90vw, 600px);
            height: 50vh;
            max-width: 600px;
            max-height: 50vh;
            background: linear-gradient(180deg, #0a0a0a 0%, #1a0033 50%, #0a0a0a 100%);
            border: 3px solid #00ff00;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5), inset 0 0 20px rgba(0, 255, 0, 0.1);
        }

        #gameInfo {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 100;
            color: #00ff00;
            font-weight: bold;
            text-shadow: 0 0 10px #00ff00;
        }

        #score {
            font-size: 18px;
            margin-bottom: 5px;
        }

        #kills {
            font-size: 16px;
            margin-bottom: 5px;
        }

        #gunStatus {
            font-size: 14px;
            color: #ffff00;
        }

        #lives {
            font-size: 16px;
            color: #ff0000;
            margin-bottom: 5px;
            font-weight: bold;
            text-shadow: 0 0 10px #ff0000;
        }

        #player {
            position: absolute;
            width: 40px;
            height: 50px;
            background: linear-gradient(180deg, #00ff00, #ff00ff);
            bottom: 20px;
            left: 280px;
            clip-path: polygon(50% 0%, 100% 100%, 75% 70%, 50% 85%, 25% 70%, 0% 100%);
            z-index: 10;
            box-shadow: 0 0 10px #00ff00, inset 0 0 10px rgba(0, 255, 0, 0.5);
        }

        .bullet {
            position: absolute;
            width: 4px;
            height: 15px;
            background: #00ff00;
            box-shadow: 0 0 8px #00ff00;
            z-index: 5;
        }

        .enemy {
            position: absolute;
            width: 25px;
            height: 25px;
            background: linear-gradient(135deg, #ff0000, #ff6600);
            clip-path: polygon(50% 0%, 100% 40%, 80% 100%, 20% 100%, 0% 40%);
            z-index: 8;
            box-shadow: 0 0 10px #ff0000;
        }

        .explosion {
            position: absolute;
            width: 40px;
            height: 40px;
            pointer-events: none;
            overflow: visible;
            z-index: 9;
        }

        .particle {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 1;
            animation: particle-burst 0.6s ease-out forwards;
        }

        @keyframes particle-burst {
            0% {
                opacity: 1;
                transform: translate(-50%, -50%) translate(0, 0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) translate(var(--dx, 0), var(--dy, 0)) scale(0.2);
            }
        }

        #gameOver {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.95);
            padding: 40px;
            border: 2px solid #ff0000;
            border-radius: 10px;
            color: #00ff00;
            text-align: center;
            z-index: 200;
            display: none;
            font-size: 24px;
        }

        #gameOverText {
            font-size: 32px;
            margin-bottom: 20px;
            color: #ff0000;
        }

        button {
            background: #00ff00;
            color: #000;
            border: 2px solid #00ff00;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
            transition: all 0.3s;
        }

        button:hover {
            background: #ffff00;
            box-shadow: 0 0 10px #ffff00;
        }

        #pauseButton {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 150;
            padding: 8px 16px;
            font-size: 14px;
        }

        #pauseOverlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            z-index: 250;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        #pauseOverlay.show {
            display: flex;
        }

        #pauseOverlay h1 {
            color: #ffff00;
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 0 0 20px #ffff00;
        }

        #pauseOverlay p {
            color: #00ff00;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .upgrade-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.95);
            padding: 30px;
            border: 3px solid #ffff00;
            border-radius: 10px;
            color: #ffff00;
            text-align: center;
            z-index: 300;
            display: none;
        }

        .upgrade-popup h2 {
            color: #ff00ff;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .upgrade-popup p {
            margin: 10px 0;
            font-size: 16px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            #gameContainer {
                width: min(95vw, 500px);
                height: min(50vh, 500px);
                max-width: 500px;
                max-height: min(50vh, 500px);
            }

            body {
                padding: 10px;
            }

            #pauseButton {
                padding: 6px 12px;
                font-size: 12px;
            }

            #gameInfo {
                font-size: 12px;
            }

            #score, #kills, #lives, #gunStatus {
                font-size: 12px !important;
                margin-bottom: 3px;
            }

            .upgrade-popup {
                padding: 20px;
                width: 90vw;
            }

            .upgrade-popup h2 {
                font-size: 20px;
            }

            .upgrade-popup p {
                font-size: 14px;
            }

            #gameOver {
                padding: 30px;
                width: 90vw;
            }

            #gameOverText {
                font-size: 24px;
            }

            #gameOver > div:not(#gameOverText) {
                font-size: 16px;
            }
        }

    </style>
</head>
<body>
<canvas id="birdBackground"></canvas>
<script>
// Vant.js Bird Animation Background
const canvas = document.getElementById('birdBackground');
const ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

class Bird {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.vx = (Math.random() - 0.5) * 2;
        this.vy = (Math.random() - 0.5) * 2;
        this.size = Math.random() * 2 + 1;
        this.opacity = Math.random() * 0.5 + 0.3;
    }

    update(width, height) {
        this.x += this.vx;
        this.y += this.vy;

        // Bounce off edges
        if (this.x < 0 || this.x > width) this.vx *= -1;
        if (this.y < 0 || this.y > height) this.vy *= -1;

        this.x = Math.max(0, Math.min(width, this.x));
        this.y = Math.max(0, Math.min(height, this.y));
    }

    draw(ctx) {
        ctx.fillStyle = `rgba(0, 255, 100, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = `rgba(0, 255, 150, ${this.opacity * 0.6})`;
        ctx.lineWidth = 0.5;
        ctx.stroke();
    }
}

const birds = [];
for (let i = 0; i < 30; i++) {
    birds.push(new Bird(Math.random() * canvas.width, Math.random() * canvas.height));
}

function animateBirds() {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    for (let bird of birds) {
        bird.update(canvas.width, canvas.height);
        bird.draw(ctx);
    }

    requestAnimationFrame(animateBirds);
}

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});

animateBirds();
</script>

<!--Game Container-->
<div id="gameContainer">
    <div id="gameInfo">
        <div id="score">Score: 0</div>
        <div id="kills">Kills: 0</div>
        <div id="lives">Lives: 4</div>
        <div id="gunStatus">Gun: Single Shot</div>
    </div>
    <button id="pauseButton">PAUSE</button>
    <div id="pauseOverlay">
        <h1>PAUSED</h1>
        <p>Press SPACE or click RESUME to continue</p>
    </div>
    <div id="player"></div>
</div>

<div class="upgrade-popup" id="upgradePopup">
    <h2>🎯 GUN UPGRADE! 🎯</h2>
    <p id="upgradeText"></p>
</div>

<div id="gameOver">
    <div id="gameOverText">GAME OVER</div>
    <div>Final Score: <span id="finalScore">0</span></div>
    <div>Total Kills: <span id="finalKills">0</span></div>
    <button onclick="location.reload()">RESTART</button>
</div>



<script>
// Web Audio API Sound System
const audioContext = new (window.AudioContext || window.webkitAudioContext)();

// Shoot sound - high pitched beep
function playShootSound() {
    if (gamePaused) return;
    const now = audioContext.currentTime;
    const osc = audioContext.createOscillator();
    const gain = audioContext.createGain();
    
    osc.connect(gain);
    gain.connect(audioContext.destination);
    
    osc.frequency.setValueAtTime(800, now);
    osc.frequency.exponentialRampToValueAtTime(600, now + 0.1);
    
    gain.gain.setValueAtTime(0.3, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
    
    osc.start(now);
    osc.stop(now + 0.1);
}

// Explosion sound - low rumble with noise
function playExplosionSound() {
    if (gamePaused) return;
    const now = audioContext.currentTime;
    
    // Sub bass
    const osc = audioContext.createOscillator();
    const gain = audioContext.createGain();
    osc.connect(gain);
    gain.connect(audioContext.destination);
    
    osc.frequency.setValueAtTime(150, now);
    osc.frequency.exponentialRampToValueAtTime(50, now + 0.5);
    
    gain.gain.setValueAtTime(0.4, now);
    gain.gain.exponentialRampToValueAtTime(0, now + 0.5);
    
    osc.start(now);
    osc.stop(now + 0.5);
    
    // Noise burst
    const bufferSize = audioContext.sampleRate * 0.2;
    const noiseBuffer = audioContext.createBuffer(1, bufferSize, audioContext.sampleRate);
    const output = noiseBuffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
        output[i] = Math.random() * 2 - 1;
    }
    
    const noiseSource = audioContext.createBufferSource();
    noiseSource.buffer = noiseBuffer;
    const noiseGain = audioContext.createGain();
    noiseSource.connect(noiseGain);
    noiseGain.connect(audioContext.destination);
    
    noiseGain.gain.setValueAtTime(0.2, now);
    noiseGain.gain.exponentialRampToValueAtTime(0, now + 0.3);
    
    noiseSource.start(now);
    noiseSource.stop(now + 0.3);
}

// Upgrade sound - ascending chime
function playUpgradeSound() {
    if (gamePaused) return;
    const now = audioContext.currentTime;
    const notes = [523, 659, 784]; // C5, E5, G5
    
    notes.forEach((freq, idx) => {
        const osc = audioContext.createOscillator();
        const gain = audioContext.createGain();
        
        osc.connect(gain);
        gain.connect(audioContext.destination);
        
        osc.frequency.setValueAtTime(freq, now + idx * 0.1);
        
        gain.gain.setValueAtTime(0.3, now + idx * 0.1);
        gain.gain.exponentialRampToValueAtTime(0, now + idx * 0.1 + 0.2);
        
        osc.start(now + idx * 0.1);
        osc.stop(now + idx * 0.1 + 0.2);
    });
}

// Wrapper function for compatibility
function playSound(id) {
    try {
        if (id === 'shootSound') playShootSound();
        else if (id === 'explosionSound') playExplosionSound();
        else if (id === 'upgradeSound') playUpgradeSound();
    } catch (e) {}
}

// Game variables
const gameContainer = document.getElementById('gameContainer');
const player = document.getElementById('player');
const scoreDisplay = document.getElementById('score');
const killsDisplay = document.getElementById('kills');
const gunStatusDisplay = document.getElementById('gunStatus');
const upgradePopup = document.getElementById('upgradePopup');
const gameOverScreen = document.getElementById('gameOver');
const livesDisplay = document.getElementById('lives');

let score = 0;
let kills = 0;
let lives = 4;
let playerX = 0;
let playerY = 0;
const playerWidth = 40;
const playerHeight = 50;
const playerPaddingBottom = 20;
let playerSpeed = 7;
let gameRunning = true;
let gamePaused = false;
let isShootingCooldown = false;

function getGameArea() {
    const rect = gameContainer.getBoundingClientRect();
    return { width: rect.width, height: rect.height };
}

function clampPlayerPosition() {
    const area = getGameArea();
    playerX = Math.max(0, Math.min(area.width - playerWidth, playerX));
    playerY = area.height - playerHeight - playerPaddingBottom;
    player.style.left = playerX + 'px';
    player.style.top = playerY + 'px';
}

window.addEventListener('resize', clampPlayerPosition);

// Gun upgrade system
let gunLevel = 1;
const gunStats = {
    1: { name: 'Single Shot', fireRate: 300, projectiles: 1, spawn: 'continuous' },
    2: { name: 'Double Shot', fireRate: 280, projectiles: 2, spawn: 'dual' },
    3: { name: 'Triple Shot', fireRate: 250, projectiles: 3, spawn: 'trio' }
};

let currentGunStats = gunStats[1];
let bullets = [];
let enemies = [];
let enemySpawnRate = 1500;
let difficultyMultiplier = 1;
let gameStartTime = Date.now();
let currentWave = 0;
let waveTimeTracker = 0;

// AI Difficulty System
let aiDifficulty = 1;
let lastAiUpdateTime = Date.now();
let performanceHistory = [];
const MAX_ENEMIES_ON_SCREEN = 15;

function calculateAIDifficulty() {
    const elapsed = (Date.now() - gameStartTime) / 1000;
    if (elapsed < 5) return; // Don't adjust in first 5 seconds
    
    // Calculate player performance metrics
    const killsPerMinute = (kills / (elapsed / 60)) || 0;
    const avgEnemiesOnScreen = enemies.length;
    const survivalRatio = Math.min(2, (elapsed / 60)); // Normalized survival time
    
    // Performance score (0-100)
    // High KPM = harder, More enemies alive = easier to player, more time = tougher
    let performanceScore = (killsPerMinute * 5) + (avgEnemiesOnScreen * 2) + (survivalRatio * 10);
    performanceHistory.push(performanceScore);
    if (performanceHistory.length > 20) performanceHistory.shift();
    
    // Calculate average performance trend
    const avgPerformance = performanceHistory.reduce((a, b) => a + b, 0) / performanceHistory.length;
    
    // AI adjusts difficulty based on player skill
    if (avgPerformance > 50) {
        // Player is dominating - make it much harder
        aiDifficulty = 1.5 + (avgPerformance / 100);
    } else if (avgPerformance > 25) {
        // Player is doing well - slightly increase difficulty
        aiDifficulty = 1.1 + (avgPerformance / 200);
    } else {
        // Player is struggling - keep it manageable
        aiDifficulty = Math.max(0.8, 1 + (avgPerformance / 300));
    }
}

// Run AI difficulty calculation every 3 seconds
setInterval(() => {
    if (gameRunning && (Date.now() - gameStartTime) > 5000) {
        calculateAIDifficulty();
    }
}, 3000);

// Pause/Play toggle function
function togglePause() {
    if (!gameRunning) return;
    
    gamePaused = !gamePaused;
    const pauseButton = document.getElementById('pauseButton');
    const pauseOverlay = document.getElementById('pauseOverlay');
    
    if (gamePaused) {
        pauseOverlay.classList.add('show');
        pauseButton.textContent = 'RESUME';
    } else {
        pauseOverlay.classList.remove('show');
        pauseButton.textContent = 'PAUSE';
    }
}

// Keyboard control
const keys = {};
document.addEventListener('keydown', (e) => {
    keys[e.key] = true;
    // Spacebar to pause/resume
    if (e.key === ' ') {
        e.preventDefault();
        togglePause();
    }
});

document.addEventListener('keyup', (e) => {
    keys[e.key] = false;
});

// Pause button click handler
document.getElementById('pauseButton').addEventListener('click', togglePause);

// Mobile touch support
gameContainer.addEventListener('touchmove', (e) => {
    e.preventDefault();
    const touch = e.touches[0];
    const rect = gameContainer.getBoundingClientRect();
    playerX = Math.max(0, Math.min(560, touch.clientX - rect.left - 20));
});

// Update player position
function updatePlayer() {
    const area = getGameArea();
    if (keys['ArrowLeft']) playerX = Math.max(0, playerX - playerSpeed);
    if (keys['ArrowRight']) playerX = Math.min(area.width - playerWidth, playerX + playerSpeed);
    playerY = area.height - playerHeight - playerPaddingBottom;
    player.style.left = playerX + 'px';
    player.style.top = playerY + 'px';
}

// Shooting mechanics
function shoot() {
    if (!gameRunning || gamePaused || isShootingCooldown) return;
    isShootingCooldown = true;
    
    playSound('shootSound');
    
    const centerX = playerX + 20;
    
    // Create bullets based on gun level with spray effect
    switch(currentGunStats.spawn) {
        case 'continuous':
            createBulletWithAngle(centerX, playerY, 0);
            break;
        case 'dual':
            createBulletWithAngle(centerX, playerY, -15);
            createBulletWithAngle(centerX, playerY, 15);
            break;
        case 'trio':
            createBulletWithAngle(centerX, playerY, -25);
            createBulletWithAngle(centerX, playerY, 0);
            createBulletWithAngle(centerX, playerY, 25);
            break;
        case 'spread':
            for (let i = 0; i < 5; i++) {
                const angle = (i - 2) * 15;
                createBulletWithAngle(centerX, playerY, angle);
            }
            break;
        case 'mega':
            for (let i = 0; i < 8; i++) {
                const angle = (i - 3.5) * 12;
                createBulletWithAngle(centerX, playerY, angle);
            }
            break;
    }
    
    setTimeout(() => {
        isShootingCooldown = false;
    }, currentGunStats.fireRate);
}

function createBulletWithAngle(x, y, angle) {
    const bullet = document.createElement('div');
    bullet.className = 'bullet';
    bullet.style.left = x + 'px';
    bullet.style.top = y + 'px';
    gameContainer.appendChild(bullet);
    
    const radians = (angle * Math.PI) / 180;
    const speed = 8;
    
    bullets.push({
        element: bullet,
        x: x,
        y: y,
        vx: Math.sin(radians) * speed,
        vy: -Math.cos(radians) * speed
    });
}

// Enemy spawning
function spawnEnemy() {
    if (!gameRunning) return;
    
    const enemy = document.createElement('div');
    enemy.className = 'enemy';
    
    // Add wave-based styling
    const waveStyle = currentWave % 3;
    if (waveStyle === 0) {
        enemy.style.background = 'linear-gradient(135deg, #ff0000, #ff6600)';
    } else if (waveStyle === 1) {
        enemy.style.background = 'linear-gradient(135deg, #ff00ff, #ff0066)';
    } else {
        enemy.style.background = 'linear-gradient(135deg, #ffff00, #ff9900)';
    }
    
    gameContainer.appendChild(enemy);
    
    // Randomly choose which side to spawn from (top, left, right - NOT bottom)
    const side = Math.floor(Math.random() * 3);
    let x, y, vx, vy;
    
    const speed = 1 + (0.5 * difficultyMultiplier);
    
    if (side === 0) {
        // Top
        x = Math.random() * 600;
        y = -25;
        vx = (Math.random() - 0.5) * 0.3 * speed;
        vy = speed;
    } else if (side === 1) {
        // Left (only top 50% of screen)
        x = -25;
        y = Math.random() * 350;
        vx = speed;
        vy = (Math.random() - 0.5) * 0.3 * speed;
    } else {
        // Right (only top 50% of screen)
        x = 625;
        y = Math.random() * 350;
        vx = -speed;
        vy = (Math.random() - 0.5) * 0.3 * speed;
    }
    
    enemy.style.left = x + 'px';
    enemy.style.top = y + 'px';
    
    enemies.push({
        element: enemy,
        x: x,
        y: y,
        vx: vx,
        vy: vy,
        health: 1
    });
}


// Create explosion effect
function createExplosion(x, y) {
    const container = document.createElement('div');
    container.className = 'explosion';
    container.style.left = (x - 20) + 'px';
    container.style.top = (y - 20) + 'px';
    gameContainer.appendChild(container);
    
    const colors = ['#ff0000', '#ff6600', '#ffff00', '#ffcc00'];
    for (let i = 0; i < 12; i++) {
        const particle = document.createElement('span');
        particle.className = 'particle';
        const angle = (Math.PI * 2) * (i / 12);
        const distance = 30 + Math.random() * 40;
        const dx = Math.cos(angle) * distance;
        const dy = Math.sin(angle) * distance;
        
        particle.style.setProperty('--dx', dx + 'px');
        particle.style.setProperty('--dy', dy + 'px');
        particle.style.background = colors[Math.floor(Math.random() * colors.length)];
        
        container.appendChild(particle);
    }
    
    setTimeout(() => container.remove(), 650);
}

// Show gun upgrade popup
function showUpgrade(newLevel) {
    const upgradeText = document.getElementById('upgradeText');
    upgradeText.textContent = `Gun upgraded to: ${gunStats[newLevel].name}!`;
    upgradePopup.style.display = 'block';
    playSound('upgradeSound');
    setTimeout(() => {
        upgradePopup.style.display = 'none';
    }, 3000);
}

// Check collision between bullet and enemy
function checkCollisions() {
    for (let i = bullets.length - 1; i >= 0; i--) {
        const bullet = bullets[i];
        
        for (let j = enemies.length - 1; j >= 0; j--) {
            const enemy = enemies[j];
            
            if (bullet.x < enemy.x + 25 &&
                bullet.x + 4 > enemy.x &&
                bullet.y < enemy.y + 25 &&
                bullet.y + 15 > enemy.y) {
                
                // Collision detected
                createExplosion(enemy.x + 17.5, enemy.y + 17.5);
                playSound('explosionSound');
                
                bullet.element.remove();
                bullets.splice(i, 1);
                
                enemy.element.remove();
                enemies.splice(j, 1);
                
                score += 100;
                kills++;
                updateUI();
                
                // Gun upgrade every 5 kills (max level 3)
                if (kills % 5 === 0 && gunLevel < 3) {
                    gunLevel++;
                    currentGunStats = gunStats[gunLevel];
                    restartAutoFire();
                }
                
                break;
            }
        }
    }
}

// Check if enemy hit player
function checkEnemyCollision() {
    for (let i = enemies.length - 1; i >= 0; i--) {
        const enemy = enemies[i];
        if (enemy.x < playerX + 40 &&
            enemy.x + 25 > playerX &&
            enemy.y < playerY + 50 &&
            enemy.y + 25 > playerY) {
            createExplosion(enemy.x + 12.5, enemy.y + 12.5);
            enemy.element.remove();
            enemies.splice(i, 1);
        }
    }
}

// Update UI
function updateUI() {
    scoreDisplay.textContent = 'Score: ' + score;
    killsDisplay.textContent = 'Kills: ' + kills;
    livesDisplay.textContent = 'Lives: ' + lives;
    gunStatusDisplay.textContent = 'Gun: ' + currentGunStats.name;
}

// Game over
function gameOver() {
    gameRunning = false;
    document.getElementById('finalScore').textContent = score;
    document.getElementById('finalKills').textContent = kills;
    gameOverScreen.style.display = 'block';
}

// Main game loop
function gameLoop() {
    if (!gameRunning || gamePaused) {
        requestAnimationFrame(gameLoop);
        return;
    }
    
    // Update player
    updatePlayer();
    
    // Update bullets
    for (let i = bullets.length - 1; i >= 0; i--) {
        const bullet = bullets[i];
        bullet.x += bullet.vx;
        bullet.y += bullet.vy;
        bullet.element.style.left = bullet.x + 'px';
        bullet.element.style.top = bullet.y + 'px';
        
        if (bullet.y < 0 || bullet.x < 0 || bullet.x > 600) {
            bullet.element.remove();
            bullets.splice(i, 1);
        }
    }
    
    // Update enemies
    for (let i = enemies.length - 1; i >= 0; i--) {
        const enemy = enemies[i];
        enemy.x += enemy.vx;
        enemy.y += enemy.vy;
        enemy.element.style.left = enemy.x + 'px';
        enemy.element.style.top = enemy.y + 'px';
        
        // Only game over if enemy reaches the bottom (inside container)
        if (enemy.y > getGameArea().height) {
            enemy.element.remove();
            enemies.splice(i, 1);
            lives--;
            updateUI();
            if (lives <= 0) {
                gameOver();
            }
        } else if (enemy.x < -50 || enemy.x > 650 || enemy.y < -50) {
            // Remove enemies that escape left, right, or top (no game over)
            enemy.element.remove();
            enemies.splice(i, 1);
        }
    }
    
    // Check collisions
    checkCollisions();
    checkEnemyCollision();
    
    // Difficulty increase
    const elapsedSeconds = (Date.now() - gameStartTime) / 1000;
    difficultyMultiplier = 1 + (kills / 20) + (Math.floor(elapsedSeconds / 20));
    enemySpawnRate = Math.max(800, 1500 - kills * 10);
    
    // Wave system - changes every 30 seconds
    currentWave = Math.floor(elapsedSeconds / 30);
    waveTimeTracker = elapsedSeconds % 30;
    
    // Apply AI difficulty multiplier
    difficultyMultiplier *= aiDifficulty;
    enemySpawnRate = Math.max(600, enemySpawnRate / aiDifficulty);
    
    requestAnimationFrame(gameLoop);
}

// Enemy spawn timer with AI-driven spawn chance
setInterval(() => {
    if (!gameRunning) return;
    const spawnChance = Math.min(1, 0.35 + Math.max(0, aiDifficulty - 1) * 0.25);
    if (Math.random() < spawnChance) {
        spawnEnemy();
    }
}, 800);

// Auto-fire system
let autoFireInterval = null;

function startAutoFire() {
    if (autoFireInterval) clearInterval(autoFireInterval);
    autoFireInterval = setInterval(() => {
        if (gameRunning) shoot();
    }, currentGunStats.fireRate);
}

function restartAutoFire() {
    startAutoFire();
}

// Start game
updateUI();
startAutoFire();
gameLoop();
</script>
</body>
</html>



 
 
<?php require "footer.php";?>
