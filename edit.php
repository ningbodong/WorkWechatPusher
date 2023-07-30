<!DOCTYPE html>
<html>
<head>
    <title>Edit Reminder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .logout {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        .logout:hover {
            background-color: #45a049;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        .notification-days {
            width: 40px;
        }
        .remark {
            width: 75%;
            height: 100px;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .table-container {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .add-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .add-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        require_once 'db.php';

        if (!isset($_SESSION["username"])) {
            header("Location: index.php");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST["id"];
            $username = $_SESSION["username"];
            $reminder_name = $_POST["reminder_name"];
            $expiration_date = $_POST["expiration_date"];
            $notification_days = $_POST["notification_days"];
            $remark = $_POST["remark"];

            $query = "UPDATE reminders SET reminder_name = ?, expiration_date = ?, notification_days = ?, remark = ? WHERE id = ? AND user_id = (SELECT id FROM users WHERE username = ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssis", $reminder_name, $expiration_date, $notification_days, $remark, $id, $username);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error: " . $conn->error;
            }
            $stmt->close();
        }

        // 获取传递过来的项目信息，用于填充表单
        $id = $_GET["id"];
        $reminder_name = $_GET["name"];
        $expiration_date = $_GET["date"];
        $notification_days = $_GET["days"];
        $remark = $_GET["remark"];
    ?>
    
        <div class="container">
        <?php include 'header.php'; ?>
    
    
    <div class="container">
        <div class="header">
            <h2>编辑提醒</h2>
        </div>

        <form method="post" action="edit.php">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label for="reminder_name">项目名称:</label>
                <input type="text" name="reminder_name" value="<?php echo $reminder_name; ?>" required>
            </div>

            <div class="form-group">
                <label for="expiration_date">到期日期:</label>
                <input type="date" name="expiration_date" value="<?php echo date('Y-m-d', strtotime($expiration_date)); ?>" required>
            </div>

            <div class="form-group">
                <label for="notification_days">提前提醒天数:</label>
                <input class="notification-days" type="number" name="notification_days" value="<?php echo $notification_days; ?>" required>
            </div>

            <div class="form-group">
                <label for="remark">备注:</label>
                <textarea class="remark" name="remark"><?php echo $remark; ?></textarea>
            </div>

            <input class="submit-btn" type="submit" value="保存修改">
        </form>
    </div>
</body>
</html>