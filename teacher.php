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

    } else if ($_SESSION['type'] === 'User') {
        header("Location: user.php");
        exit;
    }
}

include ("config/config.php");

$user_id = $_SESSION['user_id'];
$nationalID = $username = $rate = $openion = "";
$type = $_SESSION['type'];

$sql = "SELECT * FROM teachers WHERE id=$user_id";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nationalID = $row['national_id'];
        $username = $row['username'];
        $rate = $row['rate'];
        $opinion = $row['opinion'];
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
    <title>Teacher</title>

    <style>


    </style>

</head>
<body>
    <!-- Nav Section Start -->
    <?php include("include/navbar.php"); ?>
    <hr />
    <!-- Nav Section End -->

    <!-- Teacher Information Start -->
    <section class="teacher_section d-flex justify-content-center">
        <div class="col-10">
            <h2><span class="page_color">Welcome</span> <?php echo $username; ?></h2>
            <div class="teacher_information">
                <i class="fa-solid fa-user user_info_icon page_color"></i>
                <span class="user_info_title page_color">Information</span>

                <p>National ID: <?php echo $nationalID; ?></p>
                <p>Name: <?php echo $username; ?></p>
                <p>Position: Teacher</p>
                <p>Rate: <?php echo $rate; ?></p>
                <p>Opinion: <?php echo $opinion; ?></p>

                <div class="dropdown">
                    <button class="btn dropdown-toggle manage_account" data-bs-toggle="dropdown">Manage account</button>
                    <ul class="dropdown-menu">
                        <li><a href="change_username.php" class="dropdown-header">Change Username</a></li>
                        <li><a href="change_password.php" class="dropdown-header">Change Password</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <i class="fa-solid fa-users user_info_icon page_color"></i>
            <span class="user_info_title page_color">Students</span>
            <table>
                <tr><th>National ID</th><th>Username</th></tr>
                <?php

                $sql = "SELECT national_id, username FROM students";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $nationalID = $row['national_id'];
                        $username = $row['username'];
                        echo "<tr><td>$nationalID</td><td>$username</td></tr>";
                    }
                } else {
                    echo "0 results found";
                }

                ?>
            </table>
        </div>
    </section>
    <!-- Teacher Information End -->

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