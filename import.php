<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

function convertEncoding($str) {
    if (mb_detect_encoding($str, 'UTF-8', true) === false) {
        return iconv('GBK', 'UTF-8', $str);
    }
    return $str;
}

if (isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $username = $_SESSION["username"];

    if (($handle = fopen($file, "r")) !== false) {
        fgetcsv($handle, 1000, ",");

        $query = "INSERT INTO reminders (user_id, reminder_name, expiration_date, notification_days, remark) VALUES ((SELECT id FROM users WHERE username = ?), ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $data = array_map('convertEncoding', $data);

            $reminder_name = $data[0];
            $expiration_date = $data[1];
            $notification_days = $data[2];
            $remark = $data[3];

            $stmt->bind_param("sssss", $username, $reminder_name, $expiration_date, $notification_days, $remark);

            if (!$stmt->execute()) {
                die("Error executing the query: " . $stmt->error);
            }
        }

        fclose($handle);

        header("Location: dashboard.php");
        exit;
    } else {
        die("Error reading the CSV file");
    }
}
?>
