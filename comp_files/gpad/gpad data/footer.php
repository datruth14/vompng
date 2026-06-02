<style>
    /* Reset default margin and padding */
    * {
        margin: 0;
        padding: 0;
    }

    a {
        color: #fff; /* Set default text color to white for contrast */
        text-align: center;
    }

    a:hover {
        color: #000; /* Hover effect for readability */
    }

    /* Mobile navigation bar container */
    .mobile-navbar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 1000;
        transition: bottom 0.2s ease;
    }

    /* Style for navigation links with unique colors */
    .mobile-navbar a {
        flex: 1;
        margin-top: 0.5em;
        text-decoration: none;
        padding: 10px 5px;
        white-space: nowrap;
    }

    /* Set unique background colors for each navigation item */
    .mobile-navbar a:nth-child(1) { color:red; } /* Home - Green */
    .mobile-navbar a:nth-child(2) {color:yellow;} /* Challenge - Orange */
    .mobile-navbar a:nth-child(3) {  color:skyblue;} /* Freebies - Blue */
    .mobile-navbar a:nth-child(4) {color:pink; } /* Invites - Purple */
    .mobile-navbar a:nth-child(5) {color:navy;} /* Wallet - Red */

    /* Smaller font for <small> text, no wrapping */
    .mobile-navbar small {
        font-size: 1em;
        white-space: nowrap;
    }

    /* Adjust font size for smaller screens */
    @media (max-width: 480px) {
        .mobile-navbar a {
            font-size: 1em;
            padding: 10px 0px;
        }

        .mobile-navbar small {
            font-size: 1em;
        }
    }
</style>

<!-- Mobile navigation bar -->
<nav class="mobile-navbar">
    <a href="?s=gpad" class='<?php if (!isset($_GET["s"])) {echo "navItemsHome animate__animated animate__slideInUp animate__faster";} else {echo "navItems";}?>'>
    <i class="fas fa-sign-out-alt"></i>
    <br>
        <small> Exit </small>
    </a>




    <a href="?s=wallet" class='<?php if (isset($_GET["s"]) and $_GET["s"] == "wallet") {echo "navItemsWallet animate__animated animate__slideInUp animate__faster";} else {echo "navItems";}?>'>
        <i class="fas fa-wallet"></i><br>
        <small>Wallet</small> 
        
        <a href="?s=about&&#gpad" class='<?php if (isset($_GET["s"]) and $_GET["s"] == "invite") {echo "navItemsWallet animate__animated animate__slideInUp animate__faster";} else {echo "navItems";}?>'>
        <i class="fas fa-info"></i><br>
        <small> About </small>
    </a>
    </a>
</nav>
