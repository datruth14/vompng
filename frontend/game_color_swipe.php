<?php
$pageTitle = 'Color Swipe - Gamepad';
ob_start();
?>
<section class="py-6 max-w-lg mx-auto">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-black text-white">Color Swipe</h1>
        <p class="text-gray-400 text-sm">Click a column to drop. Match 3+ same colors to score!</p>
    </div>

    <!-- Score + GPTokens -->
    <div class="flex justify-between items-center mb-4">
        <div class="glass-morphism rounded-2xl px-5 py-2 border border-white/10">
            <p class="text-xs text-gray-500 font-black uppercase tracking-wider">Score</p>
            <p id="scoreDisplay" class="text-2xl font-black text-white">0</p>
        </div>
        <div class="glass-morphism rounded-2xl px-5 py-2 border border-white/10">
            <p class="text-xs text-gray-500 font-black uppercase tracking-wider">GPTokens</p>
            <p class="text-2xl font-black text-emerald-400"><?php echo number_format((int)($currentUser['gptokens'] ?? 0)); ?></p>
        </div>
    </div>

    <!-- Game Canvas -->
    <div class="glass-morphism rounded-[2rem] p-3 border border-white/10">
        <div class="relative">
            <canvas id="gameCanvas" class="w-full cursor-pointer block rounded-xl"></canvas>

            <!-- Start Screen -->
            <div id="startScreen" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-950/80 rounded-xl z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" /></svg>
                </div>
                <h2 class="text-2xl font-black text-white mb-1">Color Swipe</h2>
                <p class="text-gray-400 text-sm mb-6">Match colors, earn GPTokens!</p>
                <button onclick="startGame()" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-600 text-white font-black text-sm hover:scale-105 transition-transform">Play Now</button>
                <p class="text-xs text-gray-500 mt-4">Click column to drop &bull; Arrow keys to move</p>
            </div>

            <!-- Game Over Overlay -->
            <div id="gameOverOverlay" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-950/85 rounded-xl z-10">
                <svg class="w-12 h-12 text-yellow-400 mb-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                <h2 class="text-2xl font-black text-white mb-1">Game Over</h2>
                <p class="text-gray-400 text-sm">Score: <span id="finalScore" class="text-white font-black">0</span></p>
                <div class="flex items-center gap-2 mt-2 mb-5 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-emerald-400 text-sm font-bold">+<span id="gpTokensEarned">0</span> GPTokens</p>
                </div>
                <p class="text-xs text-gray-500 mb-4">Balance: <span id="newBalance" class="text-emerald-400 font-black">0</span> GPT</p>
                <button onclick="startGame()" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-600 text-white font-black text-sm hover:scale-105 transition-transform">Play Again</button>
            </div>
        </div>
    </div>

    <!-- Controls hint -->
    <div class="flex items-center justify-center gap-4 mt-4 text-xs text-gray-500">
        <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg> Move</span>
        <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l-7.5 7.5 7.5 7.5"/></svg> Drop</span>
        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded bg-white/20 text-[10px] leading-3 text-center text-white font-black">↓</span> Hard Drop</span>
    </div>

    <div class="text-center mt-6">
        <a href="/game" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">&larr; Back to Gamepad</a>
    </div>
</section>

