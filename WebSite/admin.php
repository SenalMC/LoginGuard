<?php
session_start();

// 确保只有授权用户才能访问


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

// 处理 AJAX 请求
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = readAdminData();

    // 添加管理员
    if (isset($_POST['action']) && $_POST['action'] === "add") {
        $playerID = $_POST['playerID'];
        $email = $_POST['email'];

        if (!isset($data['admins'][$playerID])) {
            $data['admins'][$playerID] = [
                "email" => $email,
                "allowed_ip" => null,
                "expire_time" => null
            ];
            writeAdminData($data);
            echo json_encode(["status" => "success", "message" => "管理员添加成功"]);
        } else {
            echo json_encode(["status" => "error", "message" => "管理员已存在"]);
        }
        exit;
    }

    // 更新管理员邮箱
    if (isset($_POST['action']) && $_POST['action'] === "update_email") {
        $playerID = $_POST['playerID'];
        $email = $_POST['email'];

        if (isset($data['admins'][$playerID])) {
            $data['admins'][$playerID]['email'] = $email;
            writeAdminData($data);
            echo json_encode(["status" => "success", "message" => "邮箱更新成功"]);
        } else {
            echo json_encode(["status" => "error", "message" => "管理员不存在"]);
        }
        exit;
    }

    // 清除管理员 IP
    if (isset($_POST['action']) && $_POST['action'] === "clear_ip") {
        $playerID = $_POST['playerID'];

        if (isset($data['admins'][$playerID])) {
            $data['admins'][$playerID]['allowed_ip'] = null;
            $data['admins'][$playerID]['expire_time'] = null;
            writeAdminData($data);
            echo json_encode(["status" => "success", "message" => "IP 解绑成功"]);
        } else {
            echo json_encode(["status" => "error", "message" => "管理员不存在"]);
        }
        exit;
    }

    // 删除管理员
    if (isset($_POST['action']) && $_POST['action'] === "delete") {
        $playerID = $_POST['playerID'];

        if (isset($data['admins'][$playerID])) {
            unset($data['admins'][$playerID]);
            writeAdminData($data);
            echo json_encode(["status" => "success", "message" => "管理员已删除"]);
        } else {
            echo json_encode(["status" => "error", "message" => "管理员不存在"]);
        }
        exit;
    }
}

$data = readAdminData();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

<div class="w-3/4 bg-gray-800 p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">管理员管理</h2>

    <h3 class="text-xl mb-2">添加管理员</h3>
    <input type="text" id="playerID" placeholder="玩家 ID" class="w-full p-2 mb-2 rounded">
    <input type="email" id="email" placeholder="管理员邮箱" class="w-full p-2 mb-2 rounded">
    <button onclick="addAdmin()" class="w-full bg-blue-600 p-2 rounded hover:bg-blue-700">添加管理员</button>

    <h3 class="text-xl mt-4">当前管理员</h3>
    <table class="w-full mt-2 border-collapse border border-gray-700">
        <tr class="bg-gray-700">
            <th class="p-2 border border-gray-600">玩家 ID</th>
            <th class="p-2 border border-gray-600">邮箱</th>
            <th class="p-2 border border-gray-600">IP 地址</th>
            <th class="p-2 border border-gray-600">操作</th>
        </tr>
        <?php foreach ($data['admins'] as $playerID => $admin): ?>
            <tr class="bg-gray-600">
                <td class="p-2 border border-gray-600"><?= htmlspecialchars($playerID) ?></td>
                <td class="p-2 border border-gray-600">
                    <input type="email" value="<?= htmlspecialchars($admin['email']) ?>" id="email_<?= $playerID ?>" class="bg-gray-700 p-1 rounded">
                    <button onclick="updateEmail('<?= $playerID ?>')" class="bg-yellow-500 p-1 rounded">更新</button>
                </td>
                <td class="p-2 border border-gray-600"><?= $admin['allowed_ip'] ?: "未绑定" ?></td>
                <td class="p-2 border border-gray-600">
                    <button onclick="clearIP('<?= $playerID ?>')" class="bg-green-500 p-1 rounded">解绑 IP</button>
                    <button onclick="deleteAdmin('<?= $playerID ?>')" class="bg-red-500 p-1 rounded">删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p id="message" class="text-red-400 mt-2"></p>
</div>

<script>
function ajaxPost(data) {
    fetch("admin.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data)
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("message").textContent = data.message;
        if (data.status === "success") location.reload();
    });
}

function addAdmin() {
    ajaxPost({
        action: "add",
        playerID: document.getElementById("playerID").value,
        email: document.getElementById("email").value
    });
}

function updateEmail(playerID) {
    ajaxPost({
        action: "update_email",
        playerID: playerID,
        email: document.getElementById("email_" + playerID).value
    });
}

function clearIP(playerID) {
    ajaxPost({ action: "clear_ip", playerID: playerID });
}

function deleteAdmin(playerID) {
    if (confirm("确定删除管理员 " + playerID + " 吗？")) {
        ajaxPost({ action: "delete", playerID: playerID });
    }
}
</script>

</body>
</html>
