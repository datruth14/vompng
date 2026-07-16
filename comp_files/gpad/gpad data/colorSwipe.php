<?php 
require "header.php";
require "saveUserData.php";
?>

<!--<p class="nav fixed-top" style="color:gold;margin-top:5em;text-align:center;padding:16px;font-weight:700">Welcome back, match colors as many as you can to earn gamepad coins. </p>-->


<style>
        a {
      color: white;
      text-decoration: none;
      font-size: 0.8rem;
    }

    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background: linear-gradient(90deg, #000000, #4B0082);
    }

    #game {
     text-align: center;
     box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

      border-radius: 15px; 
       background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 16px;
            position: relative;
    }

    #user {
      font-size: 0.8rem;
      font-weight: 700;
      margin-bottom: 10px;
      padding:18px;   
      background:black;
      color:gold;
    }
    
     #userBalance{
      float:right;      
      font-weight: 700;

    }

    #score {
      font-size: 0.9rem;
      margin-bottom: 10px;
      font-weight: 700;
      padding: 12px;
      color:purple;
    }

    .grid {
      display: grid;
      justify-content: center;
      align-items: center;
      grid-template-columns: repeat(4, 1fr); /* Fixed 4 columns */
      grid-template-rows: repeat(4, 60px);   /* Fixed 4 rows */
      gap: 0.3rem;
      width: 100%;
      max-width: 240px; /* 4 columns of 60px each + gaps */
            position: relative;
            overflow: visible;
    }

     .candy {
      width: 60px;
      height: 60px;
      border-radius: 10px;
      cursor: pointer;
      transition: transform 0.1s, opacity 0.1s;
      background-size: cover;
      background-position: center;
    }

    .candy.red { background-image: url('../assets/media/images/gpad/candy/red.jpg'); }
    .candy.blue { background-image: url('../assets/media/images/gpad/candy/blue.jpg'); }
    .candy.green { background-image: url('../assets/media/images/gpad/candy/green.jpg'); }
    .candy.yellow { background-image: url('../assets/media/images/gpad/candy/yellow.jpg'); }
    .candy.purple { background-image: url('../assets/media/images/gpad/candy/purple.jpg'); }

    .candy.matched {
      opacity: 0;
      transform: scale(0);
    }

    @keyframes glow {
      0%, 100% { filter: brightness(1); }
      50% { filter: brightness(2); }
    }

    .candy.glowing {
      animation: glow 0.5s ease-in-out forwards;
    }

        /* Explosion effect */
        .explosion {
            position: absolute;
            width: 60px;
            height: 60px;
            pointer-events: none;
            overflow: visible;
        }

        .particle {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            animation: particle-burst var(--dur, 600ms) ease-out forwards;
            will-change: transform, opacity, filter;
        }

        @keyframes particle-burst {
            0% {
                opacity: 1;
                transform: translate(-50%, -50%) translate(0, 0) scale(1);
                filter: blur(0);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) translate(var(--dx, 0), var(--dy, 0)) scale(0.7);
                filter: blur(1px);
            }
        }

        /* Double-match shockwave and shake */
        .shockwave {
            position: absolute;
            top: 0;
            left: 0;
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0.2);
            opacity: 0.7;
            pointer-events: none;
            animation: shockwave 650ms ease-out forwards;
        }

        @keyframes shockwave {
            0% { opacity: 0.7; transform: translate(-50%, -50%) scale(0.2); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(3.2); }
        }

        .shake { animation: shake 400ms ease-out; }
        @keyframes shake {
            0% { transform: translate(0, 0); }
            20% { transform: translate(-2px, 1px); }
            40% { transform: translate(2px, -1px); }
            60% { transform: translate(-1px, 2px); }
            80% { transform: translate(1px, -2px); }
            100% { transform: translate(0, 0); }
        }

        /* Background music toggle */
        #bgmToggle {
            position: absolute;
            top: 8px;
            right: 8px;
            background: none;
            color: #ffd24d;
            border: none;
            border-radius: 16px;
            padding: 6px 10px;
            font-size: 0.75rem;
            cursor: pointer;
        }
        #bgmToggle:hover { filter: brightness(1.2); }

</style>

