<?php
session_start();

$type = $_SESSION['type'];
$nationalID = $_SESSION['nationalID'];

include ("config/config.php");

$sql = "DELETE FROM " .
(($type === "Admin") ? "admins" :
(($type === "User") ? "users" :
(($type === "Teacher") ? "teachers" :
(($type === "Student") ? "students" : ""
)))) .
" WHERE national_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nationalID);

if ($stmt->execute()) {

} else {
    echo "Error: " . $stmt->error;
}

header("Location: logout.php");


?>