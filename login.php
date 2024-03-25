<?php
session_start();

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
    } else if ($_SESSION['type'] === 'User') {
        header("Location: user.php");
        exit;
    }
}
$teacherOrStudent = $nationalID = $userInputPassowrd = "";
$teacherOrStudentErr = $nationalIDErr = $userInputPasswordErr = "";
$submitErr = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

// echo $nationalID . ' ' . $password . ' ' . $password;
// echo $nationalIDErr . ' ' . $passwordErr . ' ' . $passwordErr;

function validate_teacherOrStudent($teacherOrStudent) {
    return preg_match("/^[a-zA-Z]+$/", $teacherOrStudent);
}

function validate_nationalID($username) {
    return preg_match("/[0-9]/", $username);
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
            $nationalIDErr = "nationalID is required";
        } else {
            $nationalID = test_input($_POST['nationalID']);

            if (!validate_nationalID($nationalID)) {
                $nationalIDErr = "Invalid nationalID format";
            }
        }

        if (empty($_POST['password'])) {
            $userInputPasswordErr = "password is required";
        } else {
            $userInputPassowrd = test_input($_POST['password']);

            if (!validate_password($userInputPassowrd)) {
                $userInputPasswordErr = "Invalid password format";
            }
        }

        
        if ($teacherOrStudentErr === "" && $nationalIDErr === "" && $userInputPasswordErr === "") {

            include("config/config.php");

            // if ($teacherOrStudent === "Teacher") {
            //     $sql = "SELECT national_id FROM teachers WHERE national_id=$nationalID;";
            // } else if ($teacherOrStudent === "Student") {
            //     $sql = "SELECT national_id FROM students WHERE national_id=$nationalID;";
            // }

            $sql = "SELECT national_id FROM " . 
            ($teacherOrStudent === "Admin" ? "admins" :
            ($teacherOrStudent === "User" ? "users" :
            ($teacherOrStudent === "Teacher" ? "teachers" :
            ($teacherOrStudent === "Student" ? "students" : "")))) .
            " WHERE national_id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nationalID);
            $stmt->execute();
            $result = $stmt->get_result();

            // print_r($result);

            if ($result->num_rows > 0) {
                // if ($teacherOrStudent === "Teacher") {
                //     $sql = "SELECT id, password FROM teachers WHERE national_id=$nationalID;";
                // } else if ($teacherOrStudent === "Student") {
                //     $sql = "SELECT id, password FROM students WHERE national_id=$nationalID;";
                // }

                $sql = "SELECT id, password FROM " . 
                ($teacherOrStudent === "Admin" ? "admins" :
                ($teacherOrStudent === "User" ? "users" :
                ($teacherOrStudent === "Teacher" ? "teachers" :
                ($teacherOrStudent === "Student" ? "students" : "")))) .
                " WHERE national_id=?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $nationalID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $user_id = $row["id"];
                        $correctPassword = $row["password"];

                        if (password_verify($userInputPassowrd, $correctPassword)) {
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['nationalID'] = $nationalID;

                            if ($teacherOrStudent === "Teacher") {
                                $_SESSION['type'] = "Teacher";
                                // header("Location: teacher.php");
                                header("Location: index.php");
                                // exit;
                            } else if ($teacherOrStudent === "Student") {
                                $_SESSION['type'] = "Student";
                                // header("Location: student.php");
                                header("Location: index.php");
                                // exit;
                            } else if ($teacherOrStudent === "Admin") {
                                $_SESSION['type'] = "Admin";
                                header("Location: index.php");
                            } else if ($teacherOrStudent === "User") {
                                $_SESSION['type'] = "User";
                                header("Location: index.php");
                            }
                            } else {
                                $submitErr = "National ID or password is invalid";
                            }
                        }
                    }
                // else {
                //     $submitErr = "National ID or password is invalid";
                // }

            } else {
                $submitErr = "National ID or password is invalid";
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
    <link rel="stylesheet" href="assets/css/animate.css" />
    <title>Login</title>
</head>
<body>
    <!-- Navbar Section Start -->
    <?php include_once("./include/navbar.php"); ?>
    <!-- Navbar Section End -->

    <div class="landing_section">
        <div class="d-flex justify-content-center">
            <div class="col-10">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group mb-3">
                        <i class="fa-solid fa-user user_register_icon page_color"></i>
                        <label for="teacherOrStudent" class="form-label user_register_title page_color">Admin | user | teacher | user</label>
                        <select name="teacherOrStudent" id="teacherOrStudent" class="form-control">
                            <option>Admin</option>
                            <option>User</option>
                            <option>Teacher</option>
                            <option>Student</option>
                        </select>
                        <span class="error"><?php echo $teacherOrStudentErr; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <i class="fa-solid fa-id-card user_register_icon page_color"></i>
                        <label for="nationalID" class="form-label user_register_title page_color">National ID</label>
                        <input class="form-control" type="text" id="nationalID" name="nationalID" placeholder="35005161101088" required />
                        <span class="error"><?php echo $nationalIDErr; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <i class="fa-solid fa-key user_register_icon page_color"></i>
                        <label for="password" class="form-label user_register_title page_color">Password</label>
                        <input class="form-control" type="password" id="password" name="password" placeholder="********" required />
                        <span class="error"><?php echo $userInputPasswordErr; ?></span>
                        <span class="error"><?php echo $submitErr; ?></span>
                    </div>
                    <button class="submit" type="submit" name="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Section Start -->
    <?php include_once("./include/footer.php"); ?>
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
                <?php } else if ($_SESSION["type"] === "User") { ?>
                    window.location.href = "user.php";
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