<!--Game Board Container-->
<div id="game" class="animate__animated animate__bounceInDown">
  <!--SaveUser core As GPC-->
  <div >
    <form action='' method="POST">
        <input style="display:none;" type="text" id="scoreValueData" name="scoreValueData" readonly />
            
        <button style='background:#FFFDD0;border:none;padding:0px 20px 0px 20px;font-size:0.7rem;margin-right:6em;color:purple;border-radius:25px;' type="submit" name='saveData' class="animate__animated animate__pulse animate__infinite	infinite animate__slow	2s">
            <b> <i class="fas fa-save"></i> Store Earnings </b>
        </button>
   </form>  
  </div>
            
    
    <!--Getting the user score-->
    <div class="teleDiv">
        <div id="score">
            <b style="color:gold;font-size:1rem;"> GPC </b> 
            <span id="scoreValue">0</span>
        </div>
    </div> 

   
    <div class="grid " style=""></div>
</div>



<!-- Audio element for match sound -->
<audio id="matchTune" src="../assets/media/music/gpad/matching_tune.mp3" preload="auto"></audio>
<!-- Audio element for explosion SFX (CDN, royalty-free) -->
<audio id="comboTune" crossorigin="anonymous" src="../assets/media/music/gpad/matching_tune.mp3" preload="auto"></audio>
<!-- Background music (looping) -->
<audio id="bgmTune" src="../assets/media/music/gpad/mochamusic-candy-clouds-405666.mp3" preload="auto" loop></audio>

 
 
<!-- Include game script -->
<script>
const grid = document.querySelector('.grid');
const scoreDisplay = document.getElementById('scoreValue');
const candyColors = ['red', 'blue', 'green', 'yellow', 'purple'];
let squares = [];
let score = 0;
let audioCtx = null;
let lastMatchSoundAt = 0;
let lastComboSoundAt = 0;
let bgmEnabled = true;
let bgmStartingTried = false;

try {
    const saved = localStorage.getItem('gpad_bgm_enabled');
    if (saved !== null) bgmEnabled = saved === 'true';
} catch {}

function ensureAudioContext() {
    if (!audioCtx) {
        const AC = window.AudioContext || window.webkitAudioContext;
        if (AC) audioCtx = new AC();
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

function playExplosionSound(opts = {}) {
    const nowTs = (typeof performance !== 'undefined' ? performance.now() : Date.now());
    if (nowTs - lastMatchSoundAt < 300) return; // cooldown
    lastMatchSoundAt = nowTs;

    const ctx = ensureAudioContext();
    if (!ctx) return;

    const intensity = Math.max(1, Math.min(3, opts.intensity || 1));
    const now = ctx.currentTime;

    // White noise buffer
    const bufferSize = 1 * ctx.sampleRate;
    const noiseBuffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
    const data = noiseBuffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
        data[i] = (Math.random() * 2 - 1) * 0.6; // white noise
    }

    const noiseSrc = ctx.createBufferSource();
    noiseSrc.buffer = noiseBuffer;
    noiseSrc.loop = false;

    const filter = ctx.createBiquadFilter();
    filter.type = 'lowpass';
    filter.Q.value = 0.6 + 0.2 * intensity;

    const gain = ctx.createGain();
    const baseGain = Math.min(0.95, 0.75 + 0.2 * (intensity - 1));
    gain.gain.setValueAtTime(0.0001, now);
    gain.gain.exponentialRampToValueAtTime(baseGain, now + 0.01);
    const noiseDur = 0.35 + 0.12 * intensity;
    gain.gain.exponentialRampToValueAtTime(0.0001, now + noiseDur);

    // Filter sweep (downwards)
    const startFreq = 9000 - 1500 * (intensity - 1);
    const endFreq = 220 + 60 * (intensity - 1);
    filter.frequency.setValueAtTime(startFreq, now);
    filter.frequency.exponentialRampToValueAtTime(endFreq, now + noiseDur);

    // Add a low-frequency thump
    const osc = ctx.createOscillator();
    osc.type = 'sine';
    const oscGain = ctx.createGain();
    const thumpStart = 90 + 25 * (intensity - 1);
    const thumpEnd = 45 + 8 * (intensity - 1);
    osc.frequency.setValueAtTime(thumpStart, now);
    osc.frequency.exponentialRampToValueAtTime(thumpEnd, now + 0.22 + 0.05 * intensity);
    const oscGainStart = Math.min(0.95, 0.8 + 0.15 * (intensity - 1));
    oscGain.gain.setValueAtTime(0.0001, now);
    oscGain.gain.exponentialRampToValueAtTime(oscGainStart, now + 0.005);
    oscGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.28 + 0.05 * intensity);

    noiseSrc.connect(filter);
    filter.connect(gain);
    gain.connect(ctx.destination);
    osc.connect(oscGain);
    oscGain.connect(ctx.destination);

    noiseSrc.start(now);
    noiseSrc.stop(now + noiseDur + 0.02);
    osc.start(now);
    osc.stop(now + 0.32 + 0.05 * intensity);
}

