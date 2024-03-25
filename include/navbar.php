<?php

if (isset($_SESSION['user_id'])) {
        // include("include/navbar_logout.php");
        echo '
        <nav>
            <div class="container-fluid">
                <div class="brand_name">
                    <a href="index.php"><span class="brand_name_text">ADHAM</span></a>
                </div>
                <div class="logout_box">
                    <button class="profile" id="profile">My profile</button>
                    <button class="logout" id="logout">Logout</button>
                </div>
                <div class="menu_icon">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </nav>
        <div class="menu_block">
            <a href="profile.php?type=' . $_SESSION["type"] . '"><i class="fa-solid fa-user"></i><span>Profile</span></a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
        </div>';
        
    } else {
        // include("include/navbar.php");
        echo '
        <nav>
            <div class="container-fluid">
                <div class="brand_name">
                    <a href="index.php"><span class="brand_name_text">ADHAM</span></a>
                </div>
                <div class="register_login">
                    <button class="register"id="register">Register</button>
                    <button class="login" id="login">Login</button>
                </div>
                <div class="menu_icon">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </nav>
        <div class="menu_block">
            <a href="register.php"><i class="fa-solid fa-user-plus"></i><span>Register</span></a>
            <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i><span>Login</span></a>
        </div> ';
    }

?>