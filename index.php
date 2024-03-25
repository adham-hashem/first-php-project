<?php
session_start();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function validate_username($username) {
    return preg_match("/^[a-zA-Z0-9_]+(\s+[a-zA-Z0-9_]+)*$/", $username);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_feedback($feedback) {
    return preg_match("/^[a-zA-Z\s.]+$/", $feedback);
}

$username = $email = $feedback = "";
$usernameErr = $emailErr = $feedbackErr = $submitErr = "";    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {

        if (empty($_POST['username'])) {
            $usernameErr = "Username is required!";
        } else {
            $username = test_input($_POST['username']);

            if (!validate_username($username)) {
                $usernameErr = "Invalid Username format!";
            }
        }

        if (empty($_POST['email'])) {
            $emailErr = "Email is required!";
        } else {
            $email = test_input($_POST['email']);

            if (!validate_email($email)) {
                $emailErr = "Invalid Email format!";
            }
        }

        if (empty($_POST['feedback'])) {
            $feedbackErr = "Feedback is required!";
        } else {
            $feedback = $_POST['feedback'];

            if (!validate_feedback($feedback)) {
                $feedbackErr = "Invalid Feedback format!";
            }
        }

        
        if ($usernameErr === "" && $emailErr === "" && $feedbackErr === "") {
            include "config/config.php";

            $sql = "INSERT INTO feedback (username, email, feedback_content)
            VALUES (?, ?, ?);";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $feedback);

            if ($stmt->execute()) {
                echo "<script>alert(`Feedback submitted successfully!`);</script>";
            } else {
                $error = $conn->error;
                $submitErr = "Error: $error";
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
    <title>DEMA School</title>

</head>
<body>
    <!-- Navbar Section Start -->
    <?php include("include/navbar.php"); ?>
    <!-- Navbar Section End -->

    <!-- Landing Section Start -->
    <section class="landing_section">
        <div class="container-fluid">
            <div class="title">
                <h1>&lt;<span>ADHAM School</span>&gt;</h1>
            </div>
            <div class="row">
                <div class="school_info col-md-6">
                    <h1 style="color: rgb(87, 145, 30);">Welcome to ADHAM School</h1>
                    <p><span class="h">W</span>e are here for you.</p>
                    <p><span class="h">W</span>e care about education and health.</p>
                    <p><span class="h">W</span>e teach using AI and modern technologies.</p>
                    <p><span class="h">W</span>e have clever students and efficient teachers.</p>
                    <p><span class="h">W</span>e hope to join to us and achieve the impossible.</p>
                </div>

                <div class="col-md-6">
                    <div class="school_img">
                        <img src="./images/school3.avif" alt="school image" title="school image">
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Landing Section End -->
    
    <!-- School Section Start -->
    <section class="School_section wow slideInLeft" data-wow-offset="100" data-wow-duration="2s">
        <div class="container">
            <h2>Our School...</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-hand-sparkles"></i>
                        <h3>Clean School</h3>
                        <p>Our school is beautiful and we maintain clear cleanliness</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-chalkboard"></i>
                        <h3>High Expectations</h3>
                        <p>Our Teachers and staff believe that all students can meet high standards.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-laptop-file"></i>
                        <h3>Good Curriculum</h3>
                        <p>The planned and actual curricula are aligned with the essential learning.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Students Section End -->

    <!-- Teachers Section Start -->
    <section class="teachers_section wow bounceIn" data-wow-offset="70" data-wow-duration="2s">
        <div class="container">
            <h2>Our Teachers...</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <h3>Efficient Teachers</h3>
                        <p>Our teachers are experts in teaching, the studens understand them efficiently</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-star"></i>
                        <h3>Modern Methods</h3>
                        <p>Our tearcher use the newest methods to teach the stuents</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <h3>Teaching Using AI</h3>
                        <p>Our teachers use AI to demonstrate the subject to efficiently teaching</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Learning Section End -->

    <!-- Students Section Start -->
    <section class="students_section wow slideInDown" data-wow-offset="70" data-wow-duration="2s">
        <div class="container">
            <h2>Our Students...</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-user"></i>
                        <h3>Clever Students</h3>
                        <p>Our sutdents are good and subscribe in the most important competitions</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-heart"></i>
                        <h3>Love Learning</h3>
                        <p>Our students love learning and acquire new knowledge to become better</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="learning_box">
                        <i class="fa-solid fa-plus"></i>
                        <h3>More Knowledge</h3>
                        <p>Our students study many of subjects to aquire new knowledge</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Students Section End -->

    <!-- Feedback Section Start -->
    <section class="feedback_section wow bounceIn" data-wow-offset="70" data-wow-duration="2s">
        <h2>Feedback</h2>
        <div class="col-10">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group mb-3">
                    <i class="fa-solid fa-user"></i>
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="John Doe" required />
                    <?php echo $usernameErr; ?>
                </div>
                <div class="form-group mb-3">
                    <i class="fa-solid fa-envelope"></i>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Example@you.me" required />
                    <?php echo $emailErr; ?>
                </div>
                
                <div class="form-group mb-3">
                    <i class="fa-solid fa-comments"></i>
                    <label for="feedback">Feedback</label>
                    <textarea name="feedback" class="form-control" id="feedback" placeholder="Uppercase letters, lowercase letters, dot and spaces are allowed only!" required></textarea>
                    <?php echo $feedbackErr; ?>
                </div>
                <button class="btn" type="submit" name="submit">Submit</button>
            </form>
            <?php echo $submitErr; ?>
        </div>
    </section>
    <!-- Feedback Section End -->

    <!-- Footer Section Start -->
    <footer>
        <p>All rights reserved &copy;</p>
    </footer>
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
</script>

</body>
</html>