// Background music helpers
function getBgmEl() { return document.getElementById('bgmTune'); }
function fadeAudio(audio, to, ms = 400, thenPause = false) {
    if (!audio) return;
    const steps = Math.max(1, Math.floor(ms / 30));
    const from = audio.volume;
    let i = 0;
    if (audio.__fadeTimer) clearInterval(audio.__fadeTimer);
    audio.__fadeTimer = setInterval(() => {
        i++;
        const t = i / steps;
        audio.volume = from + (to - from) * t;
        if (i >= steps) {
            clearInterval(audio.__fadeTimer);
            audio.__fadeTimer = null;
            audio.volume = to;
            if (thenPause) audio.pause();
        }
    }, 30);
}

function startBgm() {
    const audio = getBgmEl();
    if (!audio || !bgmEnabled) return;
    audio.volume = Math.min(audio.volume || 0, 0.01);
    const p = audio.play();
    if (p && typeof p.catch === 'function') {
        p.catch(() => {});
    }
    fadeAudio(audio, 1.0, 600, false);
}

function stopBgm() {
    const audio = getBgmEl();
    if (!audio) return;
    fadeAudio(audio, 0.0, 400, true);
}

function updateBgmToggleLabel() {
    const btn = document.getElementById('bgmToggle');
    if (!btn) return;

    btn.innerHTML = bgmEnabled
        ? '🔊 Music: On'
        : '🔇 Music: Off';
}

function toggleBgm() {
    bgmEnabled = !bgmEnabled;
    try { localStorage.setItem('gpad_bgm_enabled', String(bgmEnabled)); } catch {}
    updateBgmToggleLabel();
    if (bgmEnabled) startBgm(); else stopBgm();
}

// Explosion effect utility
function triggerExplosionAt(squareEl, opts = {}) {
    if (!squareEl) return;
    const gridRect = grid.getBoundingClientRect();
    const rect = squareEl.getBoundingClientRect();

    const container = document.createElement('div');
    container.className = 'explosion';
    container.style.left = `${rect.left - gridRect.left}px`;
    container.style.top = `${rect.top - gridRect.top}px`;
    container.style.width = `${rect.width}px`;
    container.style.height = `${rect.height}px`;

    const colorClass = Array.from(squareEl.classList).find(c => candyColors.includes(c));
    const colorMap = {
        red: '#ff4d4d',
        blue: '#4da6ff',
        green: '#66ff66',
        yellow: '#ffd24d',
        purple: '#c266ff'
    };
    const baseColor = colorMap[colorClass] || '#ffffff';

    const particleCount = opts.count || 12;
    let maxDur = 0;
    for (let i = 0; i < particleCount; i++) {
        const p = document.createElement('span');
        p.className = 'particle';

        const size = (opts.minSize || 3) + Math.random() * (opts.sizeRange || 5);
        p.style.width = `${size}px`;
        p.style.height = `${size}px`;

        const angle = (Math.PI * 2) * (i / particleCount) + (Math.random() * 0.6 - 0.3);
        const distance = (opts.baseDistance || 20) + Math.random() * (opts.distanceRange || 45);
        const dx = Math.cos(angle) * distance;
        const dy = Math.sin(angle) * distance;
        const dur = Math.floor((opts.minDur || 450) + Math.random() * (opts.durRange || 350));
        maxDur = Math.max(maxDur, dur);

        p.style.setProperty('--dx', `${dx}px`);
        p.style.setProperty('--dy', `${dy}px`);
        p.style.setProperty('--dur', `${dur}ms`);
        p.style.background = baseColor;

        container.appendChild(p);
    }

    grid.appendChild(container);
    setTimeout(() => {
        container.remove();
    }, maxDur + 80);
}

function triggerDoubleMatchEffects(matchedIdxSet) {
    const indices = Array.from(matchedIdxSet);
    if (indices.length === 0) return;
    const gridRect = grid.getBoundingClientRect();
    let cx = 0, cy = 0;
    indices.forEach(i => {
        const el = squares[i];
        if (!el) return;
        const r = el.getBoundingClientRect();
        cx += r.left + r.width / 2 - gridRect.left;
        cy += r.top + r.height / 2 - gridRect.top;
    });
    cx /= indices.length;
    cy /= indices.length;

    const wave = document.createElement('div');
    wave.className = 'shockwave';
    wave.style.left = `${cx}px`;
    wave.style.top = `${cy}px`;
    grid.appendChild(wave);
    setTimeout(() => wave.remove(), 700);

    grid.classList.add('shake');
    setTimeout(() => grid.classList.remove('shake'), 420);

    indices.forEach(i => triggerExplosionAt(squares[i], {
        count: 18,
        minSize: 3,
        sizeRange: 6,
        baseDistance: 28,
        distanceRange: 55,
        minDur: 480,
        durRange: 420
    }));
}

