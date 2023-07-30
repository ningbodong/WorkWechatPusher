<!DOCTYPE html>
<html>
<head>
    <title>企业微信参数配置</title>
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

        // 处理保存企业微信参数的功能
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"])) {
            $corpid = $_POST["corpid"];
            $corpsecret = $_POST["corpsecret"];
            $agentid = $_POST["agentid"];
            $touser = $_POST["touser"];

            if (!empty($corpid) && !empty($corpsecret) && !empty($agentid) && !empty($touser)) {
                $username = $_SESSION["username"];

                $query = "UPDATE users SET corpid = ?, corpsecret = ?, agentid = ?, touser = ? WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $corpid, $corpsecret, $agentid, $touser, $username);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        echo "保存失败，或未修改内容";
                    }
                } else {
                    echo "错误：" . $conn->error;
                }
                $stmt->close();
            }
        }

        $username = $_SESSION["username"];
        $query = "SELECT corpid, corpsecret, agentid, touser FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $params = $result->fetch_assoc();
    ?>
    
        <div class="container">
        <?php include 'header.php'; ?>

    <div class="container">
        <h2>企业微信参数配置</h2>
        <form method="post" action="pusherset.php">
            <label for="corpid">企业ID:</label>
            <input type="text" name="corpid" value="<?php echo isset($params['corpid']) ? $params['corpid'] : ''; ?>" required>

            <label for="corpsecret">应用Secret:</label>
            <input type="text" name="corpsecret" value="<?php echo isset($params['corpsecret']) ? $params['corpsecret'] : ''; ?>" required>

            <label for="agentid">应用ID:</label>
            <input type="text" name="agentid" value="<?php echo isset($params['agentid']) ? $params['agentid'] : ''; ?>" required>

            <label for="touser">用户ID（如zhangsan）:</label>
            <input type="text" name="touser" value="<?php echo isset($params['touser']) ? $params['touser'] : ''; ?>" required>

            <input class="submit-btn" type="submit" value="保存参数">
        </form>
    </div>
</body>
</html>
