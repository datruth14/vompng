<?php
$pageTitle = 'Space Shooter - Gamepad';
ob_start();
?>
<section class="py-6 max-w-lg mx-auto">
    <div class="text-center mb-4">
        <h1 class="text-3xl font-black text-white">Space Shooter</h1>
        <p class="text-gray-400 text-sm">Drag to move &bull; Hold to fire</p>
    </div>

    <div class="flex justify-between items-center mb-4">
        <div class="glass-morphism rounded-2xl px-5 py-2 border border-white/10">
            <p class="text-xs text-gray-500 font-black uppercase tracking-wider">Score</p>
            <p id="ssScore" class="text-2xl font-black text-white">0</p>
        </div>
        <div class="glass-morphism rounded-2xl px-5 py-2 border border-white/10">
            <p class="text-xs text-gray-500 font-black uppercase tracking-wider">HP</p>
            <p id="ssHp" class="text-2xl font-black text-rose-400">5</p>
        </div>
        <div class="glass-morphism rounded-2xl px-5 py-2 border border-white/10">
            <p class="text-xs text-gray-500 font-black uppercase tracking-wider">GPTokens</p>
            <p class="text-2xl font-black text-emerald-400"><?php echo number_format((int)($currentUser['gptokens'] ?? 0)); ?></p>
        </div>
    </div>

    <div class="glass-morphism rounded-[2rem] p-3 border border-white/10">
        <div class="relative">
            <canvas id="ssCanvas" class="w-full block rounded-xl bg-gray-900 cursor-pointer"></canvas>

            <!-- Start Screen -->
            <div id="ssStart" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-950/80 rounded-xl z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                </div>
                <h2 class="text-2xl font-black text-white mb-1">Space Shooter</h2>
                <p class="text-gray-400 text-sm mb-6">Blast enemies, earn GPTokens!</p>
                <button onclick="ssStartGame()" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-black text-sm hover:scale-105 transition-transform">Launch</button>
                <p class="text-xs text-gray-500 mt-4">Drag to move &bull; Hold to fire</p>
            </div>

            <!-- Game Over -->
            <div id="ssOver" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-950/85 rounded-xl z-10">
                <svg class="w-12 h-12 text-yellow-400 mb-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                <h2 class="text-2xl font-black text-white mb-1">Game Over</h2>
                <p class="text-gray-400 text-sm">Score: <span id="ssFinal" class="text-white font-black">0</span></p>
                <div class="flex items-center gap-2 mt-2 mb-5 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-emerald-400 text-sm font-bold">+<span id="ssGp">0</span> GPT</p>
                </div>
                <button onclick="ssStartGame()" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-black text-sm hover:scale-105 transition-transform">Play Again</button>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="/game" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">&larr; Back to Gamepad</a>
    </div>
</section>

