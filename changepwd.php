<!DOCTYPE html>
<html>
<head>
    <title>修改密码</title>
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
        $username = $_SESSION["username"];
        $new_password = $_POST["new_password"];


        $hashed_password = md5($new_password);


        $query = "UPDATE users SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $hashed_password, $username);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
    ?>
    
        <div class="container">
        <?php include 'header.php'; ?>

    
        <div class="container">
            <h2>修改密码</h2>
            <form method="post" action="changepwd.php" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="new_password">新密码:</label>
                    <input type="password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">确认新密码:</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <input class="submit-btn" type="submit" value="修改密码">
            </form>
        </div>

        <script>
            function validateForm() {
                var newPassword = document.getElementsByName("new_password")[0].value;
                var confirmPassword = document.getElementsByName("confirm_password")[0].value;

                if (newPassword !== confirmPassword) {
                    alert("两次输入的密码不同，请重新输入。");
                    return false;
                }

                return true;
            }
        </script>
    </div>
</body>
</html>
