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

    }
}

include ("config/config.php");

$user_id = $_SESSION['user_id'];
$userNationalID = $username = "";
$type = $_SESSION['type'];

$sql = "SELECT * FROM users WHERE id=$user_id";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userNationalID = $row['national_id'];
        $username = $row['username'];
    }
}

// =================

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function validate_nationalID ($nationalID) {
    return preg_match('/^[0-9]+$/', $nationalID);
}

function validate_degree ($degree) {
    return preg_match('/^[0-9]+$/', $degree);
}


$studentNationalID = $arabicDegree = $englishDegree = $mathematicsDegree = $chemistryDegree = $physicsDegree = "";
$studentNationalIDErr = $arabicDegreeErr = $englishDegreeErr = $mathematicsDegreeErr = $chemistryDegreeErr = $physicsDegreeErr = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        if (empty($_POST['studentNationalID'])) {
            $studentNationalIDErr = "Student national ID is required";
        } else {
            $studentNationalID = test_input($_POST['studentNationalID']);

            if (!validate_nationalID($studentNationalID)) {
                $studentNationalIDErr = "Invalid national ID format";
            }
        }

        //
        $arabicDegree = test_input($_POST['arabicDegree']);

        if (!validate_degree($arabicDegree)) {
            $arabicDegreeErr = "Invalid degree format";
        }

        //
        $englishDegree = test_input($_POST['englishDegree']);

        if (!validate_degree($englishDegree)) {
            $englishDegreeErr = "Invalid degree format";
        }

        //
        $mathematicsDegree = test_input($_POST['mathematicsDegree']);

        if (!validate_degree($mathematicsDegree)) {
            $mathematicsDegreeErr = "Invalid degree format";
        }

        //
        $chemistryDegree = test_input($_POST['chemistryDegree']);

        if (!validate_degree($chemistryDegree)) {
            $chemistryDegreeErr = "Invalid degree format";
        }

        //
        $physicsDegree = test_input($_POST['physicsDegree']);

        if (!validate_degree($physicsDegree)) {
            $physicsDegreeErr = "Invalid degree format";
        }

        // =============

        if ($studentNationalIDErr === "" && $arabicDegreeErr === "" && $englishDegreeErr === "" && $mathematicsDegreeErr === "" && $chemistryDegreeErr === "" && $physicsDegreeErr === "") {
            $sql = "INSERT INTO degrees (user_national_id, student_national_id, arabic, english, mathematics, chemistry, physics)
            VALUES (?, ?, ?, ?, ?, ?, ?);";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiiiii", $userNationalID, $studentNationalID, $arabicDegree, $englishDegree, $mathematicsDegree, $chemistryDegree, $physicsDegree);
            
            if ($stmt->execute()) {
                echo "<script>alert(`Student degrees inserted successfully`);</script>";
            } else {
                echo "Error inserting student degrees: " . $conn->error;
            }

        }


    }
}

// Profile Photo

function uploadFile($file, $uploadDirectory, $allowedTypes, $maxFileSize) {
    $filename = $file["name"];
    $tmpFilePath = $file["tmp_name"];
    $fileType = $file["type"];
    $fileSize = $file["size"];

    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($fileType, $allowedTypes) || !in_array($fileExtension, $allowedTypes)) {
        return "Invalid file type.";
        // echo "Invalid file type.";
    }

    // Check file size
    if ($fileSize > $maxFileSize) {
        return "File is too large.";
        echo "File is too large.";
    }

    $uniqueFilename = uniqid() . '-' . $filename;
    $targetFilePath = $uploadDirectory . $uniqueFilename;

    if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
        return $targetFilePath; // the result we will use
    } else {
        return "Error uploading file.";
    }


    // print_r($file) . "<br>";
    // echo $filename . "<br>";
    // echo $tmpFilePath . "<br>";
    // echo $fileType . "<br>";
    // echo $fileSize . "<br>";
    // echo $fileExtension;
}

