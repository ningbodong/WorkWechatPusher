<!DOCTYPE html>
<html>
<head>
    <title>仪表盘</title>
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
        $_SESSION['conn'] = $conn;
        // 处理登出功能
        if (isset($_GET['logout'])) {
            session_destroy();
            header("Location: index.php");
            exit;
        }

        if (!isset($_SESSION["username"])) {
            header("Location: index.php");
            exit;
        }
        // 处理新增项目请求
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_SESSION["username"];
            $reminder_name = $_POST["reminder_name"];
            $expiration_date = $_POST["expiration_date"];
            $notification_days = $_POST["notification_days"];
            $remark = $_POST["remark"];

            $query = "INSERT INTO reminders (user_id, reminder_name, expiration_date, notification_days, remark) VALUES ((SELECT id FROM users WHERE username = ?), ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $username, $reminder_name, $expiration_date, $notification_days, $remark);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error: " . $conn->error;
            }
            $stmt->close();
        }

        // 处理删除项目请求
        if (isset($_GET['delete_id'])) {
            $delete_id = $_GET['delete_id'];
            $username = $_SESSION["username"];

            $query = "DELETE FROM reminders WHERE id = ? AND user_id = (SELECT id FROM users WHERE username = ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $delete_id, $username);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error: " . $conn->error;
            }
            $stmt->close();
        }

        // 查询数据库，获取用户提醒项目信息
        $username = $_SESSION["username"];
        $query = "SELECT * FROM reminders WHERE user_id = (SELECT id FROM users WHERE username = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
           
        function exportData() {
    $username = $_SESSION["username"];
    $query = "SELECT * FROM reminders WHERE user_id = (SELECT id FROM users WHERE username = ?)";
    $stmt = $GLOBALS['conn']->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
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
    ?>

        <div class="container">
        <?php include 'header.php'; ?>
        
        <!-- 新增项目表单 -->
        <h2>新增项目</h2>
        <form method="post" action="dashboard.php">
            <div class="form-group">
                <label for="reminder_name">项目名称:</label>
                <input type="text" name="reminder_name" required>
            </div>

            <div class="form-group">
                <label for="expiration_date">到期日期:</label>
                <input type="date" name="expiration_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="notification_days">提前提醒天数:</label>
                <input class="notification-days" type="number" name="notification_days" value="3" required>
            </div>

            <div class="form-group">
                <label for="remark">备注:</label>
                <textarea class="remark" name="remark"></textarea>
            </div>

            <input class="submit-btn" type="submit" value="新增项目">
        </form>

        <!-- 提醒项目列表 -->
        <div class="table-container">
            <h2>提醒项目列表</h2>
            <!-- 导入按钮 -->
<input class="import-btn" type="button" value="导入" onclick="importData()">
<!-- 导出按钮 -->
<input class="export-btn" type="button" value="导出" onclick="exportData()">

            <table>
                <tr>
                    <th>项目名称</th>
                    <th>到期日期</th>
                    <th>提前提醒天数</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
                <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['reminder_name']."</td>";
                        echo "<td>".date('Y-m-d', strtotime($row['expiration_date']))."</td>";
                        echo "<td>".$row['notification_days']."</td>";
                        echo "<td>".$row['remark']."</td>";
                        echo "<td>";
                        echo "<a href=\"edit.php?id=".$row['id']."&name=".$row['reminder_name']."&date=".$row['expiration_date']."&days=".$row['notification_days']."&remark=".$row['remark']."\">修改</a> | ";
                        echo "<a href=\"dashboard.php?delete_id=".$row['id']."\">删除</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    $stmt->close();
                    $conn->close();
                ?>
            </table>
        </div>

    </div>
</body>
<script>
    function exportData() {
        window.location.href = "export.php?export=1";
    }

    function importData() {
        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.accept = ".csv";
        fileInput.addEventListener("change", handleFileSelect, false);
        fileInput.click();
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        const formData = new FormData();
        formData.append("csv_file", file);

        fetch("import.php", {
            method: "POST",
            body: formData,
        })
        .then(response => {
            if (response.ok) {
                alert("导入成功！");
                window.location.reload();
            } else {
                alert("导入失败，请确保CSV文件格式正确。");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("导入失败，请检查文件是否正确。");
        });
    }
</script>
</html>
