<?php

// calculating the new rate
$sql = "SELECT rate FROM teacher_ratings WHERE teacher_national_id='$teacherNationalID'";
$result = $conn->query($sql);

$rate_sum = $rate_avg = 0;
$rows_count = $result->num_rows;

if ($rows_count > 0) {
    while ($row = $result->fetch_assoc()) {
        $rate_ = $row['rate'];

        $rate_sum += $rate_;
    }

    $rate_avg = $rate_sum / $rows_count;
    $rate_avg = sprintf("%.2f", $rate_avg);
}

// echo $rate_sum . "<br />" . $rate_avg;

// UPDATE rate

$sql = "UPDATE teachers
SET rate=$rate_avg
WHERE national_id='$teacherNationalID';";

if ($conn->query($sql)) {

} else {
    echo "Error updating record: " . $conn->error;
}

// UPDATE opinion

// Get current
$sql = "SELECT opinion FROM teacher_ratings WHERE teacher_national_id='$teacherNationalID'";
$result = $conn->query($sql);

$opinions = "";
$rows_count = $result->num_rows;

if ($rows_count > 0) {
    while ($row = $result->fetch_assoc()) {
        $opinions .= $row['opinion'] . " - "; 
    }

    $opinions = substr($opinions, 0, -2);
}

$sql = "UPDATE teachers SET opinion='$opinions'
WHERE national_id=$teacherNationalID";

if ($conn->query($sql)) {

} else {
    echo "Error in updating opinion: " . $conn->error;
}



?>