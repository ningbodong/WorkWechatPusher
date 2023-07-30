<!DOCTYPE html>
<html>
<head>
    <title>管理员</title>
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
} else {
    $username = $_SESSION["username"];
    $query_admin = "SELECT id FROM users WHERE username = ? AND id = 1";
    $stmt_admin = $conn->prepare($query_admin);
    $stmt_admin->bind_param("s", $username);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows === 0) {
        header("Location: dashboard.php");
        exit;
    }
}


    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_username"]) && isset($_POST["new_password"])) {
        $new_username = $_POST["new_username"];
        $new_password = $_POST["new_password"];


        $hashed_password = md5($new_password);

        $query_new_user = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt_new_user = $conn->prepare($query_new_user);
        $stmt_new_user->bind_param("ss", $new_username, $hashed_password);

        if ($stmt_new_user->execute()) {
            $stmt_new_user->close();
            header("Location: admin.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"]) && isset($_POST["new_password"])) {
        $user_id = $_POST["user_id"];
        $new_password = $_POST["new_password"];

        $hashed_password = md5($new_password);

        $query_update_password = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update_password = $conn->prepare($query_update_password);
        $stmt_update_password->bind_param("si", $hashed_password, $user_id);

        if ($stmt_update_password->execute()) {
            $stmt_update_password->close();
            header("Location: admin.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $query_all_users = "SELECT * FROM users";
    $result_all_users = $conn->query($query_all_users);
    ?>
    
        <div class="container">
        <?php include 'header.php'; ?>

    <div class="container">
        <h2>管理员</h2>
        <h3>创建新用户</h3>
        <form method="post" action="admin.php">
            <div class="form-group">
                <label for="new_username">新用户名:</label>
                <input type="text" name="new_username" required>
            </div>

            <div class="form-group">
                <label for="new_password">新密码:</label>
                <input type="password" name="new_password" required>
            </div>

            <input class="submit-btn" type="submit" value="创建新用户">
        </form>

        <h3>修改用户密码</h3>
        <table>
            <tr>
                <th>用户ID</th>
                <th>用户名</th>
                <th>操作</th>
            </tr>
            <?php
                while ($row_user = $result_all_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row_user['id']."</td>";
                    echo "<td>".$row_user['username']."</td>";
                    echo "<td>";
                    echo "<form method='post' action='admin.php'>";
                    echo "<input type='hidden' name='user_id' value='".$row_user['id']."'>";
                    echo "<label for='new_password'>新密码:</label>";
                    echo "<input type='password' name='new_password' required>";
                    echo "<input class='submit-btn' type='submit' value='修改密码'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
</body>
</html>
