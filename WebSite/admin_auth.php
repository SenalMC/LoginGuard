<?php
session_start();
require 'PHPMailer/PHPMailer.php'; // 引入 PHPMailer
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

define("JSON_FILE", "admins.json");

// 读取 JSON 数据
function readAdminData() {
    if (!file_exists(JSON_FILE)) {
        return ["admins" => []];
    }
    return json_decode(file_get_contents(JSON_FILE), true);
}

// 写入 JSON 数据
function writeAdminData($data) {
    return file_put_contents(JSON_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

// 获取管理员信息
function getAdminInfo($playerID) {
    $data = readAdminData();
    return $data['admins'][$playerID] ?? null;
}

// 生成 128 位验证码
function generateCode() {
    return bin2hex(random_bytes(64)); // 128-bit hex
}

// 发送邮件验证码
function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.qiye.ex.com'; // SMTP 服务器
        $mail->SMTPAuth = true;
        $mail->Username = 'op@op.top';
        $mail->Password = '123123aen';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = "utf-8"; 

        $mail->setFrom('op@op.top', ' Security');
        $mail->addAddress($email);
        $mail->Subject = '管理员登录验证码';
        $mail->Body = "您的验证码是：\n\n$code\n\n有效期为10分钟，请尽快输入。\n验证码输入终端必须与你的登录终端位于同一网络环境并保证你已经关闭加速器.\n验证码长度为128位小写字母与数字混合，请直接在电脑登录邮箱后复制。";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

// 处理管理员身份验证请求
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = readAdminData();
    $playerID = $_POST['playerID'];
    $email = $_POST['email'];

    if (isset($data['admins'][$playerID]) && $data['admins'][$playerID]['email'] === $email) {
        $code = generateCode();
        $_SESSION['verification_code'] = $code;
        $_SESSION['playerID'] = $playerID;
        $_SESSION['email'] = $email;
        $_SESSION['expires'] = time() + 600; // 10 分钟有效期

        if (sendVerificationEmail($email, $code)) {
            echo json_encode(["status" => "success", "message" => "验证码已发送到您的邮箱"]);
        } else {
            echo json_encode(["status" => "error", "message" => "邮件发送失败"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "管理员 ID 或邮箱不匹配"]);
    }
}

// 处理验证码验证请求
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify_code'])) {
    $userCode = $_POST['verify_code'];

    if (
        isset($_SESSION['verification_code']) &&
        $userCode === $_SESSION['verification_code'] &&
        time() < $_SESSION['expires']
    ) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $expireTime = time() + 12 * 3600; // 12 小时有效

        $data['admins'][$_SESSION['playerID']]['allowed_ip'] = $ip;
        $data['admins'][$_SESSION['playerID']]['expire_time'] = $expireTime;
        writeAdminData($data);

        echo json_encode(["status" => "success", "message" => "IP 绑定成功，12 小时内有效"]);
    } else {
        echo json_encode(["status" => "error", "message" => "验证码错误或已过期"]);
    }
}

// 清理过期 IP
function cleanExpiredIPs() {
    $data = readAdminData();
    $currentTime = time();

    foreach ($data['admins'] as &$admin) {
        if ($admin['expire_time'] !== null && $currentTime >= $admin['expire_time']) {
            $admin['allowed_ip'] = null;
            $admin['expire_time'] = null;
        }
    }
    writeAdminData($data);
}

cleanExpiredIPs(); // 定期清理
?>
