<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        .captcha {
            display: flex;
            align-items: center;
        }
        .captcha-img {
            margin-right: 10px;
        }
        .captcha-text {
            font-size: 16px;
            font-weight: bold;
        }
        .login-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .login-btn:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        require_once 'db.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $captcha = $_POST["captcha"];

            // 检查验证码是否正确
            if (isset($_SESSION["captcha"]) && $_SESSION["captcha"] == $captcha) {
                $query = "SELECT * FROM users WHERE username = ? AND password = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $username, md5($password));
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $_SESSION["username"] = $username;
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error_message = "用户名或密码错误";
                }
            } else {
                $error_message = "验证码错误";
            }
        }

        // 生成随机的4位数验证码
        $captcha = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $_SESSION["captcha"] = $captcha;
    ?>
    <div class="container">
        <h2>用户登录</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">用户名:</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">密码:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group captcha">
                <div class="captcha-img">
                    <img src="captcha_image.php" alt="Captcha">
                </div>
                <div class="captcha-text">
                    <label for="captcha">验证码:</label>
                    <input type="text" name="captcha" required>
                </div>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <button class="login-btn" type="submit">登录</button>
        </form>
    </div>
</body>
</html>