// Create the game board
function createBoard() {
    const columns = 4; // Fixed 4 columns
    const height = 4;  // Fixed 4 rows
    squares = [];      // Clear squares array each time board is created

    // Clear any existing grid content
    grid.innerHTML = '';

    // Generate squares based on fixed columns and rows
    for (let i = 0; i < columns * height; i++) {
        const square = document.createElement('div');
        square.setAttribute('draggable', true);
        square.setAttribute('id', i);
        let randomColor = candyColors[Math.floor(Math.random() * candyColors.length)];
        square.classList.add('candy', randomColor, 'animate__animated', 'animate__pulse', 'animate__infinite', 'infinite');
        grid.appendChild(square);
        squares.push(square);
    }
}


createBoard();

let colorBeingDragged, colorBeingReplaced, squareIdBeingDragged, squareIdBeingReplaced;

// Add drag and touch event listeners
squares.forEach(square => {
    square.addEventListener('dragstart', dragStart);
    square.addEventListener('dragend', dragEnd);
    square.addEventListener('dragover', dragOver);
    square.addEventListener('dragenter', dragEnter);
    square.addEventListener('dragleave', dragLeave);
    square.addEventListener('drop', dragDrop);
    square.addEventListener('touchstart', touchStart);
    square.addEventListener('touchmove', touchMove);
    square.addEventListener('touchend', touchEnd);
});

// Start BGM on first user interaction if enabled
function armBgmAutoStartOnce() {
    if (bgmStartingTried) return;
    bgmStartingTried = true;
    const handler = () => {
        startBgm();
        document.removeEventListener('pointerdown', handler);
        document.removeEventListener('keydown', handler);
    };
    document.addEventListener('pointerdown', handler, { once: true });
    document.addEventListener('keydown', handler, { once: true });
}
armBgmAutoStartOnce();

// Drag and touch event handlers
function dragStart() {
    colorBeingDragged = this.className.split(' ')[1];
    squareIdBeingDragged = parseInt(this.id);
    if (bgmEnabled) startBgm();
}

function dragDrop() {
    colorBeingReplaced = this.className.split(' ')[1];
    squareIdBeingReplaced = parseInt(this.id);
    swapColors();
}

function dragEnd() {
    checkValidMove();
}

function touchStart(e) {
    e.preventDefault();
    colorBeingDragged = this.className.split(' ')[1];
    squareIdBeingDragged = parseInt(this.id);
    if (bgmEnabled) startBgm();
}

function touchMove(e) {
    e.preventDefault();
    const touchLocation = e.targetTouches[0];
    const elementAtTouch = document.elementFromPoint(touchLocation.clientX, touchLocation.clientY);

    if (elementAtTouch && elementAtTouch.classList.contains('candy')) {
        colorBeingReplaced = elementAtTouch.className.split(' ')[1];
        squareIdBeingReplaced = parseInt(elementAtTouch.id);
    }
}

function touchEnd() {
    if (squareIdBeingReplaced !== null) {
        swapColors();
        checkValidMove();
    }
}

function swapColors() {
    let tempColor = squares[squareIdBeingDragged].classList[1];
    squares[squareIdBeingDragged].classList.replace(tempColor, colorBeingReplaced);
    squares[squareIdBeingReplaced].classList.replace(colorBeingReplaced, tempColor);
}

function checkValidMove() {
    const columns = 4; // Fixed number of columns

    const validMoves = [
        squareIdBeingDragged - 1,
        squareIdBeingDragged - columns,
        squareIdBeingDragged + 1,
        squareIdBeingDragged + columns,
    ];
    const isValidMove = validMoves.includes(squareIdBeingReplaced);

    if (squareIdBeingReplaced && isValidMove) {
        squareIdBeingReplaced = null;
        checkMatches();
    } else {
        // Revert the swap if invalid
        swapColors();
    }
}

function dragOver(e) { e.preventDefault(); }
function dragEnter(e) { e.preventDefault(); }
function dragLeave() {}

