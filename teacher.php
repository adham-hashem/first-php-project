<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['type'] === 'Admin') {
        header("Location: admin.php");
        exit;
    } else if ($_SESSION['type'] === 'Student') {
        header("Location: student.php");
        exit;
    } else if ($_SESSION['type'] === 'Teacher') {
        // Continue to the teacher page
    } else if ($_SESSION['type'] === 'User') {
        header("Location: user.php");
        exit;
    }
}

include("config/config.php");

$user_id = $_SESSION['user_id'];
$nationalID = $username = $rate = $opinion = "";
$type = $_SESSION['type'];

$sql = "SELECT * FROM teachers WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nationalID = $row['national_id'];
        $username = $row['username'];
        $rate = $row['rate'];
        $opinion = $row['opinion'];
    }
}

// Profile Photo Upload Function
function uploadFile($file, $uploadDirectory, $allowedExtensions, $maxFileSize) {
    $filename = $file["name"];
    $tmpFilePath = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        return "Invalid file type.";
    }

    if ($fileSize > $maxFileSize) {
        return "File is too large.";
    }

    $uniqueFilename = uniqid() . '-' . $filename;
    $targetFilePath = $uploadDirectory . $uniqueFilename;

    if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
        return $targetFilePath;
    } else {
        return "Error uploading file.";
    }
}

$uploadDirectory = "profile photos/";
$allowedExtensions = ["jpg", "jpeg", "png"];
$maxFileSize = 10 * 1024 * 1024; // 10 MB

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePhoto'])) {
    if (isset($_POST['uploadProfilePhoto'])) {
        $filename = uploadFile($_FILES['profilePhoto'], $uploadDirectory, $allowedExtensions, $maxFileSize);

        if (strpos($filename, 'Invalid') === false && strpos($filename, 'Error') === false) {
            // Storing the profile photo in the database
            $sql = "INSERT INTO images (filename, description, national_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $description = "$username, $nationalID profile photo";
            $stmt->bind_param("sss", $filename, $description, $nationalID);
            if ($stmt->execute()) {
                // echo 'The photo is uploaded successfully';

                // Updating profile photo data
                $sql = "UPDATE teachers SET profile_image=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $filename, $user_id);
                if (!$stmt->execute()) {
                    echo "Error in updating profile photo";
                }
            } else {
                echo "Error in uploading the image!";
            }
        } else {
            echo $filename;
        }
    }
}

// Getting profile photo if the user has
$sql = "SELECT profile_image FROM teachers WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
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
</head>
<body>
    <!-- Nav Section Start -->
    <?php include("include/navbar.php"); ?>
    <hr />
    <!-- Nav Section End -->

    <!-- Teacher Information Start -->
    <section class="teacher_section d-flex justify-content-center">
        <div class="col-10">
            <div class="row">
                <div class="col-6">
                    <h2><span class="page_color">Welcome</span> <?php echo htmlspecialchars($username); ?></h2>
                    <div class="teacher_information">
                        <i class="fa-solid fa-user user_info_icon page_color"></i>
                        <span class="user_info_title page_color">Information</span>

                        <p>National ID: <?php echo htmlspecialchars($nationalID); ?></p>
                        <p>Name: <?php echo htmlspecialchars($username); ?></p>
                        <p>Position: Teacher</p>
                        <p>Rate: <?php echo htmlspecialchars($rate); ?></p>
                        <p>Opinion: <?php echo htmlspecialchars($opinion); ?></p>

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
                        <?php if (empty($user_profile_image)): ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="profilePhoto">Select profile Photo:</label>
                                    <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" />
                                </div>
                                <br>
                                <input type="submit" value="Upload" name="uploadProfilePhoto" />
                            </form>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($user_profile_image); ?>" alt="profile photo" />
                            <br>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="profilePhoto">Select profile Photo:</label>
                                    <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" />
                                </div>
                                <input type="submit" value="Upload" name="uploadProfilePhoto" />
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Students -->
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
                        echo "<tr><td>" . htmlspecialchars($nationalID) . "</td><td>" . htmlspecialchars($username) . "</td></tr>";
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