<script>
(function() {
    var cv, ctx, W, H, BS;
    var player, bullets, enemies, particles, stars;
    var score, hp, gameActive, gameOver;
    var lastFire, fireRate = 180;
    var enemyTimer, enemyDelay = 900;
    var pointerX, pointerY, pointerDown;
    var animId;
    var audioCtx = null;

    // --- Audio ---
    function initAudio() { try { if(!audioCtx) audioCtx=new(window.AudioContext||window.webkitAudioContext)(); if(audioCtx.state==='suspended') audioCtx.resume(); }catch(e){} }
    function snd(freq, dur, type, vol) { try { if(!audioCtx||audioCtx.state==='suspended') return; var o=audioCtx.createOscillator(),g=audioCtx.createGain(); o.connect(g); g.connect(audioCtx.destination); o.type=type||'sine'; o.frequency.setValueAtTime(freq,audioCtx.currentTime); g.gain.setValueAtTime(vol||0.1,audioCtx.currentTime); g.gain.exponentialRampToValueAtTime(0.001,audioCtx.currentTime+dur); o.start(); o.stop(audioCtx.currentTime+dur); }catch(e){} }

    // --- Resize ---
    function resize() {
        var c = cv.parentElement;
        var mw = Math.min(380, c.clientWidth - 8);
        BS = Math.floor(mw / 9); // 9 columns = 40px per unit roughly
        W = BS * 9;
        H = BS * 16;
        cv.width = W;
        cv.height = H;
        if (gameActive) draw();
    }

    // --- Init ---
    function reset() {
        player = { x: W/2, y: H - BS*2, w: BS*1.2, h: BS*1.6, hp: 5, invincible: 0 };
        bullets = [];
        enemies = [];
        particles = [];
        stars = [];
        for (var i = 0; i < 40; i++) {
            stars.push({ x: Math.random() * W, y: Math.random() * H, s: 0.5 + Math.random() * 1.5 });
        }
        score = 0;
        hp = 5;
        enemyTimer = 0;
        enemyDelay = 900;
        lastFire = 0;
        pointerX = player.x;
        pointerY = player.y;
    }

    // --- Spawn ---
    function spawnEnemy() {
        var w = BS * 1.1;
        var e = {
            x: BS + Math.random() * (W - BS*2),
            y: -w,
            w: w,
            h: w,
            hp: 1 + Math.floor(Math.random() * 2),
            speed: 40 + Math.random() * 30
        };
        enemies.push(e);
    }

    function spawnBullet() {
        var b = {
            x: player.x,
            y: player.y - player.h / 2,
            w: BS * 0.2,
            h: BS * 0.6,
            speed: 400
        };
        bullets.push(b);
    }

    function spawnParticles(x, y, color, count, spd) {
        for (var i = 0; i < count; i++) {
            var a = Math.random() * Math.PI * 2;
            var s = (spd || 80) + Math.random() * 120;
            particles.push({
                x: x, y: y,
                vx: Math.cos(a) * s,
                vy: Math.sin(a) * s,
                life: 1,
                decay: 0.015 + Math.random() * 0.02,
                color: color,
                size: 1.5 + Math.random() * 2.5
            });
        }
    }

    // --- Update ---
    function update(dt) {
        if (!gameActive) return;

        var dts = dt / 1000;

        // Player follow pointer
        if (pointerDown) {
            var tx = Math.max(BS/2, Math.min(W - BS/2, pointerX));
            var ty = Math.max(H * 0.4, Math.min(H - BS, pointerY));
            player.x += (tx - player.x) * 0.12;
            player.y += (ty - player.y) * 0.12;
        }

        // Auto-fire
        if (pointerDown) {
            lastFire += dt;
            if (lastFire >= fireRate) {
                lastFire = 0;
                spawnBullet();
                snd(700, 0.04, 'square', 0.04);
            }
        }

        // Invincibility
        if (player.invincible > 0) player.invincible -= dt;

        // Move bullets
        for (var i = bullets.length - 1; i >= 0; i--) {
            bullets[i].y -= bullets[i].speed * dts;
            if (bullets[i].y + bullets[i].h < 0) bullets.splice(i, 1);
        }

        // Move enemies
        enemyTimer += dt;
        if (enemyTimer >= enemyDelay) {
            enemyTimer = 0;
            spawnEnemy();
        }
        for (var i = enemies.length - 1; i >= 0; i--) {
            var e = enemies[i];
            e.y += e.speed * dts;
            if (e.y > H + 20) {
                enemies.splice(i, 1);
                hp--;
                updateHUD();
                if (hp <= 0) { gameActive = false; gameOver = true; showGameOver(); return; }
            }
        }

        // Collisions: bullet vs enemy
        for (var i = bullets.length - 1; i >= 0; i--) {
            var b = bullets[i];
            var hit = false;
            for (var j = enemies.length - 1; j >= 0; j--) {
                var e = enemies[j];
                if (b.x < e.x + e.w && b.x + b.w > e.x && b.y < e.y + e.h && b.y + b.h > e.y) {
                    e.hp--;
                    hit = true;
                    spawnParticles(b.x, b.y, '#ffff44', 4);
                    snd(900, 0.05, 'sine', 0.05);
                    if (e.hp <= 0) {
                        spawnParticles(e.x + e.w/2, e.y + e.h/2, '#ff4444', 12);
                        score += 10;
                        enemies.splice(j, 1);
                    }
                    break;
                }
            }
            if (hit) bullets.splice(i, 1);
        }

        // Collisions: player vs enemy
        if (player.invincible <= 0) {
            for (var j = enemies.length - 1; j >= 0; j--) {
                var e = enemies[j];
                if (player.x < e.x + e.w && player.x + player.w > e.x &&
                    player.y < e.y + e.h && player.y + player.h > e.y) {
                    spawnParticles(e.x + e.w/2, e.y + e.h/2, '#ff4444', 10);
                    enemies.splice(j, 1);
                    hp--;
                    player.invincible = 800;
                    player.hp = hp;
                    snd(200, 0.15, 'sawtooth', 0.06);
                    updateHUD();
                    if (hp <= 0) { gameActive = false; gameOver = true; showGameOver(); return; }
                }
            }
        }

        // Update particles
        for (var i = particles.length - 1; i >= 0; i--) {
            var p = particles[i];
            p.x += p.vx * dts;
            p.y += p.vy * dts;
            p.life -= p.decay;
            if (p.life <= 0) particles.splice(i, 1);
        }

        updateHUD();
    }

    // --- Draw ---
    function draw() {
        ctx.clearRect(0, 0, W, H);

        // Stars
        ctx.fillStyle = '#fff';
        for (var i = 0; i < stars.length; i++) {
            var s = stars[i];
            ctx.globalAlpha = 0.3 + Math.sin(Date.now() * 0.001 + i) * 0.2;
            ctx.fillRect(s.x, s.y, s.s, s.s);
        }
        ctx.globalAlpha = 1;

        // Bullets
        for (var i = 0; i < bullets.length; i++) {
            var b = bullets[i];
            ctx.fillStyle = '#ffdd44';
            ctx.shadowColor = '#ffdd44';
            ctx.shadowBlur = 8;
            ctx.fillRect(b.x - b.w/2, b.y - b.h/2, b.w, b.h);
        }
        ctx.shadowBlur = 0;

        // Enemies
        for (var i = 0; i < enemies.length; i++) {
            var e = enemies[i];
            var x = e.x, y = e.y, w = e.w, h = e.h;
            // Body
            ctx.fillStyle = '#cc2233';
            ctx.shadowColor = '#ff4444';
            ctx.shadowBlur = 6;
            ctx.beginPath();
            ctx.roundRect(x, y, w, h, 3);
            ctx.fill();
            // Top highlight
            ctx.fillStyle = '#ff4455';
            ctx.shadowBlur = 0;
            ctx.beginPath();
            ctx.roundRect(x + 3, y + 3, w - 6, h * 0.25, 2);
            ctx.fill();
            // HP text
            ctx.fillStyle = '#fff';
            ctx.font = 'bold ' + Math.floor(w * 0.45) + 'px monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = '#000';
            ctx.shadowBlur = 4;
            ctx.fillText(e.hp, x + w/2, y + h/2);
            ctx.shadowBlur = 0;
        }

        // Player
        if (player.invincible > 0 && Math.floor(Date.now() / 80) % 2 === 0) {
            // Flash during invincibility
        } else {
            var px = player.x, py = player.y, pw = player.w, ph = player.h;
            ctx.fillStyle = '#00ff88';
            ctx.shadowColor = '#00ff88';
            ctx.shadowBlur = 12;
            ctx.beginPath();
            ctx.moveTo(px, py - ph/2);
            ctx.lineTo(px + pw/2, py + ph/2);
            ctx.lineTo(px - pw/2, py + ph/2);
            ctx.closePath();
            ctx.fill();
            ctx.shadowBlur = 0;
            // Cockpit
            ctx.fillStyle = '#66ffbb';
            ctx.beginPath();
            ctx.moveTo(px, py - ph/3);
            ctx.lineTo(px + pw/4, py + ph/6);
            ctx.lineTo(px - pw/4, py + ph/6);
            ctx.closePath();
            ctx.fill();
        }

        // Particles
        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];
            ctx.globalAlpha = Math.max(0, p.life);
            ctx.fillStyle = p.color;
            ctx.shadowColor = p.color;
            ctx.shadowBlur = 4;
            ctx.beginPath();
            ctx.arc(p.x, p.y, Math.max(0.5, p.size * p.life), 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.globalAlpha = 1;
        ctx.shadowBlur = 0;
    }

    // --- HUD ---
    function updateHUD() {
        document.getElementById('ssScore').textContent = score;
        document.getElementById('ssHp').textContent = hp;
    }

    // --- Game Loop ---
    var lastTime = 0;
    function loop(time) {
        var dt = Math.min(50, time - lastTime);
        lastTime = time;
        update(dt);
        draw();
        animId = requestAnimationFrame(loop);
    }

    // --- Game Over ---
    function showGameOver() {
        document.getElementById('ssFinal').textContent = score;
        document.getElementById('ssGp').textContent = score;
        document.getElementById('ssOver').classList.remove('hidden');
        submitScore(score);
    }

    function submitScore(pts) {
        if (pts <= 0) return;
        fetch('/api/game_submit_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ game: 'space-shooter', score: pts })
        }).then(function(r){ return r.json(); }).catch(function(){});
    }

    // --- Start ---
    window.ssStartGame = function() {
        initAudio();
        document.getElementById('ssStart').classList.add('hidden');
        document.getElementById('ssOver').classList.add('hidden');
        reset();
        gameActive = true;
        gameOver = false;
        lastTime = performance.now();
        if (animId) cancelAnimationFrame(animId);
        loop(performance.now());
    };

    // --- Input ---
    cv = document.getElementById('ssCanvas');
    ctx = cv.getContext('2d');

    function getPos(e) {
        var r = cv.getBoundingClientRect();
        var scX = cv.width / r.width;
        var scY = cv.height / r.height;
        var cx, cy;
        if (e.touches) {
            cx = (e.touches[0].clientX - r.left) * scX;
            cy = (e.touches[0].clientY - r.top) * scY;
        } else {
            cx = (e.clientX - r.left) * scX;
            cy = (e.clientY - r.top) * scY;
        }
        return { x: cx, y: cy };
    }

    cv.addEventListener('mousedown', function(e) {
        var p = getPos(e);
        pointerX = p.x; pointerY = p.y;
        pointerDown = true;
    });
    window.addEventListener('mousemove', function(e) {
        if (!pointerDown) return;
        var p = getPos(e);
        pointerX = p.x; pointerY = p.y;
    });
    window.addEventListener('mouseup', function() { pointerDown = false; });

    cv.addEventListener('touchstart', function(e) {
        e.preventDefault();
        var p = getPos(e);
        pointerX = p.x; pointerY = p.y;
        pointerDown = true;
    }, { passive: false });
    cv.addEventListener('touchmove', function(e) {
        e.preventDefault();
        var p = getPos(e);
        pointerX = p.x; pointerY = p.y;
    }, { passive: false });
    cv.addEventListener('touchend', function(e) {
        e.preventDefault();
        pointerDown = false;
    }, { passive: false });

    // --- Init ---
    window.addEventListener('resize', resize);
    resize();
    reset();
    draw();
})();
</script>
<?php
$content = ob_get_clean();
?>