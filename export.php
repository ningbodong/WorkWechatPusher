<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['export'])) {
    function exportData($conn) {
        $username = $_SESSION["username"];
        $query = "SELECT * FROM reminders WHERE user_id = (SELECT id FROM users WHERE username = ?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }

        $stmt->bind_param("s", $username);

        if (!$stmt->execute()) {
            die("Error executing the query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $data = array(
            array("项目名称", "到期日期", "提前提醒天数", "备注")
        );

        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                $row['reminder_name'],
                date('Y-m-d', strtotime($row['expiration_date'])),
                $row['notification_days'],
                $row['remark']
            );
        }

        return $data;
    }

    $data = exportData($conn);

    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=reminders.csv');

    echo "\xEF\xBB\xBF";

    $output = fopen('php://output', 'w');
    foreach ($data as $row) {
        fputcsv($output, $row, ',', '"');
    }

    fclose($output);
    exit;
}
?>
