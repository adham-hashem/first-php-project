<?php

session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['type'] === 'Teacher') {
        header("Location: teacher.php");
        exit;
    } else if ($_SESSION['type'] === 'Student') {
        header("Location: student.php");
        exit;
    } else if ($_SESSION['type'] === 'Admin') {
        header("Location: admin.php");
        exit;
    } else if ($_SESSION['type'] === 'User') {
        header("Location: user.php");
        exit;
    }
}

include ("allowedNationalIDs.php");

$teacherOrStudent = $nationalID = $username = $password = $confirmPassword = "";
$teacherOrStudentErr = $nationalIDErr = $usernameErr = $passwordErr = $confirmPasswordErr = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function validate_teacherOrStudent($teacherOrStudent) {
    return preg_match("/^[a-zA-Z]+$/", $teacherOrStudent);
}

function validate_nationalID($nationalID) {
    return preg_match("/[0-9]+/", $nationalID); // here I added +
}

function validate_username($username) {
    return preg_match("/^[a-zA-Z0-9_]+(\s+[a-zA-Z0-9_]+)*$/", $username);
}

function validate_password($password) {
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s])[^\s]{8,}$/", $password);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {

        if (empty($_POST['teacherOrStudent'])) {
            $teacherOrStudentErr = "Teacher or Student is required";
        } else {
            $teacherOrStudent = test_input($_POST['teacherOrStudent']);

            if (!validate_teacherOrStudent($teacherOrStudent)) {
                $teacherOrStudentErr = "Teacher or Student is required";
            }
        }

        if (empty($_POST['nationalID'])) {
            $nationalIDErr = "national ID is required";
        } else {
            $nationalID = test_input($_POST['nationalID']);

            if (!validate_nationalID($nationalID)) {
                $nationalIDErr = "Invalid national ID format";
            }
        }

        if (empty($_POST['username'])) {
            $usernameErr = "username is required";
        } else {
            $username = test_input($_POST['username']);

            if (!validate_username($username)) {
                $usernameErr = "Invalid username format";
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

        if (empty($_POST['confirmPassword'])) {
            $confirmPasswordErr = "confirm password is required";
        } else {
            $confirmPassword = test_input($_POST['confirmPassword']);

            if (!validate_password($confirmPassword)) {
                $confirmPasswordErr = "Invalid confirm password format";
            }
        }

        if ($password !== $confirmPassword) {
            $confirmPasswordErr = "Passsword not match!";
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        // Vertifing that the national ID is allowed
        if (!in_array($nationalID, 
        ($teacherOrStudent === "Admin") ? $allowedAdmins :
        (($teacherOrStudent === "User") ? $allowedUsers :
        (($teacherOrStudent === "Teacher") ? $allowedTeachers :
        (($teacherOrStudent === "Student") ? $allowedStudents : ""
        ))))) {
            $nationalIDErr = "This National ID is not allowed!";
        }

        include("config/config.php");

        // Vertifing that national ID is not attached to another account
        $sql = "SELECT id FROM " .
        (($teacherOrStudent === "Admin") ? "admins" :
        (($teacherOrStudent === "User") ? "users" :
        (($teacherOrStudent === "Teacher") ? "teachers" :
        (($teacherOrStudent === "Student") ? "students" : ""
        ))))
        . " WHERE national_id=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nationalID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $nationalIDErr = "This National ID is attached to another account";
        }


        if ($nationalIDErr === "" && $usernameErr === "" && $passwordErr === "" && $confirmPasswordErr === "") {

            $sql = "INSERT INTO " .
            (($teacherOrStudent === "Admin") ? "admins" :
            (($teacherOrStudent === "User") ? "users" :
            (($teacherOrStudent === "Teacher") ? "teachers" :
            (($teacherOrStudent === "Student") ? "students" : ""
            ))))
            . " (national_id, username, password) VALUES (?, ?, ?)";

            // if ($teacherOrStudent === "Teacher") {
            //     $sql = "INSERT INTO teachers (national_id, username, password)
            //     VALUES (?, ?, ?);";
            // } else if ($teacherOrStudent === "Student") {
            //     $sql = "INSERT INTO students (national_id, username, password)
            //     VALUES (?, ?, ?);";
            // } else if ($teacherOrStudent === "Admin") {
            //     $sql = "INSERT INTO admins (national_id, username, password)
            //     VALUES (?, ?, ?);";
            // } else if ($teacherOrStudent === "User") {
            //     $sql = "INSERT INTO users (national_id, username, password)
            //     VALUES (?, ?, ?);";
            // } else {
            //     echo "ERROR: " . $conn->error; 
            // }

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nationalID, $username, $password);

            if ($stmt->execute()) {
                // echo "<script>alert(`New account created successfully!`);</script>";
                header("Location: login.php");
                exit;
            } else {
                echo "Error: " . $sql . "<br />" . $conn->error;
            }

            $stmt->close();
            $conn->close();

        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <title>Register</title>
</head>
<body>
    <!-- Navbar Section Start -->
    <?php include("include/navbar.php"); ?>
    <!-- Navbar Section End -->

    <div class="landing_section wow bounceIn" data-wow-offset="50" data-wow-duration="2s">
        <div class="d-flex justify-content-center">
            <div class="col-10">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-user user_register_icon page_color"></i>
                        <label for="teacherOrStudent" class="form-label user_register_title page_color">Admin | user | teacher | user</label>
                        <select name="teacherOrStudent" id="teacherOrStudent" class="form-control">
                            <option>Admin</option>
                            <option>User</option>
                            <option>Teacher</option>
                            <option>Student</option>
                        </select>
                        <span class="error"><php echo $teacherOrStudentErr; ?></span>
                    </div>
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-id-card user_register_icon page_color"></i>
                        <label for="nationalID" class="form-label user_register_title page_color">National ID</label>
                        <input class="form-control" type="number" id="nationalID" name="nationalID" placeholder="30205161101088" required />
                        <span class="error"><?php echo $nationalIDErr; ?></span>
                    </div>
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-circle-user user_register_icon page_color"></i>
                        <label for="username" class="form-label user_register_title page_color">Username</label>
                        <input class="form-control" type="text" id="username" name="username" placeholder="John Doe" required />
                        <span class="error"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-key user_register_icon page_color"></i>
                        <label for="password" class="form-label user_register_title page_color">Password</label>
                        <input class="form-control" type="password" id="password" name="password" placeholder="********" required />
                        <span class="error"><?php echo $passwordErr; ?></span>
                    </div>
                    <div class="form-group mb-4">
                        <i class="fa-solid fa-key user_register_icon page_color"></i>
                        <label for="confirmPassword" class="form-label user_register_title page_color">Confirm Password</label>
                        <input class="form-control" type="password" id="confirmPassword" name="confirmPassword" placeholder="********" required />
                        <span class="error"><?php echo $confirmPasswordErr; ?></span>
                    </div>
                    <button name="submit" class="submit" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <?php include_once("./include/footer.php"); ?>

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
    <script src="assets/js/wow.min.js"></script>
    <script>new WOW().init();

</body>
</html>
