<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['type'] === 'Admin') {
        header("Location: admin.php");
        exit;
    } else if ($_SESSION['type'] === 'Student') {

    } else if ($_SESSION['type'] === 'Teacher'){
        header("Location: teacher.php");
        exit;
    } else if ($_SESSION['type'] === 'User') {
        header("Location: user.php");
    }
}

include ("config/config.php");

$id = $_SESSION['user_id'];
$nationalID = $username = "";
$type = $_SESSION['type'];

$sql = "SELECT national_id, username FROM students WHERE id=$id";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nationalID = $row['national_id'];
        $username = $row['username'];
    }
}

// =================

// prepared statement
$sql = "SELECT arabic, english, mathematics, chemistry, physics FROM degrees
WHERE student_national_id=?;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nationalID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $arabicDegree = $row['arabic'];
    $englishDegree = $row['english'];
    $mathematicsDegree = $row['mathematics'];
    $chemistryDegree = $row['chemistry'];
    $physicsDegree = $row['physics'];
} else {
    echo "Error in getting degrees: " . $conn->error;
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

function validate_rate ($rate) {
    return preg_match('/^(?:[1-9]|10)$/', $rate);
}

function validate_opinion ($opinion) {
    return preg_match('/^[A-Za-z,.\' ]+$/', $opinion);
}

$teacherNationalID = $opinion = "";
$rate = 0;
$teacherNationalIDErr = $rateErr = $opinionErr = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        if (empty($_POST['teacherID'])) {
            $teacherNationalIDErr = "Teacher ID is required";
        } else {
            $teacherNationalID = test_input($_POST['teacherID']);

            if (!validate_nationalID($teacherNationalID)) {
                $teacherNationalIDErr = "Invalid national ID format";
            }
        }

        if (empty($_POST['rate'])) {
            $rateErr = "Rate is required!";
        } else {
            $rate = test_input($_POST['rate']);

            if (!validate_rate($rate)) {
                $rateErr = "Invalid rate format";
            }
        }

        if (empty($_POST['opinion'])) {
            $opinionErr = "Opinion is required!";
        } else {
            $opinion = test_input($_POST['opinion']);

            if (!validate_opinion($_POST['opinion'])) {
                $opinionErr = "Invalid opinion format!";
            }
        }

        // echo $teacherNationalID . "<br />";
        // echo $rate . "<br />";
        // echo $opinion;

        $sql = "SELECT student_national_id, teacher_national_id FROM teacher_ratings WHERE student_national_id='$nationalID' AND teacher_national_id='$teacherNationalID'";  
        $result = $conn->query($sql);

        // If the student rated the teacher before
        if ($result->num_rows > 0) {

            if ($teacherNationalIDErr === "" && $rateErr === "" && $opinionErr === "") {
                $sql = "UPDATE teacher_ratings
                SET student_national_id=?, teacher_national_id=?, rate=?, opinion=?
                WHERE student_national_id=$nationalID AND teacher_national_id=$teacherNationalID;";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssds", $nationalID, $teacherNationalID, $rate, $opinion);

                if ($stmt->execute()) {
                    echo "<script>alert('Teacher rating updated successsfully');</script>";
                    // header("Location: student.php");
                    // exit;
                } else {
                    echo "Error: " . $sql . "<br />" . $conn->error; // right ??
                }

            }

        } else {

            if ($teacherNationalIDErr === "" && $rateErr === "" && $opinionErr === "") {
                $sql = "INSERT INTO teacher_ratings (student_national_id, teacher_national_id, rate, opinion)
                VALUES (?, ?, ?, ?);";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssds", $nationalID, $teacherNationalID, $rate, $opinion);

                if ($stmt->execute()) {
                    echo "<script>alert('Teacher rated successsfully');</script>";
                    // header("Location: student.php");
                    // exit;
                } else {
                    echo "Error: " . $sql . "<br />" . $conn->error; // right ??
                }
            }
        }

        include ("update_teacher_info.php");

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

