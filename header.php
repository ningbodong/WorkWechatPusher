<div class="header">
    <a class="submit-btn" href="dashboard.php">返回项目列表</a>
    <a class="submit-btn" href="pusherset.php">配置企业微信参数</a>
    <a class="submit-btn" href="changepwd.php">修改密码</a>
    <?php
    $username = $_SESSION["username"];
    $query_admin = "SELECT id FROM users WHERE username = ? AND id = 1";
    $stmt_admin = $conn->prepare($query_admin);
    $stmt_admin->bind_param("s", $username);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows > 0) {
        echo '<a class="submit-btn" href="admin.php">管理员</a>';
    }
    ?>
    <a class="logout" href="dashboard.php?logout=1">退出登录</a>
</div>
