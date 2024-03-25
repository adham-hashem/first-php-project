<?php
session_start();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

$type = $_SESSION['type'];
$nationalID = $_SESSION['nationalID'];

function validate_username($username) {
    return preg_match("/^[a-zA-Z0-9_]+(\s+[a-zA-Z0-9_]+)*$/", $username);
}

function validate_password($password) {
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s])[^\s]{8,}$/", $password);
}

$newUsername = $password = "";
$newUsernameErr = $passwordErr = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {

        if (empty($_POST['newUsername'])) {
            $newUsernameErr = "New username is required";
        } else {
            $newUsername = test_input($_POST['newUsername']);

            if (!validate_username($newUsername)) {
                $newUsernameErr = "Invalid username format";
            }
        }

        if (empty($_POST['password'])) {
            $passwordErr = "password is required";
        } else {
            $password = test_input($_POST['password']);

            if (!validate_password($password)) {
                $passwordErr = "Invalid password format";
            }
        }

        if ($newUsernameErr === "" && $passwordErr === "") {
            include ("config/config.php");

            $sql = "SELECT password FROM " .
            (($type==="Admin") ? "admins" :
            (($type==="User") ? "users" :
            (($type==="Teacher") ? "teachers" :
            (($type==="Student") ? "students" : ""))))
            . " WHERE national_id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nationalID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                $passwordFromDB = $row['password'];

                if (password_verify($password, $passwordFromDB)) {
                    $sql = "UPDATE " .
                    (($d==="Admin") ? "admins" :
                    (($d==="User") ? "users" :
                    (($type==="Teacher") ? "teachers" :
                    (($type==="Student") ? "students" : ""))))
                    . " SET username=? WHERE national_id=?";
        
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $newUsername, $nationalID);
        
                    if ($stmt->execute()) {
        
                    } else {
                        echo "Error updating username: " . $conn->error;
                    }
                } else {
                    $passwordErr = "Invalid Username or Password!";
                }
            }

        }
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <title>Change Username</title>
</head>
<body>
    <!-- Nav Section Start -->
    <?php include("include/navbar.php"); ?>
    <hr />
    <!-- Nav Section End -->


    <!-- Change Username Section -->
    <div class="change_username_section">
        <div class="d-flex justify-content-center">
            <div class="col-10">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-circle-user user_register_icon"></i>
                        <label for="newUsername" class="form-label user_register_title">New Username</label>
                        <input class="form-control" type="text" id="newUsername" name="newUsername" placeholder="John Doe" required />
                        <?php echo $newUsernameErr; ?>  
                    </div>
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-key user_register_icon"></i>
                        <label for="password" class="form-label user_register_title">Current Password</label>
                        <input class="form-control" type="password" id="password" name="password" placeholder="********" required />
                        <?php echo $passwordErr; ?>
                    </div>
                    <button name="submit" class="submit" type="submit">Submit</button>
                </form>
            </div>
        </div>

    </div>


    <!-- Footer Section Start -->
    <hr />
    <?php include("include/footer.php"); ?>
    <!-- Footer Section End -->

    <script>
        let profileButton = document.getElementById("profile");

        profileButton.addEventListener("click", () => {
            <?php if(isset($_SESSION["type"])) { ?>
                <?php if($_SESSION["type"] === "Teacher") { ?>
                    window.location.href = "teacher.php";
                <?php } else if ($_SESSION["type"] === "Student") { ?>
                    window.location.href = "student.php";
                <?php } else if ($_SESSION["type"] === "Admin") { ?>
                    window.location.href = "admin.php";
                <?php } ?>
            <?php } ?>
        });

        document.addEventListener("DOMContentLoaded", function() {
            let logoutButton  = document.getElementById("logout");

            if (logoutButton) {
                logoutButton.addEventListener("click", () => {
                    window.location.href = "logout.php";
                });
            }
        });

    </script>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/1f3d09c930.js" crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>