$uploadDirectory = "profile photos/";
$allowedTypes = array("image/jpeg", "image/png", "jpg", "jpeg", "png");
$maxFileSize = 10 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePhoto'])) {
    if (isset($_POST['uploadProfilePhoto'])) {
        $filename = uploadFile($_FILES['profilePhoto'], $uploadDirectory, $allowedTypes, $maxFileSize);

        if (is_string($filename)) {
            // echo $filename;
        }

        // Storing the profile photo in photos table
        $sql = "INSERT INTO images (filename, description, national_id)
        VALUES (?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $description = "$username, $nationalID profile photo";
        $stmt->bind_param("sss", $filename, $description, $nationalID);

        if ($stmt->execute()) {

        } else {
            echo "<script>Error in uploading the image!</script>";
        }


        // Updating profile photo data
        $sql = "UPDATE " .
        (($type === "Admin") ? "admins" :
        (($type === "User") ? "users" :
        (($type === "Teacher") ? "teachers" :
        (($type === "Student") ? "students" : ""
        ))))
        . " SET profile_image=? WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $filename, $id);

        if ($stmt->execute()) {

        } else {
            echo "Error in updating profile photo";
        }
    }


}


// Getting profile photo if the user has

$sql = "SELECT profile_image FROM " .
(($type === "Admin") ? "admins" :
(($type === "User") ? "users" :
(($type === "Teacher") ? "teachers" :
(($type === "Student") ? "students" : ""
))))
. " WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

$user_profile_image = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $user_profile_image = $row['profile_image'];
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
    <title>Student</title>

</head>
<body>
    <!-- Nav Section Start -->
    <?php include("include/navbar.php"); ?>
    <hr />
    <!-- Nav Section End -->

    <!-- Student Information Section Start -->
    <section class="user_section d-flex justify-content-center">
        <div class="col-10">
            <h2><span class="page_color">Welcome</span> <?php echo $username; ?></h2>
            <!-- Information -->
            <div class="user_information">
                <i class="fa-solid fa-user user_info_icon page_color"></i>
                <span class="user_info_title page_color">Information</span>
                <p>National ID: <?php echo $userNationalID; ?></p>
                <p>Name: <?php echo $username; ?></p>
                <p>Position: User</p>

                <div class="dropdown">
                    <button class="btn dropdown-toggle manage_account" data-bs-toggle="dropdown">Manage account</button>
                    <ul class="dropdown-menu">
                        <li><a href="change_username.php" class="dropdown-header">Change Username</a></li>
                        <li><a href="change_password.php" class="dropdown-header">Change Password</a></li>
                        <li><a href="delete_account.php" class="dropdown-header">Delete account</a></li>
                    </ul>
                </div>
            </div>
            <hr />
            <!-- Students -->
            <i class="fa-solid fa-users user_info_icon page_color"></i>
            <span class="user_info_title page_color">Students</span>
            <table>
                <tr><th>National ID</th><th>Username</th></tr>

                <?php 

                // $sql = "SELECT teachers.national_id, teachers.username, teacher_ratings.rate, teacher_ratings.opinion
                // FROM teachers
                // JOIN teacher_ratings ON teachers.national_id = teacher_ratings.teacher_national_id";
                $sql = "SELECT * FROM students";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $nationalID = $row['national_id'];
                        $username = $row['username'];

                        echo "<tr><td>$nationalID</td><td>$username</td></tr>";
                    }
                } else {
                    echo "0 results found"  ;
                }

                $conn->close();

                ?>

            </table>
            <hr>
            <!-- Degrees -->
            <i class="fa-solid fa-flask user_info_icon page_color"></i>
            <span class="user_info_title page_color">Degrees</span>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group mb-2">
                    <label for="studentNationalID" class="form-label">Student national ID</label>
                    <input id="studentNationalID" name="studentNationalID" type="text" class="form-control" required />
                    <?php echo $studentNationalIDErr; ?>
                </div>
                <div class="form-group mb-2">
                    <label for="arabicDegree" class="form-label">Arabic degree</label>
                    <input type="number" class="form-control" name="arabicDegree" id="arabicDegree" required />
                </div>
                <div class="form-group mb-2">
                    <label for="englishDegree" class="form-label">English degree</label>
                    <input type="number" class="form-control" name="englishDegree" id="englishDegree" required />
                </div>
                <div class="form-group mb-2">
                    <label for="mathematicsDegree" class="form-label">Mathematics degree</label>
                    <input type="number" class="form-control" name="mathematicsDegree" id="mathematicsDegree" required />
                </div>
                <div class="form-group mb-2">
                    <label for="chemistryDegree" class="form-label">Chemistry degree</label>
                    <input type="number" class="form-control" name="chemistryDegree" id="chemistryDegree" required />
                </div>
                <div class="form-group mb-2">
                    <label for="physicsDegree" class="form-label">Physics degree</label>
                    <input type="number" class="form-control" name="physicsDegree" id="physicsDegree" required />
                </div>
                <button name="submit" class="submit" type="submit">Submit</button>
            </form>
        </div>
    </section>
    <!-- Student Information Section End -->

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