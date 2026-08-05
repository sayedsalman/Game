<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Space Shooter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            user-select: none;
        }
        body {
            background: #0a0a1a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Courier New', monospace;
            overflow: hidden;
        }
        canvas {
            display: block;
            width: 100vw;
            height: 100vh;
            background: #0a0a1a;
            cursor: none;
            image-rendering: pixelated;
        }
        #ui {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 30px;
            z-index: 10;
        }
        #top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            pointer-events: none;
        }
        #score-display {
            color: #00ffcc;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 0 20px #00ffcc88, 0 0 60px #00ffcc44;
            letter-spacing: 2px;
        }
        #high-score-display {
            color: #ffcc44;
            font-size: 18px;
            text-shadow: 0 0 15px #ffcc4488, 0 0 40px #ffcc4422;
            letter-spacing: 1px;
        }
        #lives-display {
            color: #ff4477;
            font-size: 24px;
            text-shadow: 0 0 20px #ff447788, 0 0 60px #ff447744;
            letter-spacing: 4px;
        }
        #controls-hint {
            color: #445566;
            font-size: 13px;
            text-align: center;
            letter-spacing: 1px;
            opacity: 0.6;
            pointer-events: none;
            padding-bottom: 10px;
        }
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            pointer-events: none;
            z-index: 20;
            background: rgba(10, 10, 26, 0.5);
            opacity: 0;
            transition: opacity 0.6s ease;
        }
        #overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        #overlay h1 {
            color: #00ffcc;
            font-size: 72px;
            font-weight: bold;
            text-shadow: 0 0 40px #00ffcc88, 0 0 120px #00ffcc44, 0 0 200px #00ffcc22;
            letter-spacing: 8px;
            margin-bottom: 10px;
        }
        #overlay .sub {
            color: #8899bb;
            font-size: 22px;
            letter-spacing: 4px;
            margin-bottom: 30px;
            text-shadow: 0 0 20px #00ffcc44;
        }
        #overlay .final-score {
            color: #ffffff;
            font-size: 32px;
            margin-bottom: 6px;
            text-shadow: 0 0 30px #ffffff44;
        }
        #overlay .final-high {
            color: #ffcc44;
            font-size: 22px;
            text-shadow: 0 0 30px #ffcc4444;
            margin-bottom: 30px;
        }
        #overlay .btn {
            color: #00ffcc;
            font-size: 22px;
            letter-spacing: 3px;
            padding: 12px 40px;
            border: 2px solid #00ffcc88;
            border-radius: 6px;
            background: transparent;
            cursor: pointer;
            pointer-events: auto;
            transition: all 0.3s ease;
            text-shadow: 0 0 20px #00ffcc44;
            box-shadow: 0 0 30px #00ffcc22;
            font-family: 'Courier New', monospace;
        }
        #overlay .btn:hover {
            background: #00ffcc22;
            box-shadow: 0 0 60px #00ffcc44, inset 0 0 60px #00ffcc11;
            transform: scale(1.04);
        }
        #overlay .btn:active {
            transform: scale(0.96);
        }
        #overlay .gameover-title {
            color: #ff4477;
            text-shadow: 0 0 40px #ff447788, 0 0 120px #ff447744;
        }
        #overlay .menu-sub {
            color: #6688aa;
            font-size: 18px;
            letter-spacing: 3px;
            margin-top: 10px;
            animation: pulse 1.8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,
            100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }
        @media (max-width: 600px) {
            #overlay h1 {
                font-size: 40px;
                letter-spacing: 4px;
            }
            #overlay .sub {
                font-size: 16px;
            }
            #score-display {
                font-size: 20px;
            }
            #high-score-display {
                font-size: 14px;
            }
            #lives-display {
                font-size: 18px;
            }
            #controls-hint {
                font-size: 11px;
            }
            #overlay .btn {
                font-size: 18px;
                padding: 10px 28px;
            }
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas"></canvas>

    <div id="ui">
        <div id="top-bar">
            <div>
                <div id="score-display">⭐ 0</div>
                <div id="high-score-display">🏆 0</div>
            </div>
            <div id="lives-display">❤️ ❤️ ❤️</div>
        </div>
        <div id="controls-hint">← ↑ → ↓ &nbsp;&nbsp;·&nbsp;&nbsp; SPACE to fire</div>
    </div>

    <div id="overlay">
        <h1>SPACE SHOOTER</h1>
        <div class="sub">✦ NEON EDGE ✦</div>
        <div class="menu-sub">► PRESS SPACE TO START ◄</div>
        <div class="final-score" style="display:none;"></div>
        <div class="final-high" style="display:none;"></div>
        <button class="btn" id="actionBtn" style="display:none;">▶ PLAY AGAIN</button>
    </div>

    <script>
        (function() {
            const canvas = document.getElementById('gameCanvas');
            const ctx = canvas.getContext('2d');

            let W, H;

            function resize() {
                W = canvas.width = window.innerWidth;
                H = canvas.height = window.innerHeight;
            }
            resize();
            window.addEventListener('resize', resize);

            const uiScore = document.getElementById('score-display');
            const uiHighScore = document.getElementById('high-score-display');
            const uiLives = document.getElementById('lives-display');
            const overlay = document.getElementById('overlay');
            const overlayTitle = overlay.querySelector('h1');
            const overlaySub = overlay.querySelector('.sub');
            const overlayMenuSub = overlay.querySelector('.menu-sub');
            const overlayFinalScore = overlay.querySelector('.final-score');
            const overlayFinalHigh = overlay.querySelector('.final-high');
            const actionBtn = document.getElementById('actionBtn');

            const keys = {};
            let gameRunning = false;
            let gamePaused = false;

            // ─── Stars ────────────────────────────────────────────────
            class Star {
                constructor() {
                    this.reset(true);
                }
                reset(init) {
                    this.x = Math.random() * W;
                    this.y = init ? Math.random() * H : -4;
                    this.size = Math.random() * 2 + 0.5;
                    this.speed = this.size * 0.8 + 0.2;
                    this.brightness = Math.random() * 0.6 + 0.4;
                    this.twinkleSpeed = Math.random() * 0.02 + 0.01;
                    this.twinkleOffset = Math.random() * 100;
                }
                update() {
                    this.y += this.speed;
                    if (this.y > H + 4) this.reset(false);
                }
                draw(time) {
                    const b = this.brightness * (0.7 + 0.3 * Math.sin(time * this.twinkleSpeed + this.twinkleOffset));
                    const a = b * 0.9 + 0.1;
                    ctx.globalAlpha = a;
                    ctx.fillStyle = '#ffffff';
                    ctx.shadowColor = '#aaccff';
                    ctx.shadowBlur = this.size * 3;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.shadowBlur = 0;
                    ctx.globalAlpha = 1;
                }
            }

            // ─── Player ───────────────────────────────────────────────
            class Player {
                constructor() {
                    this.x = W / 2;
                    this.y = H - 100;
                    this.w = 36;
                    this.h = 40;
                    this.speed = 5.5;
                    this.tilt = 0;
                    this.targetTilt = 0;
                    this.shootCooldown = 0;
                    this.shootDelay = 10;
                    this.lives = 3;
                    this.invincible = 0;
                    this.engineFlicker = 0;
                    this.alive = true;
                    this.bullets = [];
                    this.trail = [];
                }
                update() {
                    if (!this.alive) return;
                    if (this.invincible > 0) this.invincible--;
                    this.engineFlicker += 0.15;

                    let dx = 0,
                        dy = 0;
                    if (keys['ArrowLeft'] || keys['KeyA']) dx = -1;
                    if (keys['ArrowRight'] || keys['KeyD']) dx = 1;
                    if (keys['ArrowUp'] || keys['KeyW']) dy = -1;
                    if (keys['ArrowDown'] || keys['KeyS']) dy = 1;
                    if (dx && dy) {
                        dx *= 0.707;
                        dy *= 0.707;
                    }
                    this.x += dx * this.speed;
                    this.y += dy * this.speed;
                    this.x = Math.max(this.w / 2, Math.min(W - this.w / 2, this.x));
                    this.y = Math.max(this.h / 2 + 30, Math.min(H - this.h / 2 - 10, this.y));

                    this.targetTilt = -dx * 0.35;
                    this.tilt += (this.targetTilt - this.tilt) * 0.12;

                    if (this.shootCooldown > 0) this.shootCooldown--;
                    if (keys['Space'] && this.shootCooldown === 0) {
                        this.shoot();
                        this.shootCooldown = this.shootDelay;
                    }

                    this.trail.push({ x: this.x, y: this.y + 20 });
                    if (this.trail.length > 18) this.trail.shift();

                    this.bullets.forEach(b => b.update());
                    this.bullets = this.bullets.filter(b => b.alive);
                }
                shoot() {
                    this.bullets.push(new Bullet(this.x, this.y - this.h / 2));
                }
                draw(time) {
                    if (!this.alive) return;
                    if (this.invincible > 0 && Math.floor(this.invincible / 4) % 2 === 0) {
                        ctx.globalAlpha = 0.4;
                    }

                    const px = this.x,
                        py = this.y;
                    const t = this.tilt;

                    // Engine glow
                    const grad = ctx.createRadialGradient(px, py + 24, 2, px, py + 32, 28);
                    grad.addColorStop(0, 'rgba(0,255,200,0.7)');
                    grad.addColorStop(0.4, 'rgba(0,200,255,0.3)');
                    grad.addColorStop(1, 'rgba(0,100,200,0)');
                    ctx.fillStyle = grad;
                    ctx.beginPath();
                    ctx.arc(px, py + 28, 28, 0, Math.PI * 2);
                    ctx.fill();

                    // Engine flame
                    const flicker = 0.85 + 0.15 * Math.sin(this.engineFlicker * 3);
                    const fl = 16 + 10 * flicker;
                    const grad2 = ctx.createLinearGradient(px, py + 18, px, py + 18 + fl);
                    grad2.addColorStop(0, 'rgba(0,255,220,0.9)');
                    grad2.addColorStop(0.4, 'rgba(0,200,255,0.6)');
                    grad2.addColorStop(1, 'rgba(0,100,255,0)');
                    ctx.fillStyle = grad2;
                    ctx.beginPath();
                    ctx.moveTo(px - 8, py + 18);
                    ctx.quadraticCurveTo(px - 4 + Math.sin(this.engineFlicker * 5) * 2, py + 18 + fl * 0.7, px, py + 18 + fl);
                    ctx.quadraticCurveTo(px + 4 + Math.sin(this.engineFlicker * 5 + 1) * 2, py + 18 + fl * 0.7, px + 8, py + 18);
                    ctx.fill();

                    // Ship body
                    ctx.save();
                    ctx.translate(px, py);
                    ctx.rotate(t);

                    // Glow
                    ctx.shadowColor = '#00ffcc';
                    ctx.shadowBlur = 30;

                    // Main hull
                    const grd = ctx.createLinearGradient(0, -22, 0, 22);
                    grd.addColorStop(0, '#88ffdd');
                    grd.addColorStop(0.5, '#00ddbb');
                    grd.addColorStop(1, '#008899');
                    ctx.fillStyle = grd;
                    ctx.strokeStyle = '#00ffcc';
                    ctx.lineWidth = 1.5;

                    ctx.beginPath();
                    ctx.moveTo(0, -22);
                    ctx.quadraticCurveTo(14, -14, 18, -4);
                    ctx.quadraticCurveTo(20, 2, 16, 10);
                    ctx.quadraticCurveTo(12, 16, 6, 18);
                    ctx.lineTo(0, 20);
                    ctx.lineTo(-6, 18);
                    ctx.quadraticCurveTo(-12, 16, -16, 10);
                    ctx.quadraticCurveTo(-20, 2, -18, -4);
                    ctx.quadraticCurveTo(-14, -14, 0, -22);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();

                    // Cockpit
                    ctx.shadowBlur = 15;
                    const grd2 = ctx.createRadialGradient(0, -8, 2, 0, -8, 10);
                    grd2.addColorStop(0, '#aaffff');
                    grd2.addColorStop(0.7, '#00ccdd');
                    grd2.addColorStop(1, '#006688');
                    ctx.fillStyle = grd2;
                    ctx.beginPath();
                    ctx.ellipse(0, -6, 8, 10, 0, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.strokeStyle = '#44ffdd';
                    ctx.lineWidth = 0.8;
                    ctx.stroke();

                    // Wing details
                    ctx.shadowBlur = 10;
                    ctx.strokeStyle = '#44ffdd';
                    ctx.lineWidth = 1.2;
                    ctx.beginPath();
                    ctx.moveTo(-16, 0);
                    ctx.lineTo(-22, 6);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(16, 0);
                    ctx.lineTo(22, 6);
                    ctx.stroke();

                    ctx.shadowBlur = 0;
                    ctx.restore();
                    ctx.globalAlpha = 1;

                    // Trail
                    for (let i = 0; i < this.trail.length - 1; i++) {
                        const p = this.trail[i];
                        const alpha = (i / this.trail.length) * 0.35;
                        const size = (i / this.trail.length) * 6 + 1;
                        ctx.globalAlpha = alpha;
                        ctx.fillStyle = '#00ffcc';
                        ctx.shadowColor = '#00ffcc';
                        ctx.shadowBlur = 10;
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, size, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    ctx.shadowBlur = 0;
                    ctx.globalAlpha = 1;

                    this.bullets.forEach(b => b.draw());
                }
                getBounds() {
                    return {
                        x: this.x - 16,
                        y: this.y - 20,
                        w: 32,
                        h: 38
                    };
                }
            }

            // ─── Bullet ──────────────────────────────────────────────
            class Bullet {
                constructor(x, y) {
                    this.x = x;
                    this.y = y;
                    this.speed = 10;
                    this.alive = true;
                    this.trail = [];
                    this.w = 4;
                    this.h = 16;
                }
                update() {
                    this.trail.push({ x: this.x, y: this.y });
                    if (this.trail.length > 12) this.trail.shift();
                    this.y -= this.speed;
                    if (this.y < -20) this.alive = false;
                }
                draw() {
                    // Trail
                    for (let i = 0; i < this.trail.length - 1; i++) {
                        const p = this.trail[i];
                        const alpha = (i / this.trail.length) * 0.6;
                        const size = (i / this.trail.length) * 4 + 1.5;
                        ctx.globalAlpha = alpha;
                        ctx.fillStyle = '#00ffff';
                        ctx.shadowColor = '#00ffff';
                        ctx.shadowBlur = 20;
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, size, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    ctx.shadowBlur = 30;
                    ctx.shadowColor = '#00ffff';
                    ctx.globalAlpha = 1;
                    const grd = ctx.createLinearGradient(this.x, this.y - 8, this.x, this.y + 8);
                    grd.addColorStop(0, '#ffffff');
                    grd.addColorStop(0.3, '#00ffff');
                    grd.addColorStop(1, '#0088ff');
                    ctx.fillStyle = grd;
                    ctx.fillRect(this.x - 2.5, this.y - 8, 5, 16);
                    ctx.shadowBlur = 0;
                }
                getBounds() {
                    return { x: this.x - 4, y: this.y - 10, w: 8, h: 20 };
                }
            }

            // ─── Enemy ──────────────────────────────────────────────
            class Enemy {
                constructor(x, y, type) {
                    this.x = x;
                    this.y = y;
                    this.type = type || 'basic';
                    this.alive = true;
                    this.hitFlash = 0;
                    this.angle = 0;

                    switch (this.type) {
                        case 'basic':
                            this.w = 30;
                            this.h = 30;
                            this.hp = 1;
                            this.speed = 1.8 + Math.random() * 0.8;
                            this.score = 10;
                            this.color1 = '#ff4455';
                            this.color2 = '#cc1133';
                            this.glowColor = '#ff4455';
                            this.shootChance = 0.002;
                            break;
                        case 'fast':
                            this.w = 24;
                            this.h = 24;
                            this.hp = 1;
                            this.speed = 3.2 + Math.random() * 0.8;
                            this.score = 15;
                            this.color1 = '#ff8833';
                            this.color2 = '#cc5511';
                            this.glowColor = '#ff8833';
                            this.shootChance = 0.003;
                            break;
                        case 'tank':
                            this.w = 38;
                            this.h = 38;
                            this.hp = 3;
                            this.speed = 1.0 + Math.random() * 0.5;
                            this.score = 25;
                            this.color1 = '#aa44ff';
                            this.color2 = '#7711cc';
                            this.glowColor = '#aa44ff';
                            this.shootChance = 0.005;
                            break;
                    }
                    this.maxHp = this.hp;
                    this.wobble = Math.random() * 100;
                    this.wobbleSpeed = 0.02 + Math.random() * 0.02;
                    this.wobbleAmp = 0.5 + Math.random() * 0.8;
                    this.enemyBullets = [];
                }
                update() {
                    if (!this.alive) return;
                    this.y += this.speed;
                    this.x += Math.sin(this.wobble) * this.wobbleAmp;
                    this.wobble += this.wobbleSpeed;
                    if (this.hitFlash > 0) this.hitFlash--;
                    this.angle += 0.03;

                    if (this.y > H + 40) this.alive = false;

                    // Enemy shooting
                    if (Math.random() < this.shootChance && this.y > 40 && this.y < H * 0.7) {
                        this.enemyBullets.push(new EnemyBullet(this.x, this.y + this.h / 2));
                    }
                    this.enemyBullets.forEach(b => b.update());
                    this.enemyBullets = this.enemyBullets.filter(b => b.alive);
                }
                draw(time) {
                    if (!this.alive) return;
                    const px = this.x,
                        py = this.y;
                    const flash = this.hitFlash > 0;

                    // Glow
                    ctx.shadowColor = this.glowColor;
                    ctx.shadowBlur = 25;

                    // Body
                    const grd = ctx.createRadialGradient(px - 4, py - 4, 2, px, py, this.w / 2 + 4);
                    grd.addColorStop(0, flash ? '#ffffff' : this.color1);
                    grd.addColorStop(0.7, flash ? '#ffaaaa' : this.color2);
                    grd.addColorStop(1, flash ? '#ff6666' : '#441122');

                    ctx.fillStyle = grd;
                    ctx.strokeStyle = this.glowColor;
                    ctx.lineWidth = 1.5;

                    const r = this.w / 2;
                    ctx.beginPath();
                    if (this.type === 'fast') {
                        // Diamond
                        ctx.moveTo(px, py - r);
                        ctx.lineTo(px + r, py);
                        ctx.lineTo(px, py + r);
                        ctx.lineTo(px - r, py);
                        ctx.closePath();
                    } else if (this.type === 'tank') {
                        // Hexagon
                        for (let i = 0; i < 6; i++) {
                            const a = i / 6 * Math.PI * 2 - Math.PI / 2 + this.angle * 0.1;
                            const rr = r + (i % 2 === 0 ? 2 : -2);
                            if (i === 0) ctx.moveTo(px + rr * Math.cos(a), py + rr * Math.sin(a));
                            else ctx.lineTo(px + rr * Math.cos(a), py + rr * Math.sin(a));
                        }
                        ctx.closePath();
                    } else {
                        // Basic - triangle down
                        ctx.moveTo(px, py + r);
                        ctx.lineTo(px - r, py - r * 0.7);
                        ctx.lineTo(px - r * 0.4, py - r * 0.4);
                        ctx.lineTo(px, py - r * 0.5);
                        ctx.lineTo(px + r * 0.4, py - r * 0.4);
                        ctx.lineTo(px + r, py - r * 0.7);
                        ctx.closePath();
                    }
                    ctx.fill();
                    ctx.stroke();

                    // Eye / core
                    ctx.shadowBlur = 10;
                    ctx.fillStyle = flash ? '#ffffff' : '#ffffffcc';
                    ctx.beginPath();
                    ctx.arc(px, py, r * 0.25 + 2, 0, Math.PI * 2);
                    ctx.fill();

                    // HP bar for tank
                    if (this.type === 'tank' && this.hp < this.maxHp) {
                        ctx.shadowBlur = 0;
                        const bw = this.w * 0.8;
                        const bh = 3;
                        const by = py - r - 6;
                        ctx.fillStyle = '#333355';
                        ctx.fillRect(px - bw / 2, by, bw, bh);
                        ctx.fillStyle = '#aa44ff';
                        ctx.shadowColor = '#aa44ff';
                        ctx.shadowBlur = 8;
                        ctx.fillRect(px - bw / 2, by, bw * (this.hp / this.maxHp), bh);
                    }

                    ctx.shadowBlur = 0;

                    // Enemy bullets
                    this.enemyBullets.forEach(b => b.draw());
                }
                getBounds() {
                    const r = this.w / 2;
                    return { x: this.x - r, y: this.y - r, w: this.w, h: this.h };
                }
                takeDamage(dmg) {
                    this.hp -= dmg;
                    this.hitFlash = 8;
                    if (this.hp <= 0) {
                        this.alive = false;
                        return true;
                    }
                    return false;
                }
            }

            // ─── Enemy Bullet ────────────────────────────────────────
            class EnemyBullet {
                constructor(x, y) {
                    this.x = x;
                    this.y = y;
                    this.speed = 3.5 + Math.random() * 1.5;
                    this.alive = true;
                    this.w = 8;
                    this.h = 8;
                    this.trail = [];
                }
                update() {
                    this.trail.push({ x: this.x, y: this.y });
                    if (this.trail.length > 8) this.trail.shift();
                    this.y += this.speed;
                    if (this.y > H + 20) this.alive = false;
                }
                draw() {
                    for (let i = 0; i < this.trail.length - 1; i++) {
                        const p = this.trail[i];
                        const alpha = (i / this.trail.length) * 0.4;
                        ctx.globalAlpha = alpha;
                        ctx.fillStyle = '#ff8844';
                        ctx.shadowColor = '#ff8844';
                        ctx.shadowBlur = 12;
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, (i / this.trail.length) * 3 + 1, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    ctx.shadowBlur = 25;
                    ctx.shadowColor = '#ff6633';
                    ctx.globalAlpha = 1;
                    const grd = ctx.createRadialGradient(this.x, this.y, 1, this.x, this.y, 6);
                    grd.addColorStop(0, '#ffffff');
                    grd.addColorStop(0.4, '#ff8844');
                    grd.addColorStop(1, '#ff4400');
                    ctx.fillStyle = grd;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, 5, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.shadowBlur = 0;
                }
                getBounds() {
                    return { x: this.x - 5, y: this.y - 5, w: 10, h: 10 };
                }
            }

            // ─── Particles ────────────────────────────────────────────
            class Particle {
                constructor(x, y, color, speed, size, life) {
                    this.x = x;
                    this.y = y;
                    const angle = Math.random() * Math.PI * 2;
                    const spd = speed || (1 + Math.random() * 4);
                    this.vx = Math.cos(angle) * spd;
                    this.vy = Math.sin(angle) * spd - 0.5;
                    this.size = size || (2 + Math.random() * 5);
                    this.life = life || (30 + Math.random() * 40);
                    this.maxLife = this.life;
                    this.color = color || '#ff8844';
                    this.alive = true;
                    this.gravity = 0.04;
                    this.drag = 0.98;
                }
                update() {
                    this.vx *= this.drag;
                    this.vy *= this.drag;
                    this.vy += this.gravity;
                    this.x += this.vx;
                    this.y += this.vy;
                    this.life--;
                    if (this.life <= 0 || this.y > H + 20) this.alive = false;
                }
                draw() {
                    const a = this.life / this.maxLife;
                    const s = this.size * (0.3 + 0.7 * a);
                    ctx.globalAlpha = a * 0.9;
                    ctx.shadowColor = this.color;
                    ctx.shadowBlur = 12;
                    ctx.fillStyle = this.color;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, s, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.shadowBlur = 0;
                    ctx.globalAlpha = 1;
                }
            }

            // ─── Floating Text ──────────────────────────────────────
            class FloatingText {
                constructor(x, y, text, color) {
                    this.x = x;
                    this.y = y;
                    this.text = text;
                    this.color = color || '#00ffcc';
                    this.life = 50;
                    this.maxLife = 50;
                    this.vy = -1.8;
                    this.alive = true;
                    this.size = 22 + Math.random() * 6;
                }
                update() {
                    this.y += this.vy;
                    this.vy *= 0.97;
                    this.life--;
                    if (this.life <= 0) this.alive = false;
                }
                draw() {
                    const a = this.life / this.maxLife;
                    ctx.globalAlpha = a;
                    ctx.shadowColor = this.color;
                    ctx.shadowBlur = 20;
                    ctx.fillStyle = this.color;
                    ctx.font = `bold ${this.size}px 'Courier New', monospace`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(this.text, this.x, this.y);
                    ctx.shadowBlur = 0;
                    ctx.globalAlpha = 1;
                }
            }

            // ─── Game State ──────────────────────────────────────────
            let player, enemies, particles, floatingTexts, stars;
            let score = 0;
            let highScore = parseInt(localStorage.getItem('spaceShooterHighScore')) || 0;
            let gameState = 'menu'; // 'menu' | 'playing' | 'gameover'
            let frameCount = 0;
            let spawnTimer = 0;
            let spawnInterval = 70;
            let difficulty = 1;
            let screenShake = { x: 0, y: 0, intensity: 0 };
            let comboCount = 0;

            uiHighScore.textContent = '🏆 ' + highScore;

            function initGame() {
                player = new Player();
                enemies = [];
                particles = [];
                floatingTexts = [];
                stars = [];
                for (let i = 0; i < 150; i++) stars.push(new Star());
                score = 0;
                frameCount = 0;
                spawnTimer = 0;
                spawnInterval = 70;
                difficulty = 1;
                comboCount = 0;
                screenShake.intensity = 0;
                updateUI();
            }

            function spawnEnemy() {
                const types = ['basic', 'basic', 'basic', 'fast', 'fast', 'tank'];
                const type = types[Math.floor(Math.random() * types.length)];
                const x = 40 + Math.random() * (W - 80);
                const y = -30;
                const enemy = new Enemy(x, y, type);
                // Scale difficulty
                enemy.speed *= (1 + (difficulty - 1) * 0.06);
                enemies.push(enemy);
            }

            function triggerExplosion(x, y, color, count, speed, size) {
                const colors = color ? [color] : ['#ff6644', '#ffaa44', '#ff4422', '#ffcc66', '#ffffff'];
                for (let i = 0; i < (count || 35); i++) {
                    const c = colors[Math.floor(Math.random() * colors.length)];
                    const s = speed || (2 + Math.random() * 4);
                    const sz = size || (2 + Math.random() * 5);
                    particles.push(new Particle(x, y, c, s, sz));
                }
                // Extra sparkle
                for (let i = 0; i < 8; i++) {
                    const c = '#ffffff';
                    const s = 1 + Math.random() * 2;
                    const sz = 1 + Math.random() * 2;
                    const p = new Particle(x, y, c, s, sz, 15 + Math.random() * 10);
                    p.gravity = 0.01;
                    particles.push(p);
                }
            }

            function triggerScreenShake(intensity) {
                screenShake.intensity = Math.max(screenShake.intensity, intensity);
            }

            function addFloatingText(x, y, text, color) {
                floatingTexts.push(new FloatingText(x, y, text, color));
            }

            function updateUI() {
                uiScore.textContent = '⭐ ' + score;
                uiHighScore.textContent = '🏆 ' + highScore;
                let hearts = '';
                for (let i = 0; i < player.lives; i++) hearts += '❤️ ';
                if (player.lives === 0) hearts = '💀';
                uiLives.textContent = hearts || '💀';
            }

            function checkCollisions(a, b) {
                return a.x < b.x + b.w &&
                    a.x + a.w > b.x &&
                    a.y < b.y + b.h &&
                    a.y + a.h > b.y;
            }

            function gameOver() {
                gameState = 'gameover';
                gameRunning = false;
                if (score > highScore) {
                    highScore = score;
                    localStorage.setItem('spaceShooterHighScore', String(highScore));
                    uiHighScore.textContent = '🏆 ' + highScore;
                }
                overlayFinalScore.textContent = '✦ SCORE: ' + score + ' ✦';
                overlayFinalHigh.textContent = '🏆 HIGH: ' + highScore;
                overlayFinalScore.style.display = 'block';
                overlayFinalHigh.style.display = 'block';
                overlayTitle.textContent = 'GAME OVER';
                overlayTitle.className = 'gameover-title';
                overlaySub.textContent = '✦ ✦ ✦';
                overlayMenuSub.style.display = 'none';
                actionBtn.style.display = 'inline-block';
                actionBtn.textContent = '▶ PLAY AGAIN';
                overlay.classList.add('show');
            }

            function startGame() {
                initGame();
                gameState = 'playing';
                gameRunning = true;
                overlay.classList.remove('show');
                overlayFinalScore.style.display = 'none';
                overlayFinalHigh.style.display = 'none';
                overlayTitle.className = '';
                overlayMenuSub.style.display = 'block';
                actionBtn.style.display = 'none';
            }

            // ─── Main Game Loop ──────────────────────────────────────
            function update() {
                if (gameState !== 'playing' || !gameRunning) return;
                frameCount++;

                // Difficulty scaling
                difficulty = 1 + Math.floor(score / 150) * 0.5;
                spawnInterval = Math.max(18, 70 - difficulty * 4);

                // Spawn enemies
                spawnTimer++;
                if (spawnTimer >= spawnInterval) {
                    spawnTimer = 0;
                    spawnEnemy();
                    if (Math.random() < 0.2 + difficulty * 0.02) {
                        spawnEnemy(); // double spawn
                    }
                }

                // Update player
                player.update();

                // Update enemies
                enemies.forEach(e => e.update());
                enemies = enemies.filter(e => e.alive || e.enemyBullets.some(b => b.alive));

                // Update particles
                particles.forEach(p => p.update());
                particles = particles.filter(p => p.alive);

                // Update floating texts
                floatingTexts.forEach(f => f.update());
                floatingTexts = floatingTexts.filter(f => f.alive);

                // Update stars
                stars.forEach(s => s.update());

                // ── Collisions: Player bullets → Enemies ──
                for (let i = player.bullets.length - 1; i >= 0; i--) {
                    const b = player.bullets[i];
                    if (!b.alive) continue;
                    const bb = b.getBounds();
                    let hit = false;
                    for (let j = enemies.length - 1; j >= 0; j--) {
                        const e = enemies[j];
                        if (!e.alive) continue;
                        const eb = e.getBounds();
                        if (checkCollisions(bb, eb)) {
                            b.alive = false;
                            hit = true;
                            const destroyed = e.takeDamage(1);
                            if (destroyed) {
                                // Kill enemy
                                const color = e.color1;
                                triggerExplosion(e.x, e.y, color, 40 + Math.random() * 20, 2 + Math.random() * 3, 2 + Math
                                .random() * 4);
                                triggerScreenShake(4 + Math.random() * 4);
                                const pts = e.score * (1 + Math.floor(difficulty * 0.3));
                                addFloatingText(e.x, e.y - 10, '+' + pts, e.color1);
                                score += pts;
                                comboCount++;
                                if (comboCount > 5) {
                                    addFloatingText(e.x, e.y - 40, '🔥 x' + comboCount, '#ffcc44');
                                    score += Math.floor(comboCount / 3) * 2;
                                }
                                updateUI();
                                // Drop pickup? small chance
                                if (Math.random() < 0.08) {
                                    // health restore
                                    if (player.lives < 5) {
                                        player.lives++;
                                        addFloatingText(e.x, e.y - 70, '❤️ +1', '#ff4477');
                                        updateUI();
                                    }
                                }
                            } else {
                                // Hit but not destroyed
                                triggerExplosion(e.x, e.y, '#ffffff', 8, 1, 2);
                                triggerScreenShake(2);
                            }
                            break;
                        }
                    }
                    if (hit) {
                        player.bullets.splice(i, 1);
                    }
                }

                // ── Collisions: Enemy bullets → Player ──
                if (player.invincible === 0 && player.alive) {
                    const pb = player.getBounds();
                    for (let j = enemies.length - 1; j >= 0; j--) {
                        const e = enemies[j];
                        if (!e.alive) continue;
                        for (let k = e.enemyBullets.length - 1; k >= 0; k--) {
                            const eb = e.enemyBullets[k];
                            if (!eb.alive) continue;
                            const ebb = eb.getBounds();
                            if (checkCollisions(pb, ebb)) {
                                eb.alive = false;
                                playerHit();
                                break;
                            }
                        }
                        e.enemyBullets = e.enemyBullets.filter(b => b.alive);
                        // Also collision with enemy body
                        const eb2 = e.getBounds();
                        if (checkCollisions(pb, eb2) && e.alive) {
                            // Ram enemy
                            e.alive = false;
                            triggerExplosion(e.x, e.y, e.color1, 30, 3, 3);
                            triggerScreenShake(6);
                            playerHit();
                        }
                    }
                }

                // ── Collisions: Enemies passing bottom ──
                for (let j = enemies.length - 1; j >= 0; j--) {
                    const e = enemies[j];
                    if (!e.alive) continue;
                    if (e.y > H + 30) {
                        e.alive = false;
                        // Lose life if enemy reaches bottom
                        if (player.alive && player.invincible === 0) {
                            playerHit();
                        }
                    }
                }
                enemies = enemies.filter(e => e.alive || e.enemyBullets.some(b => b.alive));

                // Clean up dead enemies fully
                enemies = enemies.filter(e => e.alive);

                // ── Screen shake decay ──
                if (screenShake.intensity > 0) {
                    screenShake.intensity *= 0.9;
                    if (screenShake.intensity < 0.1) screenShake.intensity = 0;
                    screenShake.x = (Math.random() - 0.5) * screenShake.intensity * 2;
                    screenShake.y = (Math.random() - 0.5) * screenShake.intensity * 2;
                } else {
                    screenShake.x = 0;
                    screenShake.y = 0;
                }

                // ── Check game over ──
                if (player.lives <= 0 && player.alive) {
                    player.alive = false;
                    triggerExplosion(player.x, player.y, '#00ffcc', 50, 4, 4);
                    triggerScreenShake(12);
                    setTimeout(() => { gameOver(); }, 500);
                }

                // Update UI periodically
                if (frameCount % 3 === 0) updateUI();
            }

            function playerHit() {
                if (player.invincible > 0 || !player.alive) return;
                player.lives--;
                player.invincible = 90; // 1.5 seconds
                triggerExplosion(player.x, player.y, '#ff4477', 25, 2, 3);
                triggerScreenShake(8);
                comboCount = 0;
                updateUI();
                if (player.lives <= 0) {
                    player.alive = false;
                    triggerExplosion(player.x, player.y, '#00ffcc', 50, 4, 4);
                    triggerScreenShake(12);
                    setTimeout(() => { gameOver(); }, 500);
                }
            }

            // ─── Render ─────────────────────────────────────────────
            function draw() {
                ctx.clearRect(0, 0, W, H);

                // ── Background ──
                const grad = ctx.createLinearGradient(0, 0, 0, H);
                grad.addColorStop(0, '#060612');
                grad.addColorStop(0.5, '#0a0a1f');
                grad.addColorStop(1, '#0f0a1a');
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, W, H);

                // ── Stars ──
                stars.forEach(s => s.draw(frameCount));

                // ── Screen shake ──
                ctx.save();
                if (screenShake.intensity > 0.5) {
                    ctx.translate(screenShake.x, screenShake.y);
                }

                // ── Draw enemies ──
                enemies.forEach(e => e.draw(frameCount));

                // ── Draw player ──
                if (player && player.alive) {
                    player.draw(frameCount);
                } else if (player) {
                    // Draw player bullets even if dead
                    player.bullets.forEach(b => b.draw());
                }

                // ── Draw particles ──
                particles.forEach(p => p.draw());

                // ── Draw floating texts ──
                floatingTexts.forEach(f => f.draw());

                // ── Draw all bullets from enemies (already drawn in enemy draw) ──
                // But also draw any stray enemy bullets
                for (const e of enemies) {
                    if (e.enemyBullets) {
                        e.enemyBullets.forEach(b => b.draw());
                    }
                }

                // ── Draw player bullets that might be missed ──
                if (player) {
                    player.bullets.forEach(b => b.draw());
                }

                ctx.restore();

                // ── Vignette ──
                const vg = ctx.createRadialGradient(W / 2, H / 2, H * 0.3, W / 2, H / 2, H * 0.9);
                vg.addColorStop(0, 'rgba(0,0,0,0)');
                vg.addColorStop(1, 'rgba(0,0,20,0.4)');
                ctx.fillStyle = vg;
                ctx.fillRect(0, 0, W, H);

                // ── Border glow ──
                ctx.shadowColor = '#00ffcc';
                ctx.shadowBlur = 2;
                ctx.strokeStyle = 'rgba(0,255,204,0.06)';
                ctx.lineWidth = 1;
                ctx.strokeRect(2, 2, W - 4, H - 4);
                ctx.shadowBlur = 0;

                // ── Combo display ──
                if (comboCount > 2 && gameState === 'playing') {
                    ctx.globalAlpha = 0.6 + 0.4 * Math.sin(frameCount * 0.06);
                    ctx.shadowColor = '#ffcc44';
                    ctx.shadowBlur = 25;
                    ctx.fillStyle = '#ffcc44';
                    ctx.font = 'bold 20px "Courier New", monospace';
                    ctx.textAlign = 'right';
                    ctx.textBaseline = 'top';
                    ctx.fillText('🔥 x' + comboCount, W - 24, 70);
                    ctx.shadowBlur = 0;
                    ctx.globalAlpha = 1;
                }
            }

            // ─── Game Loop ───────────────────────────────────────────
            function gameLoop() {
                update();
                draw();
                requestAnimationFrame(gameLoop);
            }

            // ─── Input ──────────────────────────────────────────────
            document.addEventListener('keydown', (e) => {
                keys[e.code] = true;
                if (e.code === 'Space') {
                    e.preventDefault();
                    if (gameState === 'menu') {
                        startGame();
                    } else if (gameState === 'gameover') {
                        startGame();
                    }
                }
            });
            document.addEventListener('keyup', (e) => {
                keys[e.code] = false;
            });

            // Touch controls for mobile
            let touchX = null,
                touchY = null;
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                const t = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                touchX = t.clientX - rect.left;
                touchY = t.clientY - rect.top;
                if (gameState === 'menu') { startGame(); return; }
                if (gameState === 'gameover') { startGame(); return; }
                // Shoot on touch
                if (gameState === 'playing') {
                    keys['Space'] = true;
                }
            }, { passive: false });
            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                const t = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                touchX = t.clientX - rect.left;
                touchY = t.clientY - rect.top;
                // Map touch to keyboard
                if (gameState === 'playing') {
                    const cx = W / 2,
                        cy = H - 100;
                    const dx = touchX - cx,
                        dy = touchY - cy;
                    keys['ArrowLeft'] = dx < -20;
                    keys['ArrowRight'] = dx > 20;
                    keys['ArrowUp'] = dy < -20;
                    keys['ArrowDown'] = dy > 20;
                }
            }, { passive: false });
            canvas.addEventListener('touchend', (e) => {
                e.preventDefault();
                touchX = null;
                touchY = null;
                keys['ArrowLeft'] = false;
                keys['ArrowRight'] = false;
                keys['ArrowUp'] = false;
                keys['ArrowDown'] = false;
                keys['Space'] = false;
            }, { passive: false });

            // Button handler
            actionBtn.addEventListener('click', () => {
                startGame();
            });

            // ─── Init ───────────────────────────────────────────────
            initGame();
            gameState = 'menu';
            overlayTitle.textContent = 'SPACE SHOOTER';
            overlayTitle.className = '';
            overlaySub.textContent = '✦ NEON EDGE ✦';
            overlayMenuSub.style.display = 'block';
            overlayFinalScore.style.display = 'none';
            overlayFinalHigh.style.display = 'none';
            actionBtn.style.display = 'none';
            overlay.classList.add('show');
            updateUI();

            // ─── Start loop ─────────────────────────────────────────
            gameLoop();

            // ─── Handle resize ──────────────────────────────────────
            window.addEventListener('resize', () => {
                resize();
                if (player) {
                    player.x = Math.min(Math.max(player.x, player.w / 2), W - player.w / 2);
                    player.y = Math.min(Math.max(player.y, player.h / 2 + 30), H - player.h / 2 - 10);
                }
            });

        })();
    </script>
</body>
</html>
