<?php
require_once __DIR__ . '/vendor/autoload.php'; // 載入 dotenv 套件

use Dotenv\Dotenv;

// 初始化 dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'];

    $url = "https://api.openai.com/v1/chat/completions";
    $api_key = $_ENV['OPENAI_API_KEY']; // 從環境變數中獲取 API Key

    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "你是一個友好的客戶服務機器人，幫助回答常見問題。"],
            ["role" => "user", "content" => $question]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);
    $answer = $response_data['choices'][0]['message']['content'] ?? "抱歉，我無法回答您的問題。";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>機器人 FAQ</title>
    <link rel="stylesheet" href="style.css">

    <style>
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
    <header class="header">
        <h1 class="logo">常見問題 FAQ</h1>
    </header>

    <div class="navigation">

        <a href="index.php">返回首頁</a>

    </div>

    <div class="container">
        <h2>親愛的用戶，請輸入您的問題：</h2>
        <form method="POST" action="chatbot.php">
            <!-- 使用者輸入框 -->
            <textarea name="question" rows="5" cols="50" placeholder="在此輸入您的問題..."><?php echo isset($question) ? htmlspecialchars($question, ENT_QUOTES, 'UTF-8') : ''; ?></textarea><br>

            <!-- 機器人回答框 -->
            <textarea rows="10" cols="50" readonly placeholder="機器人回答將顯示在此..."><?php echo isset($answer) ? htmlspecialchars($answer, ENT_QUOTES, 'UTF-8') : ''; ?></textarea><br>
            
            <input type="submit" value="送出">
        </form>
    </div>
</body>
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <p>&copy; 2024 客戶資料管理系統</p>
            <p>用心服務 • 持續創新 • 永續經營</p>
        </div>
    </div>
</footer>
</html>
