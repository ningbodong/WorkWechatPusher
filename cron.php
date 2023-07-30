<?php

require_once 'db.php';

function sendreminder($corpid, $corpsecret, $agentid, $touser, $title, $description, $url) {
    $json = '{"touser":"","msgtype":"textcard","agentid":"","textcard":{"title":"","description":"","url":"","btntxt":"更多"},"safe":1,"enable_id_trans":0,"enable_duplicate_check":0}';
    $json = json_decode($json);

    $json->touser = $touser;
    $json->agentid = $agentid;
    $json->textcard->title = $title ? $title : '提醒';
    $json->textcard->description = $description ? $description : '这是一个提醒。';
    $json->textcard->url = $url ? $url : 'URL';

    // 获取access_token
    $response = CurlGet("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corpid&corpsecret=$corpsecret","","");
    $access_token = json_decode($response)->access_token;

    if(!$access_token){
        exit("获取access_token失败");
    } else {
        // 调用企业微信的发送消息API，可设置延迟
        sleep(1);
        $result = CurlPost("https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$access_token","", json_encode($json));

        // 输出结果
        echo $result;
    }
}

// 获取当前时间
$current_time = date('Y-m-d H:i:s');

// 查询数据库，获取需要发送的提醒信息
$query = "SELECT * FROM reminders WHERE DATE(expiration_date) >= CURDATE() AND DATE(expiration_date) <= DATE_ADD(CURDATE(), INTERVAL notification_days DAY)";
$result = $conn->query($query);

if ($result->num_rows > 0) {

    $reminders_to_send = array();

    while ($row = $result->fetch_assoc()) {

        $user_id = $row['user_id'];
        $user_query = "SELECT * FROM users WHERE id = $user_id";
        $user_result = $conn->query($user_query);

        if ($user_result->num_rows === 1) {
            $user_row = $user_result->fetch_assoc();

            $corpid = $user_row['corpid'];
            $corpsecret = $user_row['corpsecret'];
            $agentid = $user_row['agentid'];
            $touser = $user_row['touser']; // 获取touser字段的值
            $title = $row['reminder_name'];
            $description = '项目名称：' . $row['reminder_name'] . PHP_EOL .
                           '到期日期：' . $row['expiration_date'] . PHP_EOL .
                           '提前提醒天数：' . $row['notification_days'] . PHP_EOL .
                           '备注：' . $row['remark'];
            $url = 'http://www.github.com'; // 这里需要根据实际情况设置提醒消息的跳转链接

            $expiration_date = $row['expiration_date'];
            $notification_days = $row['notification_days'];

            // 计算提前提醒的日期
            $reminder_date = date('Y-m-d', strtotime("-$notification_days day", strtotime($expiration_date)));

            // 判断是否在提前提醒的范围内
            if ($current_time >= $reminder_date) {
                $reminders_to_send[] = array(
                    'corpid' => $corpid,
                    'corpsecret' => $corpsecret,
                    'agentid' => $agentid,
                    'touser' => $touser,
                    'title' => $title,
                    'description' => $description,
                    'url' => $url
                );
            }
        }
    }

    foreach ($reminders_to_send as $reminder) {
        sendreminder($reminder['corpid'], $reminder['corpsecret'], $reminder['agentid'], $reminder['touser'], $reminder['title'], $reminder['description'], $reminder['url']);
    }
}

$conn->close();

function CurlGet($url,$cookies = "",$UserAgent = "")
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($curl, CURLOPT_URL, $url);   
    curl_setopt($curl, CURLOPT_REFERER, '');
    curl_setopt($curl, CURLOPT_COOKIE, $cookies);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    if ($UserAgent != "") {
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function CurlPost($url, $cookies="", $post_data="", $headers=array(), $refer="", $UserAgent = '')
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($curl, CURLOPT_URL, $url);   
    curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
    curl_setopt($curl, CURLOPT_COOKIE, $cookies);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    if ($refer != '') {
        curl_setopt($curl, CURLOPT_REFERER, $refer);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>