<script>
(function() {
    const COLS = 6;
    const ROWS = 9;
    const COLORS = [
        { name: 'pink',   hex: '#FF69B4' },
        { name: 'purple', hex: '#A855F7' },
        { name: 'yellow', hex: '#FACC15' },
        { name: 'blue',   hex: '#2563EB' },
        { name: 'red',    hex: '#EF4444' }
    ];

    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    const scoreDisplay = document.getElementById('scoreDisplay');
    const finalScore = document.getElementById('finalScore');
    const gpTokensEarned = document.getElementById('gpTokensEarned');
    const newBalance = document.getElementById('newBalance');
    const startScreen = document.getElementById('startScreen');
    const gameOverOverlay = document.getElementById('gameOverOverlay');

    let grid = [];
    let currentPiece = null;
    let score = 0;
    let gameOver = false;
    let gameActive = false;
    let animFrameId = null;
    let accumTime = 0;
    const DROP_INTERVAL = 700;
    let lastTime = 0;
    let BLOCK_SIZE = 50;
    let canvasW = 0;
    let canvasH = 0;
    let isProcessing = false;
    let chainCount = 0;

    // Effects state
    let particles = [];
    let matchedBlocks = [];
    let shakeIntensity = 0;
    let flashOverlay = 0;
    const FLASH_DURATION = 350;
    let audioCtx = null;

    // --- Audio ---
    function getAudio() {
        if (!audioCtx) {
            try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) {}
        }
        return audioCtx;
    }

    function playTone(freqs, dur, type, vol) {
        try {
            const ctx = getAudio();
            if (!ctx) return;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = type || 'sine';
            const t = ctx.currentTime;
            freqs.forEach((f, i) => {
                if (i === 0) osc.frequency.setValueAtTime(f, t);
                else osc.frequency.setValueAtTime(f, t + i * 0.04);
            });
            gain.gain.setValueAtTime(vol || 0.25, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + dur);
            osc.start(t);
            osc.stop(t + dur);
        } catch(e) {}
    }

    function playMatchSound() {
        playTone([523, 659, 784], 0.25, 'sine', 0.25);
    }

    function playChainSound(chain) {
        const base = 523 + Math.min(chain, 8) * 80;
        playTone([base, base + 150, base + 300, base + 450], 0.3, 'sine', 0.2);
    }

    function playDropSound() {
        playTone([200, 120, 80], 0.2, 'triangle', 0.25);
    }

    function playMoveSound() {
        playTone([350], 0.05, 'sine', 0.1);
    }

    function playGameOverSound() {
        try {
            const ctx = getAudio();
            if (!ctx) return;
            const t = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(400, t);
            osc.frequency.exponentialRampToValueAtTime(80, t + 0.6);
            gain.gain.setValueAtTime(0.15, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + 0.6);
            osc.start(t);
            osc.stop(t + 0.6);
            // Second tone lower
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.type = 'triangle';
            osc2.frequency.setValueAtTime(300, t + 0.15);
            osc2.frequency.exponentialRampToValueAtTime(60, t + 0.7);
            gain2.gain.setValueAtTime(0.12, t + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.001, t + 0.7);
            osc2.start(t + 0.15);
            osc2.stop(t + 0.7);
        } catch(e) {}
    }

    // --- Particles ---
    function spawnParticles(col, row, hex) {
        const cx = col * BLOCK_SIZE + BLOCK_SIZE / 2;
        const cy = row * BLOCK_SIZE + BLOCK_SIZE / 2;
        for (let i = 0; i < 10; i++) {
            const angle = (Math.PI * 2 / 10) * i + (Math.random() - 0.5) * 0.6;
            const speed = 1.2 + Math.random() * 2.5;
            particles.push({
                x: cx, y: cy,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                life: 1,
                decay: 0.012 + Math.random() * 0.018,
                color: hex,
                size: 2 + Math.random() * 3.5
            });
        }
    }

    function updateParticles() {
        for (const p of particles) {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.06;
            p.life -= p.decay;
        }
        particles = particles.filter(p => p.life > 0);
    }

    function drawParticles() {
        for (const p of particles) {
            ctx.globalAlpha = Math.max(0, p.life);
            ctx.fillStyle = p.color;
            ctx.shadowColor = p.color + '60';
            ctx.shadowBlur = 6;
            ctx.beginPath();
            ctx.arc(p.x, p.y, Math.max(0.5, p.size * p.life), 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.globalAlpha = 1;
        ctx.shadowBlur = 0;
    }

    // --- Canvas ---
    function resizeCanvas() {
        const container = canvas.parentElement;
        const maxW = Math.min(380, container.clientWidth - 8);
        BLOCK_SIZE = Math.floor(maxW / COLS);
        canvasW = BLOCK_SIZE * COLS;
        canvasH = BLOCK_SIZE * ROWS;
        canvas.width = canvasW;
        canvas.height = canvasH;
        if (gameActive) draw();
    }

    function initGrid() {
        grid = Array.from({length: ROWS}, () => Array(COLS).fill(-1));
    }

    function rng() {
        return Math.floor(Math.random() * COLORS.length);
    }

    function spawnPiece() {
        currentPiece = { col: Math.floor(COLS / 2), row: 0, color: rng() };
        if (grid[0][currentPiece.col] !== -1) {
            gameOver = true;
            currentPiece = null;
            endGame();
        }
    }

    function getNeighbors(r, c) {
        const out = [];
        if (r > 0) out.push([r-1, c]);
        if (r < ROWS-1) out.push([r+1, c]);
        if (c > 0) out.push([r, c-1]);
        if (c < COLS-1) out.push([r, c+1]);
        return out;
    }

    function findMatches() {
        const visited = Array.from({length: ROWS}, () => Array(COLS).fill(false));
        const matches = [];
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                if (grid[r][c] === -1 || visited[r][c]) continue;
                const color = grid[r][c];
                const group = [];
                const stack = [[r, c]];
                while (stack.length) {
                    const [cr, cc] = stack.pop();
                    if (cr < 0 || cr >= ROWS || cc < 0 || cc >= COLS) continue;
                    if (visited[cr][cc] || grid[cr][cc] !== color) continue;
                    visited[cr][cc] = true;
                    group.push([cr, cc]);
                    for (const [nr, nc] of getNeighbors(cr, cc)) {
                        if (!visited[nr][nc] && grid[nr][nc] === color) stack.push([nr, nc]);
                    }
                }
                if (group.length >= 3) matches.push(group);
            }
        }
        return matches;
    }

    function applyGravity() {
        for (let c = 0; c < COLS; c++) {
            let writeRow = ROWS - 1;
            for (let r = ROWS - 1; r >= 0; r--) {
                if (grid[r][c] !== -1) {
                    grid[writeRow][c] = grid[r][c];
                    if (writeRow !== r) grid[r][c] = -1;
                    writeRow--;
                }
            }
            for (let r = writeRow; r >= 0; r--) grid[r][c] = -1;
        }
    }

    function lockPiece() {
        if (!currentPiece) return;
        const { row, col, color } = currentPiece;
        if (row >= 0 && row < ROWS && col >= 0 && col < COLS && grid[row][col] === -1) {
            grid[row][col] = color;
        }
        currentPiece = null;
        playDropSound();
        isProcessing = true;
        processMatch();
    }

    function processMatch() {
        const matches = findMatches();
        if (matches.length === 0) {
            isProcessing = false;
            chainCount = 0;
            if (!gameOver) spawnPiece();
            return;
        }

        const all = matches.flat();
        const pts = all.length * 10 * (chainCount + 1);
        score += pts;
        chainCount++;
        updateScore();

        // Effects
        playMatchSound();
        if (chainCount > 1) playChainSound(chainCount);
        shakeIntensity = Math.min(chainCount * 5, 14);
        flashOverlay = 1;

        // Record for flash animation + spawn particles
        for (const [r, c] of all) {
            const ci = grid[r][c];
            if (ci >= 0) {
                spawnParticles(c, r, COLORS[ci].hex);
                matchedBlocks.push({ r, c, color: ci, timer: FLASH_DURATION });
                grid[r][c] = -1;
            }
        }

        setTimeout(function() {
            matchedBlocks = [];
            applyGravity();
            setTimeout(processMatch, 100);
        }, FLASH_DURATION);
    }

    function dropPiece() {
        if (!currentPiece || gameOver || isProcessing) return false;
        const nr = currentPiece.row + 1;
        if (nr < ROWS && grid[nr][currentPiece.col] === -1) {
            currentPiece.row = nr;
            return true;
        }
        lockPiece();
        return false;
    }

    function hardDrop() {
        if (!currentPiece || gameOver || isProcessing) return;
        while (currentPiece.row + 1 < ROWS && grid[currentPiece.row + 1][currentPiece.col] === -1) {
            currentPiece.row++;
        }
        lockPiece();
    }

    function handleColumnClick(col) {
        if (gameOver || !gameActive || isProcessing) return;
        col = Math.max(0, Math.min(COLS - 1, col));
        currentPiece.col = col;
        hardDrop();
    }

    function updateScore() {
        scoreDisplay.textContent = score.toLocaleString();
    }

    function draw() {
        ctx.save();

        // Screen shake
        if (shakeIntensity > 0.5) {
            const sx = (Math.random() - 0.5) * shakeIntensity;
            const sy = (Math.random() - 0.5) * shakeIntensity;
            ctx.translate(sx, sy);
            shakeIntensity *= 0.88;
        } else {
            shakeIntensity = 0;
        }

        ctx.clearRect(-10, -10, canvasW + 20, canvasH + 20);

        // Background
        ctx.fillStyle = '#0f172a';
        ctx.fillRect(-10, -10, canvasW + 20, canvasH + 20);

        // Grid cells
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                const x = c * BLOCK_SIZE;
                const y = r * BLOCK_SIZE;
                const ci = grid[r][c];
                if (ci >= 0) {
                    ctx.fillStyle = COLORS[ci].hex;
                    ctx.shadowColor = COLORS[ci].hex + '50';
                    ctx.shadowBlur = 10;
                    const pad = 1.5;
                    const rad = 4;
                    const bw = BLOCK_SIZE - pad * 2;
                    ctx.beginPath();
                    ctx.roundRect(x + pad, y + pad, bw, bw, rad);
                    ctx.fill();
                    ctx.shadowBlur = 0;
                    ctx.fillStyle = 'rgba(255,255,255,0.18)';
                    ctx.beginPath();
                    ctx.roundRect(x + pad + 2, y + pad + 2, bw - 4, 5, 2);
                    ctx.fill();
                } else {
                    ctx.fillStyle = 'rgba(255,255,255,0.03)';
                    const pad = 1;
                    ctx.beginPath();
                    ctx.roundRect(x + pad, y + pad, BLOCK_SIZE - pad * 2, BLOCK_SIZE - pad * 2, 3);
                    ctx.fill();
                }
                ctx.strokeStyle = 'rgba(255,255,255,0.05)';
                ctx.lineWidth = 0.5;
                ctx.strokeRect(x, y, BLOCK_SIZE, BLOCK_SIZE);
            }
        }

        // Matched blocks (flash animation)
        for (const mb of matchedBlocks) {
            const progress = 1 - (mb.timer / FLASH_DURATION);
            mb.timer -= 16;
            const x = mb.col * BLOCK_SIZE;
            const y = mb.row * BLOCK_SIZE;
            const pulse = Math.sin(progress * Math.PI * 6) * 0.3 + 0.7;
            const scale = 1 - progress * 0.15;

            ctx.save();
            ctx.translate(x + BLOCK_SIZE / 2, y + BLOCK_SIZE / 2);
            ctx.scale(scale, scale);

            ctx.fillStyle = COLORS[mb.color].hex;
            ctx.shadowColor = COLORS[mb.color].hex + '80';
            ctx.shadowBlur = 20 * pulse;
            const s = BLOCK_SIZE * 0.9;
            ctx.globalAlpha = pulse;
            ctx.beginPath();
            ctx.roundRect(-s / 2, -s / 2, s, s, 5);
            ctx.fill();
            ctx.globalAlpha = 1;
            ctx.shadowBlur = 0;

            // Inner bright pulse
            ctx.fillStyle = 'rgba(255,255,255,' + (0.3 * pulse) + ')';
            ctx.beginPath();
            ctx.roundRect(-s / 2 + 3, -s / 2 + 3, s - 6, s * 0.15, 2);
            ctx.fill();
            ctx.restore();
        }

        // Current piece
        if (currentPiece && !gameOver) {
            const x = currentPiece.col * BLOCK_SIZE;
            const y = currentPiece.row * BLOCK_SIZE;
            ctx.fillStyle = COLORS[currentPiece.color].hex;
            ctx.shadowColor = COLORS[currentPiece.color].hex + '70';
            ctx.shadowBlur = 18;
            const pad = 1.5;
            ctx.beginPath();
            ctx.roundRect(x + pad, y + pad, BLOCK_SIZE - pad * 2, BLOCK_SIZE - pad * 2, 4);
            ctx.fill();
            ctx.shadowBlur = 0;
            ctx.fillStyle = 'rgba(255,255,255,0.2)';
            ctx.beginPath();
            ctx.roundRect(x + pad + 2, y + pad + 2, BLOCK_SIZE - pad * 2 - 4, 5, 2);
            ctx.fill();
        }

        // Particles
        drawParticles();
        updateParticles();

        // Flash overlay
        if (flashOverlay > 0.01) {
            ctx.fillStyle = 'rgba(255,255,255,' + (flashOverlay * 0.15) + ')';
            ctx.fillRect(-10, -10, canvasW + 20, canvasH + 20);
            flashOverlay *= 0.85;
        } else {
            flashOverlay = 0;
        }

        // Column indicators
        if (gameActive && !gameOver && !isProcessing) {
            for (let c = 0; c < COLS; c++) {
                const cx = c * BLOCK_SIZE + BLOCK_SIZE / 2;
                const cy = ROWS * BLOCK_SIZE - 12;
                ctx.fillStyle = 'rgba(255,255,255,0.15)';
                ctx.beginPath();
                ctx.arc(cx, cy, 4, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        ctx.restore();
    }

    function endGame() {
        gameActive = false;
        gameOver = true;
        cancelAnimationFrame(animFrameId);
        playGameOverSound();
        draw();
        finalScore.textContent = score.toLocaleString();
        gpTokensEarned.textContent = score.toLocaleString();
        gameOverOverlay.classList.remove('hidden');
        submitScore(score);
    }

    function submitScore(pts) {
        if (pts <= 0) return;
        fetch('/api/game_submit_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ game: 'color-swipe', score: pts })
        })
        .then(r => r.json())
        .then(data => {
            if (data.newBalance !== undefined) {
                newBalance.textContent = data.newBalance.toLocaleString();
            }
            if (data.error) {
                newBalance.textContent = '?';
            }
        })
        .catch(() => { newBalance.textContent = '?'; });
    }

    function startGame() {
        gameActive = true;
        gameOver = false;
        score = 0;
        chainCount = 0;
        isProcessing = false;
        particles = [];
        matchedBlocks = [];
        shakeIntensity = 0;
        flashOverlay = 0;
        initGrid();
        updateScore();
        startScreen.classList.add('hidden');
        gameOverOverlay.classList.add('hidden');
        spawnPiece();
        accumTime = 0;
        lastTime = performance.now();
        if (animFrameId) cancelAnimationFrame(animFrameId);
        gameLoop(performance.now());
    }

    function gameLoop(timestamp) {
        if (!gameActive) return;
        const dt = timestamp - lastTime;
        lastTime = timestamp;
        if (!gameOver && currentPiece && !isProcessing) {
            accumTime += dt;
            if (accumTime >= DROP_INTERVAL) {
                accumTime = 0;
                dropPiece();
            }
        }
        draw();
        animFrameId = requestAnimationFrame(gameLoop);
    }

    // Input
    canvas.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const cx = (e.clientX - rect.left) * scaleX;
        const col = Math.floor(cx / BLOCK_SIZE);
        if (!gameActive && !gameOver) return;
        handleColumnClick(col);
    });

    let touchMoved = false;
    canvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        touchMoved = false;
    }, { passive: false });

    canvas.addEventListener('touchmove', function() {
        touchMoved = true;
    }, { passive: true });

    canvas.addEventListener('touchend', function(e) {
        if (touchMoved) return;
        if (!gameActive && !gameOver) return;
        const rect = this.getBoundingClientRect();
        const sc = canvas.width / rect.width;
        const cx = (e.changedTouches[0].clientX - rect.left) * sc;
        const col = Math.floor(cx / BLOCK_SIZE);
        handleColumnClick(col);
    }, { passive: true });

    document.addEventListener('keydown', function(e) {
        if (!gameActive) return;
        if (e.key === 'ArrowLeft' && currentPiece && !isProcessing) {
            if (currentPiece.col > 0) playMoveSound();
            currentPiece.col = Math.max(0, currentPiece.col - 1);
        } else if (e.key === 'ArrowRight' && currentPiece && !isProcessing) {
            if (currentPiece.col < COLS - 1) playMoveSound();
            currentPiece.col = Math.min(COLS - 1, currentPiece.col + 1);
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            hardDrop();
        } else if (e.key === ' ' || e.key === 'Space') {
            e.preventDefault();
            hardDrop();
        }
    });

    // Init
    window.startGame = startGame;
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    initGrid();
    draw();
})();
</script>
<?php
$content = ob_get_clean();
?>