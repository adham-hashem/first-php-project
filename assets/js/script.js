
let registerButton = document.getElementsByClassName("register")[0];
let loginButton = document.getElementsByClassName("login")[0];

document.addEventListener("DOMContentLoaded", () => {
    registerButton.addEventListener("click", () => {
        window.location.href = "register.php";
    });
    
    loginButton.addEventListener("click", () => {
        window.location.href = "login.php";
    });
});

// ==============================================

document.addEventListener("DOMContentLoaded", () => {
    let menuButton = document.getElementsByTagName("i")[0];
    let menuBlock = document.getElementsByClassName("menu_block")[0];

    menuButton.addEventListener("click", () => {
        if (menuBlock.style.display === "none" || menuBlock.style.display === "") {
            // menuBlock.style.display = "flex";
            menuBlock.classList.toggle("show");
        }
    });
});


// ============================================== 

// It does not work 

// document.addEventListener("DOMContentLoaded", function() {
//     let logoutButton  = document.getElementById("logout");

//     if (logoutButton) {
//         logoutButton.addEventListener("click", () => {
//             window.location.href = "logout.php";
//         });
//     }

// });