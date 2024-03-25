<?php
session_start();

// we should use one condition here in all php files to redirect the user to the suitable page

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['type'] === 'Admin') {
        header("Location: admin.php");
        exit;
    } else if ($_SESSION['type'] === 'Student') {
        header("Location: student.php");
        exit;
    } else if ($_SESSION['type'] === 'Teacher'){
        header("Location: teacher.php");
        exit;
    } else if ($_SESSION['type'] === 'User') else {
        header("Location: user.php");
        exit;
    }
}

$nationalID = $password = "";
$nationalIDErr = $passwordErr = "";
$submitErr = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function validate_nationalID($username) {
    return preg_match("/[0-9]/", $username);
}

function validate_password($password) {
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s])[^\s]{8,}$/", $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        if (empty($_POST['nationalID'])) {
            $nationalIDErr = "National ID is required";
        } else {
            $nationalID = test_input($_POST['nationalID']);

            if (!validate_nationalID($nationalID)) {
                $nationalIDErr = "Invalid National ID format";
            }
        }

        if (empty($_POST['password'])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST['password']);

            if (!validate_password($password)) {
                $passwordErr = "Invalid password format";
            }
        }

        
        if ($nationalIDErr === "" && $passwordErr === "") {
            echo "go";
            // $password = password_hash($password, PASSWORD_DEFAULT);
            include("config/config.php");

            $sql =  "SELECT national_id FROM admins WHERE national_id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nationalID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "goo";
                $sql = "SELECT id, password FROM admins WHERE national_id=?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $nationalID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    $user_id = $row['id'];
                    $correctPassword = $row['password'];
                    echo $password . "<br>";
                    echo $correctPassword;
                    
                    if (password_verify($password, $correctPassword)) {
                        echo "goode";
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['type'] = 'Admin';
                        header("Location: admin.php");
                        exit;
                    } else {
                        $submitErr = "Invalid national ID or password";
                    }
                } else {
                    $submitErr = "Invalid national ID or password";
                }
            } else {
                $submitErr = "Invalid national ID or password";
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
    <title>Admin login</title>

</head>
<body>
    <!-- Nav Section Start -->
    <?php include("include/navbar.php"); ?>
    <!-- Nav Section End -->

    <!-- Login Form Section Start -->
    <div class="admin_login_section">
        <div class="d-flex justify-content-center">
            <div class="col-10">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group mb-3">
                        <label for="nationalID" class="form-label">National ID</label>
                        <input class="form-control" type="text" id="nationalID" name="nationalID" placeholder="35005161101088" required />
                        <?php echo $nationalIDErr; ?>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input class="form-control" type="password" id="password" name="password" placeholder="********" required />
                        <?php echo $passwordErr; ?>
                        <?php echo $submitErr; ?>
                    </div>
                    <button class="submit" type="submit" name="submit">Submit</button>
                    <br>
                    <?php echo $submitErr; ?>
                </form>
            </div>
        </div>
    </div>
    <!-- Login Form Section End -->

    <!-- Footer Section Start -->
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