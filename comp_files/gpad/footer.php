<style>
    #gpad-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        display: flex;
        justify-content: space-around;
        background: rgba(0,0,0,0.85);
        backdrop-filter: blur(5px);
        padding: 8px 0 12px 0;
        font-family: Arial, sans-serif;
        font-size: 0.75rem;
    }
    #gpad-footer a {
        color: #fff;
        text-decoration: none;
        text-align: center;
        flex: 1;
        padding: 4px 0;
    }
    #gpad-footer a:hover { filter: brightness(1.3); }
    #gpad-footer a:nth-child(1) { color: #ff6b6b; }
    #gpad-footer a:nth-child(2) { color: #ffd93d; }
    #gpad-footer a:nth-child(3) { color: #6bcbff; }
    #gpad-footer small { display: block; margin-top: 2px; font-size: 0.65rem; }
</style>
<nav id="gpad-footer">
    <a href="/games"><i class="fas fa-sign-out-alt"></i><small>Exit</small></a>
    <a href="/tokens"><i class="fas fa-wallet"></i><small>Wallet</small></a>
    <a href="/profile"><i class="fas fa-user"></i><small>Profile</small></a>
</nav>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
