<?php
require_once 'db_connect.php';
require_once 'log_message.php';

$id = $name = $contact_info = $company_name = $notes = "";
$errors = [];
$successMessage = "";

// 載入客戶資料（GET 請求）
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET["id"]);
    $conn = getConnection();
    $sql = "SELECT * FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $contact_info = $row["contact_info"];
        $company_name = $row["company_name"];
        $notes = $row["notes"];
    } else {
        $errors[] = "找不到該客戶資料";
    }

    $stmt->close();
    $conn->close();
}

// 更新客戶資料（POST 請求）
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"]);
    $name = trim($_POST["name"]);
    $contact_info = trim($_POST["contact_info"]);
    $company_name = trim($_POST["company_name"]);
    $notes = trim($_POST["notes"]);

    // 驗證輸入
    if (empty($name)) $errors[] = "客戶姓名為必填";
    if (empty($contact_info)) $errors[] = "聯絡資訊為必填";
    if (empty($company_name)) $errors[] = "公司名稱為必填";

    if (empty($errors)) {
        $conn = getConnection();

        // 獲取舊資料
        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldData = $result->fetch_assoc();

        // 更新資料
        $sql = "UPDATE customers SET name = ?, contact_info = ?, company_name = ?, notes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $contact_info, $company_name, $notes, $id);

        if ($stmt->execute()) {
            $successMessage = "客戶資料更新成功！";

            // 紀錄更新日誌
            $logMessage = "客戶 ID: {$id} 資料已更新。\n";
            $logMessage .= "更新前: " . json_encode($oldData, JSON_UNESCAPED_UNICODE) . "\n";
            $logMessage .= "更新後: " . json_encode([
                'name' => $name,
                'contact_info' => $contact_info,
                'company_name' => $company_name,
                'notes' => $notes
            ], JSON_UNESCAPED_UNICODE) . "\n";
            logMessage($logMessage);

            // 自動跳轉到客戶列表
            echo "<script>
                    alert('客戶資料更新成功！');
                    window.location.href = 'view_customers.php';
                  </script>";
            exit();
        } else {
            $errors[] = "資料庫更新失敗: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯客戶資料</title>
    <style>
        .form-container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container textarea {
            width: calc(100% - 12px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-container textarea {
            height: 100px;
        }

        .form-container input[type="submit"] {
            background-color: #0ABAB5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #FF3737;
        }

        .message-container {
            width: 60%;
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }

        .errors {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
            padding: 10px;
        }

        .success {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            margin-bottom: 15px;
            padding: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        .navigation {
            text-align: center;
            margin-top: 20px;
        }
        .navigation a {
            text-decoration: none;
            color: #1abc9c;
            font-size: 1.2rem;
            margin: 0 10px;
        }
        .navigation a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center; color: #333;">編輯客戶資料</h1>

    <!-- 顯示錯誤訊息 -->
    <?php if (!empty($errors)): ?>
        <div class="message-container errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 顯示成功訊息 -->
    <?php if (!empty($successMessage)): ?>
        <div class="message-container success">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- 編輯表單 -->
    <form method="post" class="form-container">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="name">客戶姓名:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="contact_info">聯絡資訊:</label>
        <textarea name="contact_info" id="contact_info"><?php echo htmlspecialchars($contact_info, ENT_QUOTES, 'UTF-8'); ?></textarea>
        
        <label for="company_name">公司名稱:</label>
        <input type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?>">
        
        <label for="notes">備註:</label>
        <textarea name="notes" id="notes"><?php echo htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
        
        <input type="submit" value="更新">
    </form>

    <div class="navigation">

        <a href="index.php">返回首頁</a>

    </div>

</body>
</html>