// Match detection and handling
function checkMatches() {
    const columns = 4; // Fixed number of columns
    let matchFound = false; // Track if a match is found
    let matchGroups = 0; // Track how many groups per check (for double matches)
    const matchedIndices = new Set();

    // Check for row matches (Horizontal)
    for (let i = 0; i < squares.length; i++) {
        if (i % columns > columns - 3) continue; // Skip edge columns

        let rowOfThree = [i, i + 1, i + 2];
        let decidedColor = squares[i].className.split(' ')[1];
        const isBlank = squares[i].classList.contains('matched');

        if (rowOfThree.every(index => squares[index].className.includes(decidedColor) && !isBlank)) {
            matchFound = true;
            matchGroups++;
            rowOfThree.forEach(index => {
                squares[index].classList.add('matched');
                matchedIndices.add(index);
                triggerExplosionAt(squares[index]);
            });
        }
    }

    // Check for column matches (Vertical)
    for (let i = 0; i < squares.length - columns * 2; i++) {
        let columnOfThree = [i, i + columns, i + columns * 2];
        let decidedColor = squares[i].className.split(' ')[1];
        const isBlank = squares[i].classList.contains('matched');

        if (columnOfThree.every(index => squares[index].className.includes(decidedColor) && !isBlank)) {
            matchFound = true;
            matchGroups++;
            columnOfThree.forEach(index => {
                squares[index].classList.add('matched');
                matchedIndices.add(index);
                triggerExplosionAt(squares[index]);
            });
        }
    }

    // Play match tune and update score only if a match is found
    if (matchFound) {
        if (matchGroups >= 2) {
            triggerDoubleMatchEffects(matchedIndices);
        }
        setTimeout(clearMatches, 500); // Add delay for visual feedback
        updateScore(); // Update the score when a match is found
        playMatchTune(matchGroups); // Play enhanced explosion sound
    }
}

// Clear matched candies
function clearMatches() {
    squares.forEach(square => {
        if (square.classList.contains('matched')) {
            square.className = 'candy matched'; // Reset to matched class
        }
    });
    dropCandies();
}

// Drop candies and refill the board
function dropCandies() {
    const columns = 4;

    for (let i = squares.length - 1; i >= 0; i--) {
        if (squares[i].classList.contains('matched')) {
            let randomColor = candyColors[Math.floor(Math.random() * candyColors.length)];
            squares[i].className = `candy ${randomColor}`;
        }
    }
    refillCandies();
}

function refillCandies() {
    for (let i = 0; i < squares.length; i++) {
        if (squares[i].classList.contains('matched')) {
            let randomColor = candyColors[Math.floor(Math.random() * candyColors.length)];
            squares[i].className = `candy ${randomColor}`;
        }
    }
}

// Update the score when a match is found
function updateScore() {
    score += 10; // Increment score by 3 for each match
    scoreDisplay.textContent = score;
    document.getElementById('scoreValueData').value = score;
}

// Play the match tune
function playMatchTune(groups = 1) {
    // Always try CDN explosion SFX first; fallback to synthesized boom.
    const grp = Math.max(1, groups || 1);
    const nowTs = (typeof performance !== 'undefined' ? performance.now() : Date.now());
    if (nowTs - lastComboSoundAt < 200) return; // simple cooldown for frequent matches
    lastComboSoundAt = nowTs;
    const combo = document.getElementById('comboTune');
    if (combo && typeof combo.play === 'function') {
        try { combo.currentTime = 0; } catch {}
        const p = combo.play();
        if (p && typeof p.catch === 'function') {
            p.catch(() => playExplosionSound({ intensity: Math.min(3, grp) }));
        }
    } else {
        playExplosionSound({ intensity: Math.min(3, grp) });
    }
}

// Initialize the game and check matches every 100ms
setInterval(() => {
    checkMatches();
}, 100);

// Add BGM toggle button to UI
const bgmBtn = document.createElement('button');
bgmBtn.id = 'bgmToggle';
bgmBtn.type = 'button';
bgmBtn.addEventListener('click', toggleBgm);
document.getElementById('game').appendChild(bgmBtn);
updateBgmToggleLabel();

// Set initial volumes for external audio elements
(function initExternalAudioVolumes(){
    const combo = document.getElementById('comboTune');
    if (combo) combo.volume = 1.0;
    const bgm = document.getElementById('bgmTune');
    if (bgm) bgm.volume = 0.0; // will fade to 1.0
})();
</script>


 
 
 
 
 
 
<?php require "footer.php";?>