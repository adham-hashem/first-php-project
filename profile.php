<?php

$userType = $_GET['type'];

if ($userType === 'Teacher') {
    header("Location: teacher.php");
    exit;
} else if ($userType === 'Admin') {
    header("Location: admin.php");
    exit;
} else if ($userType === 'Student'){
    header("Location: student.php");
    exit;
} else if ($userType === 'User') {
    header("Location: user.php");
    exit;
}

?>