<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gamepad — Play. Earn. Dominate.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
:root{
  --bg:#0d0f10;
  --panel:#121518;
  --panel-2:#171a1e;
  --text:#e7edf2;
  --muted:#98a2ad;
  --accent:#ff6a00;
  --accent-2:#00e0ff;
  --outline:#2a2f36;
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  background:linear-gradient(180deg, #0b0d0f 0%, #0d0f10 40%, #0a0c0d 100%);
  color:var(--text);
  font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji";
}
.container{width:min(1200px, 92vw); margin:0 auto}

/* Nav */
.nav{position:sticky; top:0; z-index:10; backdrop-filter:saturate(180%) blur(10px); background:rgba(13,15,16,.6); border-bottom:1px solid var(--outline)}
.nav .container{display:flex; align-items:center; gap:16px; padding:14px 0}
.brand{font-family:Rajdhani, Inter, sans-serif; font-weight:800; letter-spacing:.12em; color:var(--accent); display:inline-flex; align-items:center; gap:8px}
.brand .logo-icon{width:22px; height:22px; color:var(--accent)}
.links{display:flex; gap:18px; margin-left:auto}
.links a{color:var(--muted); text-decoration:none; font-weight:600; font-size:14px}
.links a:hover{color:var(--text)}
.actions{display:flex; gap:10px; margin-left:8px}
.menu-toggle{display:none; align-items:center; justify-content:center; width:40px; height:40px; border-radius:8px; border:1px solid var(--outline); background:transparent; color:var(--text); cursor:pointer}
.menu-toggle:hover{border-color:var(--accent); color:var(--accent)}

/* Mobile Menu */
.mobile-scrim{position:fixed; inset:0; background:rgba(0,0,0,.42); opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:15}
.mobile-menu{position:fixed; top:0; left:0; height:100vh; width:min(88vw, 360px); background:var(--panel); border-right:1px solid var(--outline); transform:translateX(-100%); transition:transform .25s ease; z-index:20; box-shadow:6px 0 24px rgba(0,0,0,.45)}
.mobile-menu .mm-inner{display:flex; flex-direction:column; gap:10px; padding:72px 16px 16px}
.mobile-menu a{color:var(--text); text-decoration:none; font-weight:700; padding:10px 0}
.mobile-menu a:hover{color:var(--accent)}
.mobile-menu .mm-actions{display:flex; gap:10px; flex-wrap:wrap; margin-top:6px}
.menu-open .mobile-menu{transform:translateX(0)}
.menu-open .mobile-scrim{opacity:1; pointer-events:auto}
body.menu-open{overflow:hidden}

/* Buttons */
.btn{appearance:none; border:1px solid transparent; background:transparent; color:var(--text); padding:12px 18px; border-radius:8px; font-weight:700; letter-spacing:.02em; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:8px; cursor:pointer}
.btn.sm{padding:8px 12px; font-weight:700}
.btn-primary{background:var(--accent); color:#111}
.btn-primary:hover{filter:brightness(1.05)}
.btn-outline{border-color:var(--outline)}
.btn-outline:hover{border-color:var(--accent); color:var(--accent)}
.btn-accent{background:transparent; border-color:var(--accent); color:var(--accent)}
.btn-accent:hover{background:rgba(255,106,0,.08)}
.btn-dark{background:#111; color:var(--text)}
.btn-ghost{border-color:transparent; color:var(--muted)}
.btn-ghost:hover{color:var(--text)}

/* Hero */
.hero{position:relative; padding:96px 0 112px; background:transparent; border-bottom:1px solid var(--outline)}
.hero-inner{display:flex; flex-direction:column; align-items:flex-start; text-align:left; gap:18px}
.pill{display:inline-block; font-size:12px; letter-spacing:.14em; text-transform:uppercase; background:rgba(0,224,255,.1); color:var(--accent-2); border:1px solid rgba(0,224,255,.3); padding:8px 12px; border-radius:999px}
.headline{font-family:Rajdhani, Inter, sans-serif; font-size: clamp(36px, 6vw, 72px); line-height:.95; font-weight:900; margin:0; display:flex; gap:0; flex-wrap:nowrap; align-items:baseline}
.headline .dim{opacity:.9}
.headline .accent{color:var(--accent)}
.headline .typeword{position:relative; -webkit-text-stroke: .5px rgba(0,0,0,.35)}
.headline .dim{color:#eaf6ff; text-shadow:0 0 6px rgba(0,224,255,.55),0 0 14px rgba(0,224,255,.35),0 0 26px rgba(0,224,255,.25); animation:neonCyan 2.6s ease-in-out infinite alternate}
.headline .accent{color:var(--accent); text-shadow:0 0 6px rgba(255,106,0,.65),0 0 16px rgba(255,106,0,.45),0 0 28px rgba(255,106,0,.35); animation:neonOrange 2.8s ease-in-out infinite alternate}
.headline .typeword{display:inline-block; width:0; overflow:hidden; white-space:nowrap}
.headline .typing{animation:type-on var(--type-speed,1.8s) steps(var(--chars), end) forwards}
.subtitle{max-width:800px; color:var(--muted); font-size:16px}
.cta{display:flex; gap:14px; margin-top:6px}

/* Sections */
.section{padding:72px 0; position:relative; overflow:hidden}
.section-head{display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:24px}
.section h2{font-family:Rajdhani, Inter, sans-serif; font-size:32px; margin:0}

/* Slider */
.slider{position:relative}
.slider-viewport{overflow:hidden; border-radius:16px}
.slider-track{display:flex; transition:transform .35s ease; will-change:transform}
.slide{flex:0 0 100%; padding:0}
.slide .card{min-height:clamp(260px, 48vw, 420px); padding:24px}
.slider-btn{position:absolute; top:50%; transform:translateY(-50%); width:40px; height:40px; border-radius:999px; border:1px solid rgba(255,255,255,.12); background:rgba(13,15,16,.55); color:var(--text); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; backdrop-filter:saturate(140%) blur(8px)}
.slider-btn:hover{border-color:var(--accent); color:var(--accent)}
.slider-btn.prev{left:8px}
.slider-btn.next{right:8px}
.slider-dots{display:flex; gap:8px; justify-content:center; margin-top:12px}
.slider-dots button{width:8px; height:8px; border-radius:999px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.12); cursor:pointer}
.slider-dots button[aria-current="true"]{border-color:var(--accent); background:var(--accent)}

/* Card styles */
.card{background:rgba(18,21,24,.55); border:1px solid rgba(255,255,255,.06); backdrop-filter:saturate(140%) blur(8px); border-radius:14px; padding:20px; display:flex; flex-direction:column; justify-content:flex-end; position:relative; overflow:hidden}
.card > *:not(.card-bg){position:relative; z-index:2}
.card .card-bg{position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:0; filter:saturate(1.05) brightness(.7)}
.card::after{content:""; position:absolute; inset:0; z-index:1; background:linear-gradient(180deg, rgba(0,0,0,.15) 0%, rgba(0,0,0,.55) 100%)}
.card h3{margin:0 0 6px; font-family:Rajdhani, Inter, sans-serif; font-size:24px}
.card p{margin:0 0 12px; color:var(--muted)}
.card .chip{background:rgba(255,106,0,.12); color:var(--accent); border:1px solid rgba(255,106,0,.35); border-radius:999px; padding:4px 8px; font-size:12px}
.card .tag{background:rgba(0,224,255,.12); color:var(--accent-2); border:1px solid rgba(0,224,255,.35); border-radius:999px; padding:4px 8px; font-size:12px}
.card .card-meta{position:absolute; top:14px; left:14px; display:flex; gap:8px}

.card-xl{grid-column:1 / 2; grid-row:1 / 3; background:linear-gradient(135deg, rgba(0,224,255,.16), rgba(0,0,0,0) 40%),radial-gradient(80% 120% at 110% -10%, rgba(255,106,0,.2), rgba(0,0,0,0) 50%),var(--panel-2)}
.card-tall{grid-column:2 / 3; grid-row:1 / 2; background:radial-gradient(60% 60% at 20% 20%, rgba(0,224,255,.2), rgba(0,0,0,0) 50%),linear-gradient(180deg, #0f1120, #0c0e12)}

/* Keyframes */
@keyframes type-on{from{width:0} to{width:var(--chars)ch}}
@keyframes neonCyan{from{text-shadow:0 0 4px rgba(0,224,255,.45),0 0 10px rgba(0,224,255,.30),0 0 18px rgba(0,224,255,.22)}to{text-shadow:0 0 10px rgba(0,224,255,.70),0 0 22px rgba(0,224,255,.42),0 0 36px rgba(0,224,255,.32)}}
@keyframes neonOrange{from{text-shadow:0 0 4px rgba(255,106,0,.50),0 0 12px rgba(255,106,0,.35),0 0 20px rgba(255,106,0,.26)}to{text-shadow:0 0 10px rgba(255,106,0,.75),0 0 24px rgba(255,106,0,.48),0 0 40px rgba(255,106,0,.36)}}

/* Value section */
.value{border-top:1px solid var(--outline)}
.muted{color:var(--muted)}
.feature-grid{display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-top:18px}
.feature{background:rgba(18,21,24,.55); border:1px solid rgba(255,255,255,.06); backdrop-filter:saturate(140%) blur(8px); border-radius:12px; padding:18px}
.feature .ico{font-size:22px; margin-bottom:8px; color:var(--accent)}

/* Banner */
.banner{background:linear-gradient(180deg, rgba(255,106,0,.98), rgba(255,106,0,.95)); color:#101010}
.banner-inner{display:flex; align-items:center; justify-content:space-between; gap:16px}
.banner .eyebrow{letter-spacing:.12em; text-transform:uppercase; font-weight:800}
.banner p{margin:.35rem 0 0; max-width:800px}

/* Section decorative glows */
.section::before{content:""; position:absolute; inset:-20% -10% auto -10%; height:60%; z-index:0; pointer-events:none; background:radial-gradient(50% 60% at 20% 20%, rgba(0,224,255,.10), rgba(0,0,0,0) 60%),radial-gradient(40% 40% at 80% -10%, rgba(255,106,0,.10), rgba(0,0,0,0) 60%);filter:blur(8px)}
.choose::before{background:radial-gradient(50% 60% at 15% 10%, rgba(0,224,255,.16), rgba(0,0,0,0) 60%),radial-gradient(40% 50% at 90% -10%, rgba(255,106,0,.12), rgba(0,0,0,0) 60%)}
.value::before{background:radial-gradient(40% 50% at 85% 15%, rgba(255,106,0,.16), rgba(0,0,0,0) 60%),radial-gradient(36% 44% at 10% -10%, rgba(0,224,255,.12), rgba(0,0,0,0) 60%)}

/* Section heading underline */
.section-head h2{position:relative; z-index:1; text-shadow:0 2px 22px rgba(0,0,0,.35)}
.section-head h2::after{content:""; display:block; margin-top:8px; width:72px; height:3px; background:linear-gradient(90deg, var(--accent), rgba(0,224,255,0.6)); border-radius:3px}

/* Responsive */
@media (max-width: 960px){
  .menu-toggle{display:inline-flex; order:3; margin-left:auto}
  .links{display:none}
  .feature-grid{grid-template-columns:repeat(2, 1fr)}
  .banner-inner{flex-direction:column; align-items:flex-start}
  .actions{display:none}
}
@media (max-width: 560px){
  .feature-grid{grid-template-columns:1fr}
  .card{padding:16px}
  .card h3{font-size:20px}
  .card p{font-size:14px}
  .card .card-meta{position:static; margin:0 0 10px}
  .card .card-meta .tag,.card .card-meta .chip{padding:3px 8px; font-size:11px}
  .card-xl{min-height:220px; background:linear-gradient(0deg, rgba(0,0,0,.35), rgba(0,0,0,0)),linear-gradient(135deg, rgba(0,224,255,.16), rgba(0,0,0,0) 40%),radial-gradient(80% 120% at 110% -10%, rgba(255,106,0,.2), rgba(0,0,0,0) 50%),var(--panel-2)}
}
</style>
  </head>
  <body>
    <header class="nav">
      <div class="container">
        <button class="menu-toggle" aria-expanded="false" aria-label="Open menu">☰</button>
        <div class="brand">
          <svg class="logo-icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="7" width="18" height="10" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="1.8"/>
            <path d="M7 10v4M5 12h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <circle cx="16" cy="11" r="1.3" fill="currentColor"/>
            <circle cx="18.5" cy="13.5" r="1.3" fill="currentColor"/>
          </svg>
          <span>GAMEPAD</span>
        </div>
        <nav class="links">
          <a href="#games">Games</a>
          <a href="/tokens">Earnings</a>
          <a href="/marketplace">Marketplace</a>
        </nav>
        <div class="actions">
          <a class="btn btn-ghost" href="/profile">My Profile</a>
        </div>
      </div>
    </header>

    <!-- Mobile overlay and menu -->
    <div class="mobile-scrim" id="mobileScrim" aria-hidden="true"></div>
    <nav class="mobile-menu" id="mobileMenu" aria-label="Mobile">
      <div class="container mm-inner">
        <a href="#games">Games</a>
        <a href="/tokens">Earnings</a>
        <a href="/marketplace">Marketplace</a>
        <div class="mm-actions">
          <a class="btn btn-ghost" href="/profile">My Profile</a>
        </div>
      </div>
    </nav>

    <main>
      <section class="hero">
        <div class="container hero-inner">
          <div class="pill">Next‑Gen Performance Platform</div>
          <h1 class="headline">
            <span class="dim typeword">PLAY </span>
            <span class="dim typeword">EARN </span>
            <span class="accent typeword">EXCHANGE</span>
          </h1>
          <p class="subtitle">
            Level up your gaming experience. Compete in high‑stakes online matches to earn exclusive
            <strong>GPC coins</strong> and <strong>Gamepad coins</strong> while climbing the global ranks.
          </p>
          <div class="cta">
            <a class="btn btn-primary" href="#games">Start Earning Now</a>
          </div>
        </div>
      </section>

      <section id="games" class="section choose">
        <div class="container section-head">
          <h2>Choose Your Battle</h2>
        </div>

        <div class="container">
          <div class="slider" id="gameSlider">
            <div class="slider-viewport">
              <div class="slider-track" id="battles">
                <div class="slide" data-key="candy-swipe">
                  <article class="card card-xl">
                    <img class="card-bg" loading="lazy" alt="Candy match-3 puzzle" src="/assets/media/images/gpad/candy/candy-swipe.png" onerror="this.style.display='none'" />
                    <div class="card-meta">
                      <span class="tag">New</span>
                      <span class="chip">Arcade</span>
                    </div>
                    <h3>Candy Swipe</h3>
                    <p>Match sweets, chain combos, and rack up massive scores.</p>
                    <a href="/games?game=colorSwipe" class="btn btn-primary sm">Play Now</a>
                  </article>
                </div>
                <div class="slide" data-key="space-shooter">
                  <article class="card card-tall">
                    <img class="card-bg" loading="lazy" alt="Space shooter arcade" src="/assets/media/images/gpad/space-shooter/space-shooter.png" onerror="this.style.display='none'" />
                    <h3>Space Shooter</h3>
                    <p>Blast waves of enemies and dodge meteors among the stars.</p>
                    <a href="/games?game=spaceShooter" class="btn btn-primary sm">Play Now</a>
                  </article>
                </div>
              </div>
            </div>
            <button class="slider-btn prev" type="button" aria-label="Previous">‹</button>
            <button class="slider-btn next" type="button" aria-label="Next">›</button>
            <div class="slider-dots" id="sliderDots" aria-label="Slides navigation"></div>
          </div>
        </div>
      </section>

      <section id="earnings" class="section value">
        <div class="container">
          <h2>The Value of <span class="accent">GPC Coins</span></h2>
          <p class="muted">Built for players. Earning isn't just a feature; it's the core engine of the Gamepad experience.</p>

          <div class="feature-grid">
            <div class="feature">
              <div class="ico">⚡</div>
              <h4>Instant Rewards</h4>
              <p>Win a match, get paid. No waiting for weekly payouts.</p>
            </div>
            <div class="feature">
              <div class="ico">🪙</div>
              <h4>Universal Utility</h4>
              <p>Use coins across games, buy skins, enter tournaments, swap for Gamepad offers.</p>
            </div>
            <div class="feature">
              <div class="ico">📈</div>
              <h4>Market Growth</h4>
              <p>Earn from performance. Join the economy and boost your collector's worth.</p>
            </div>
            <div class="feature">
              <div class="ico">🔐</div>
              <h4>Secure Assets</h4>
              <p>Military‑grade security protects your winnings and identity.</p>
            </div>
          </div>
        </div>
      </section>

      <section id="get-started" class="section banner">
        <div class="container banner-inner">
          <div>
            <div class="eyebrow">Ready to climb?</div>
            <p>Compete with thousands already earning every day. Don't get left behind.</p>
          </div>
          <a class="btn btn-dark" href="#games">Select Game to Play</a>
        </div>
      </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.clouds.min.js"></script>
    <script>
      (function(){
        const body = document.body;
        const btn = document.querySelector('.menu-toggle');
        const scrim = document.getElementById('mobileScrim');
        const close = ()=>{ body.classList.remove('menu-open'); btn?.setAttribute('aria-expanded','false'); };
        const toggle = ()=>{ const open = body.classList.toggle('menu-open'); btn?.setAttribute('aria-expanded', open ? 'true':'false'); };
        btn?.addEventListener('click', toggle);
        scrim?.addEventListener('click', close);
        window.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
        document.querySelectorAll('.mobile-menu a, .mobile-menu .btn').forEach(el=> el.addEventListener('click', close));

        // Slider
        const track = document.getElementById('battles');
        const slides = [...track.querySelectorAll('.slide')];
        const dotsWrap = document.getElementById('sliderDots');
        const btnPrev = document.querySelector('#gameSlider .prev');
        const btnNext = document.querySelector('#gameSlider .next');
        let i = 0;
        const renderDots = ()=>{
          dotsWrap.innerHTML = '';
          slides.forEach((_, idx)=>{
            const b = document.createElement('button');
            b.type = 'button';
            b.setAttribute('aria-label', 'Go to slide ' + (idx+1));
            if(idx===i) b.setAttribute('aria-current','true');
            b.addEventListener('click', ()=>{ i = idx; update(); });
            dotsWrap.appendChild(b);
          });
        };
        const update = ()=>{
          track.style.transform = 'translateX(-' + (i*100) + '%)';
          [...dotsWrap.children].forEach((d, idx)=> d.toggleAttribute('aria-current', idx===i));
        };
        renderDots();
        update();
        btnPrev?.addEventListener('click', ()=>{ i = (i - 1 + slides.length) % slides.length; update(); });
        btnNext?.addEventListener('click', ()=>{ i = (i + 1) % slides.length; update(); });
        window.addEventListener('keydown', (e)=>{
          if(e.key==='ArrowLeft') { i = (i - 1 + slides.length) % slides.length; update(); }
          if(e.key==='ArrowRight'){ i = (i + 1) % slides.length; update(); }
        });

        // Headline typewriter
        const words = [...document.querySelectorAll('.headline .typeword')];
        if(words.length){
          const TYPE_PER_CHAR = 120;
          const PAUSE_BETWEEN = 350;
          const PAUSE_RESET = 1600;
          const typeWord = (idx)=>{
            if(idx >= words.length){
              setTimeout(()=>{
                words.forEach(w=>{ w.style.width = '0'; w.classList.remove('typing'); w.style.removeProperty('--chars'); w.style.removeProperty('--type-speed'); });
                setTimeout(()=> typeWord(0), 300);
              }, PAUSE_RESET);
              return;
            }
            const el = words[idx];
            const len = (el.textContent || '').length;
            const dur = Math.max(600, len * TYPE_PER_CHAR);
            words.forEach(w=>{ w.classList.remove('typing'); });
            el.style.setProperty('--chars', String(len));
            el.style.setProperty('--type-speed', dur + 'ms');
            el.classList.add('typing');
            setTimeout(()=>{
              el.style.width = len + 'ch';
              el.classList.remove('typing');
              setTimeout(()=> typeWord(idx + 1), PAUSE_BETWEEN);
            }, dur);
          };
          typeWord(0);
        }

        // Vanta clouds background
        if (window.VANTA && VANTA.CLOUDS) {
          try {
            window.vantaEffect = VANTA.CLOUDS({
              el: document.querySelector('.hero'),
              mouseControls: true,
              touchControls: true,
              gyroControls: false,
              minHeight: 200.00,
              minWidth: 200.00,
              skyColor: 0x0d0f10,
              cloudColor: 0x2a2f36,
              cloudShadowColor: 0x0a0c0d,
              sunColor: 0xff6a00,
              sunGlareColor: 0xff6a00,
              sunlightIntensity: 0.2,
              speed: 0.6
            });
          } catch (e) {}
        }
      })();
    </script>
  </body>
</html>