echo "user profile image: " . $user_profile_image;



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
    <section class="student_section d-flex justify-content-center">
        <div class="col-10">
            <div class="row">
                <div class="col-6">
                    <h2 class="mb-0"><span class="page_color">Welcome</span></h2>
                    <h2><?php echo $username; ?></h2>

                    <div class="student_information">
                        <i class="fa-solid fa-user user_info_icon page_color"></i>
                        <span class="user_info_title page_color">Info</span>
                        <p>National ID: <?php echo $nationalID; ?></p>
                        <p>Username: <?php echo $username; ?></p>
                        <p>Position: Student</p>

                        <div class="dropdown">
                            <button class="btn dropdown-toggle manage_account" data-bs-toggle="dropdown">Manage account</button>
                            <ul class="dropdown-menu">
                                <li><a href="change_username.php" class="dropdown-header">Change Username</a></li>
                                <li><a href="change_password.php" class="dropdown-header">Change Password</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="profile_image">
                        <?php
                            
                            if ($user_profile_image === "") {
                                echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
                                            <div class="form-group">
                                            <label for="profilePhoto">Select profile Photo:</label>
                                            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" />
                                            </div>
                                            <br>
                                            <input type="submit" value="Upload" name="uploadProfilePhoto" />
                                        </form>';
                            } else {
                            
                                echo '<img src="' . $user_profile_image . '" alt="profile photo" />';
                                echo "<br>";
                                echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
                                            <div class="form-group">
                                            <label for="profilePhoto">Select profile Photo:</label>
                                            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" />
                                            </div>
                                            <input type="submit" value="Upload" name="uploadProfilePhoto" />
                                        </form>';
                            }
                                
                            ?>


                            <!-- <a class="btn btn-primary" href="uploadProfileImage.php">Change Photo</a> -->
                    </div>
                </div>
            </div>
            <hr />
            <!-- Degrees -->
            <i class="fa-solid fa-flask user_info_icon page_color"></i>
            <span class="user_info_title page_color">Degrees</span>
            
            <p>Arabic: <?php echo $arabicDegree; ?></p>
            <p>English: <?php echo $englishDegree; ?></p>
            <p>Mathematics: <?php echo $mathematicsDegree; ?></p> 
            <p>Chemistry: <?php echo $chemistryDegree; ?></p>    
            <p>Physics: <?php echo $physicsDegree; ?></p>
            

            <hr />
            <!-- Teachers -->
            <i class="fa-solid fa-chalkboard-user user_info_icon page_color"></i>
            <span class="user_info_title page_color">Teachers</span>
            <table>
                <tr><th>National ID</th><th>Username</th><th>Rate</th><th>Opinion</th></tr>

                <?php 

                // $sql = "SELECT teachers.national_id, teachers.username, teacher_ratings.rate, teacher_ratings.opinion
                // FROM teachers
                // JOIN teacher_ratings ON teachers.national_id = teacher_ratings.teacher_national_id";
                $sql = "SELECT * FROM teachers";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $nationalID = $row['national_id'];
                        $username = $row['username'];
                        $rate = $row['rate'];
                        $opinion = $row['opinion'];

                        echo "<tr><td>$nationalID</td><td>$username</td><td>$rate</td><td>$opinion</td></tr>";
                    }
                } else {
                    echo "0 results found"  ;
                }

                $conn->close();

                ?>

            </table>
            <hr>
            <!-- Rate a teacher -->
            <i class="fa-solid fa-percent user_info_icon page_color"></i>
            <span class="user_info_title page_color">Rate a teacher</span>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group mb-2">
                    <label for="teacherID" class="form-label">Teacher national ID</label>
                    <input id="teacherID" name="teacherID" type="text" class="form-control" required />
                    <?php echo $teacherNationalIDErr; ?>
                </div>
                <div class="form-group mb-2">
                    <label for="rate" class="form-label">Rate: select 0 or 1</label>
                    <select id="rate" name="rate" class="form-control">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                    </select>
                    <?php echo $rateErr; ?>
                </div>
                <span>Opinion</span>
                <div class="form-group mb-2">
                    <textarea class="form-control" name="opinion" id="opinion" placeholder="Only upper and lower case letters, commas, apostrophes, dots, and spaces are allowed!" required></textarea>
                    <?php echo $opinionErr; ?>
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