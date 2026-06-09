<?php
$pageTitle = 'Space Shooter - Gamepad';
ob_start();
?>
<section class="py-6 max-w-lg mx-auto">
    <div class="text-center mb-4">
        <h1 class="text-3xl font-black text-white">Space Shooter</h1>
        <p class="text-gray-400 text-sm">Drag to move &bull; Tap to shoot</p>
    </div>

    <div class="flex justify-between items-center mb-4">
        <div class="glass-morphism rounded-2xl px-5 py-2 border border-white/10">
            <p class="text-xs text-gray-500 font-black uppercase tracking-wider">GPTokens</p>
            <p class="text-2xl font-black text-emerald-400"><?php echo number_format((int)($currentUser['gptokens'] ?? 0)); ?></p>
        </div>
    </div>

    <div class="glass-morphism rounded-[2rem] p-3 border border-white/10">
        <div id="spaceShooterContainer" class="relative w-full aspect-[9/16] max-h-[560px] mx-auto overflow-hidden rounded-xl bg-gray-900"></div>
    </div>

    <div class="text-center mt-4">
        <a href="/game" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">&larr; Back to Gamepad</a>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/phaser@3.60.0/dist/phaser.min.js"></script>
<script>
(function() {
    var game, audioCtx = null;

    function initAudio() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
        } catch(e) {}
    }

    function playTone(freq, dur, type, vol) {
        try {
            if (!audioCtx || audioCtx.state === 'suspended') return;
            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = type || 'sine';
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
            gain.gain.setValueAtTime(vol || 0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + dur);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + dur);
        } catch(e) {}
    }

    function submitScore(pts) {
        if (pts <= 0) return;
        fetch('/api/game_submit_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ game: 'space-shooter', score: pts })
        }).then(function(r){ return r.json(); }).catch(function(){});
    }

    function createTextures(scene) {
        var g;
        // Player ship
        g = scene.make.graphics({add:false});
        g.fillStyle(0x00ff88);
        g.fillTriangle(18, 0, 36, 32, 0, 32);
        g.fillStyle(0x66ffbb);
        g.fillRect(7, 20, 22, 12);
        g.generateTexture('player', 36, 32);
        g.destroy();

        // Enemy
        g = scene.make.graphics({add:false});
        g.fillStyle(0xff3344);
        g.fillRect(0, 0, 28, 28);
        g.fillStyle(0xff6677);
        g.fillRect(4, 4, 20, 8);
        g.generateTexture('enemy', 28, 28);
        g.destroy();

        // Bullet
        g = scene.make.graphics({add:false});
        g.fillStyle(0xffff44);
        g.fillRect(0, 0, 4, 14);
        g.fillStyle(0xffffff);
        g.fillRect(1, 0, 2, 6);
        g.generateTexture('bullet', 4, 14);
        g.destroy();

        // Powerup
        g = scene.make.graphics({add:false});
        g.fillStyle(0x4488ff);
        g.fillCircle(10, 10, 10);
        g.fillStyle(0x88bbff);
        g.fillCircle(10, 10, 6);
        g.generateTexture('powerup', 20, 20);
        g.destroy();

        // Particle
        g = scene.make.graphics({add:false});
        g.fillStyle(0xffffff);
        g.fillCircle(3, 3, 3);
        g.generateTexture('particle', 6, 6);
        g.destroy();
    }

    class GameScene extends Phaser.Scene {
        constructor() {
            super({ key: 'GameScene' });
        }

        create() {
            createTextures(this);
            var self = this;

            self.gameActive = true;
            self.score = 0;
            self.level = 1;
            self.xp = 0;
            self.xpToNext = 50;
            self.firepower = 1;
            self.health = 5;
            self.lastFired = 0;
            self.fireRate = 250;
            self.enemySpawnDelay = 1200;
            self.enemySpawnTimer = 0;
            self.powerupSpawnTimer = 0;
            self.powerupSpawnDelay = 6000;

            self.player = self.physics.add.sprite(180, 560, 'player');
            self.player.setCollideWorldBounds(true);
            self.player.setDepth(10);

            self.bullets = self.physics.add.group({ defaultKey: 'bullet' });
            self.enemies = self.physics.add.group();
            self.powerups = self.physics.add.group();

            // HUD
            var hudY = 16;
            self.scoreLabel = self.add.text(12, hudY, 'SCORE', { fontSize:'10px', fontFamily:'monospace', color:'#666', fontStyle:'bold' });
            self.scoreText = self.add.text(12, hudY + 12, '0', { fontSize:'20px', fontFamily:'monospace', color:'#fff', fontStyle:'bold' });
            self.hpLabel = self.add.text(348, hudY, 'HP', { fontSize:'10px', fontFamily:'monospace', color:'#666', fontStyle:'bold' }).setOrigin(1, 0);
            self.hpText = self.add.text(348, hudY + 12, '5', { fontSize:'20px', fontFamily:'monospace', color:'#ff6666', fontStyle:'bold' }).setOrigin(1, 0);
            self.fpText = self.add.text(180, hudY, 'Lv.1', { fontSize:'11px', fontFamily:'monospace', color:'#ffcc00', fontStyle:'bold' }).setOrigin(0.5, 0);

            // XP bar
            var barY = 52;
            self.xpBarBg = self.add.graphics();
            self.xpBarBg.fillStyle(0x222222, 0.6);
            self.xpBarBg.fillRoundedRect(12, barY, 336, 8, 4);
            self.xpBar = self.add.graphics();
            self.xpBar.fillStyle(0x00ff88, 1);
            self.xpBar.fillRoundedRect(12, barY, 0, 8, 4);

            // Collisions
            self.physics.add.overlap(self.bullets, self.enemies, self.onBulletHitEnemy, null, self);
            self.physics.add.overlap(self.player, self.powerups, self.onCollectPowerup, null, self);
            self.physics.add.overlap(self.player, self.enemies, self.onEnemyHitPlayer, null, self);

            // Game over overlay
            self.gameOverGroup = self.add.container(180, 320).setDepth(100).setVisible(false);
            var bg = self.add.graphics();
            bg.fillStyle(0x000000, 0.75);
            bg.fillRoundedRect(-140, -100, 280, 200, 20);
            self.gameOverGroup.add(bg);
            self.goTitle = self.add.text(0, -70, 'GAME OVER', { fontSize:'28px', fontFamily:'monospace', color:'#ff4444', fontStyle:'bold' }).setOrigin(0.5);
            self.gameOverGroup.add(self.goTitle);
            self.goScore = self.add.text(0, -30, 'Score: 0', { fontSize:'18px', fontFamily:'monospace', color:'#fff' }).setOrigin(0.5);
            self.gameOverGroup.add(self.goScore);
            self.goGp = self.add.text(0, 10, '+0 GPT', { fontSize:'14px', fontFamily:'monospace', color:'#00ff88' }).setOrigin(0.5);
            self.gameOverGroup.add(self.goGp);
            var btnBg = self.add.graphics();
            btnBg.fillStyle(0xff610a, 1);
            btnBg.fillRoundedRect(-60, 45, 120, 40, 10);
            self.gameOverGroup.add(btnBg);
            self.goBtn = self.add.text(0, 65, 'PLAY AGAIN', { fontSize:'14px', fontFamily:'monospace', color:'#fff', fontStyle:'bold' }).setOrigin(0.5);
            self.gameOverGroup.add(self.goBtn);
            self.goBtn.setInteractive(new Phaser.Geom.Rectangle(-60, -20, 120, 40), Phaser.Geom.Rectangle.Contains);
            self.goBtn.on('pointerdown', function() { self.scene.restart(); });
        }

        update(time, delta) {
            var self = this;
            if (!self.gameActive) return;

            var pointer = self.input.activePointer;

            // Player follow pointer
            if (pointer.isDown) {
                var tx = Phaser.Math.Clamp(pointer.worldX, 18, 342);
                var ty = Phaser.Math.Clamp(pointer.worldY, 280, 620);
                self.player.x += (tx - self.player.x) * 0.15;
                self.player.y += (ty - self.player.y) * 0.15;
            }

            // Tap to fire
            if (pointer.isDown && time - self.lastFired >= self.fireRate) {
                self.fire();
                self.lastFired = time;
            }

            // Sync enemy HP text
            var eChildren = self.enemies.getChildren();
            for (var i = 0; i < eChildren.length; i++) {
                var e = eChildren[i];
                if (e.active) {
                    var t = e.getData('hpText');
                    if (t && t.active) {
                        t.setPosition(e.x, e.y - 14);
                    }
                }
            }

            // Spawn enemies
            self.enemySpawnTimer += delta;
            if (self.enemySpawnTimer >= self.enemySpawnDelay) {
                self.enemySpawnTimer = 0;
                self.spawnEnemy();
            }

            // Spawn powerups
            self.powerupSpawnTimer += delta;
            if (self.powerupSpawnTimer >= self.powerupSpawnDelay) {
                self.powerupSpawnTimer = 0;
                self.spawnPowerup();
            }

            // Clean off-screen bullets
            var bChildren = self.bullets.getChildren();
            for (var i = bChildren.length - 1; i >= 0; i--) {
                if (bChildren[i].y < -20) bChildren[i].destroy();
            }

            // Clean off-screen enemies → damage
            for (var i = eChildren.length - 1; i >= 0; i--) {
                var e2 = eChildren[i];
                if (e2.active && e2.y > 660) {
                    var ht = e2.getData('hpText');
                    if (ht && ht.active) ht.destroy();
                    e2.destroy();
                    self.takeDamage(1);
                }
            }

            // Clean off-screen powerups
            var pChildren = self.powerups.getChildren();
            for (var i = pChildren.length - 1; i >= 0; i--) {
                if (pChildren[i].active && pChildren[i].y > 660) pChildren[i].destroy();
            }
        }

        fire() {
            var self = this;
            var px = self.player.x;
            var py = self.player.y - 18;
            playTone(600, 0.06, 'square', 0.04);

            if (self.firepower === 1) {
                var b = self.physics.add.sprite(px, py, 'bullet');
                b.setVelocityY(-550);
                self.bullets.add(b);
            } else if (self.firepower === 2) {
                var b1 = self.physics.add.sprite(px - 7, py, 'bullet');
                b1.setVelocityY(-550);
                self.bullets.add(b1);
                var b2 = self.physics.add.sprite(px + 7, py, 'bullet');
                b2.setVelocityY(-550);
                self.bullets.add(b2);
            } else {
                var angles = [-20, -10, 0, 10, 20];
                for (var i = 0; i < angles.length; i++) {
                    var b = self.physics.add.sprite(px, py, 'bullet');
                    var rad = Phaser.Math.DegToRad(angles[i] - 90);
                    b.setVelocity(Math.cos(rad) * 450, Math.sin(rad) * 450);
                    self.bullets.add(b);
                }
            }
        }

        spawnEnemy() {
            var self = this;
            var x = Phaser.Math.Between(20, 340);
            var hp = Phaser.Math.Between(1, 2 + Math.floor(self.level / 2));
            var e = self.physics.add.sprite(x, -20, 'enemy');
            e.setData('hp', hp);
            e.setVelocityY(60 + self.level * 5);

            var txt = self.add.text(e.x, e.y - 14, String(hp), {
                fontSize:'13px', fontFamily:'monospace', color:'#fff', fontStyle:'bold', stroke:'#000', strokeThickness:3
            }).setOrigin(0.5).setDepth(5);
            e.setData('hpText', txt);
            self.enemies.add(e);
        }

        spawnPowerup() {
            var self = this;
            if (self.firepower >= 3) return;
            var x = Phaser.Math.Between(30, 330);
            var p = self.physics.add.sprite(x, -20, 'powerup');
            p.setVelocityY(80);
            self.powerups.add(p);
        }

        onBulletHitEnemy(bullet, enemy) {
            var self = this;
            if (!bullet.active || !enemy.active) return;
            bullet.destroy();
            playTone(800, 0.08, 'sine', 0.06);

            var hp = enemy.getData('hp') - 1;
            enemy.setData('hp', hp);
            var txt = enemy.getData('hpText');
            if (txt && txt.active) txt.setText(String(hp));

            self.spawnParticles(enemy.x, enemy.y, 0xffff44, 3);

            if (hp <= 0) {
                if (txt && txt.active) txt.destroy();
                self.spawnParticles(enemy.x, enemy.y, 0xff3344, 10);
                enemy.destroy();
                self.score += 10;
                self.xp += 10;
                self.updateHUD();
                self.checkLevelUp();
            }
        }

        onCollectPowerup(player, pu) {
            var self = this;
            if (!pu.active) return;
            pu.destroy();
            self.firepower = Math.min(self.firepower + 1, 3);
            self.fpText.setText('Lv.' + self.firepower);
            self.fpText.setColor(self.firepower >= 3 ? '#ff4444' : '#ffcc00');
            self.spawnParticles(pu.x, pu.y, 0x4488ff, 8);
            playTone(500, 0.15, 'sine', 0.08);
        }

        onEnemyHitPlayer(player, enemy) {
            var self = this;
            if (!enemy.active) return;
            var txt = enemy.getData('hpText');
            if (txt && txt.active) txt.destroy();
            enemy.destroy();
            self.spawnParticles(enemy.x, enemy.y, 0xff3344, 10);
            self.takeDamage(1);
        }

        takeDamage(amount) {
            var self = this;
            self.health -= amount;
            self.hpText.setText(String(self.health));
            self.spawnParticles(self.player.x, self.player.y, 0xff4444, 5);
            self.player.setTint(0xff4444);
            self.time.delayedCall(150, function() {
                if (self.player && self.player.active) self.player.clearTint();
            });
            self.firepower = Math.max(1, self.firepower - 1);
            self.fpText.setText('Lv.' + self.firepower);
            self.fpText.setColor('#ffcc00');
            playTone(200, 0.2, 'sawtooth', 0.06);

            if (self.health <= 0) self.gameOver();
        }

        spawnParticles(x, y, color, count) {
            var self = this;
            for (var i = 0; i < count; i++) {
                var p = self.add.sprite(x, y, 'particle');
                p.setTint(color);
                p.setDepth(20);
                var angle = Math.random() * Math.PI * 2;
                var speed = 60 + Math.random() * 120;
                self.tweens.add({
                    targets: p,
                    x: p.x + Math.cos(angle) * speed * 0.6,
                    y: p.y + Math.sin(angle) * speed * 0.6,
                    alpha: 0,
                    scale: 0.1,
                    duration: 300 + Math.random() * 200,
                    onComplete: function() { p.destroy(); }
                });
            }
        }

        checkLevelUp() {
            var self = this;
            while (self.xp >= self.xpToNext) {
                self.xp -= self.xpToNext;
                self.level++;
                self.xpToNext = Math.floor(50 * Math.pow(1.3, self.level - 1));
                self.enemySpawnDelay = Math.max(500, 1200 - self.level * 50);
                self.updateHUD();
            }
        }

        updateHUD() {
            var self = this;
            self.scoreText.setText(String(self.score));
            var ratio = Math.min(1, self.xp / self.xpToNext);
            self.xpBar.clear();
            self.xpBar.fillStyle(0x00ff88, 1);
            self.xpBar.fillRoundedRect(12, 52, Math.floor(336 * ratio), 8, 4);
        }

        gameOver() {
            var self = this;
            self.gameActive = false;
            self.physics.pause();
            self.goScore.setText('Score: ' + self.score);
            self.goGp.setText('+' + self.score + ' GPT');
            self.gameOverGroup.setVisible(true);
            submitScore(self.score);
        }
    }

    var config = {
        type: Phaser.AUTO,
        parent: 'spaceShooterContainer',
        width: 360,
        height: 640,
        backgroundColor: '#0a0a1a',
        physics: {
            default: 'arcade',
            arcade: { gravity: { y: 0 }, debug: false }
        },
        scale: {
            mode: Phaser.Scale.FIT,
            autoCenter: Phaser.Scale.CENTER_BOTH
        },
        scene: [GameScene],
        input: { activePointers: 1 }
    };

    game = new Phaser.Game(config);
})();
</script>
<?php
$content = ob_get_clean();
?>