<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客戶資料管理系統</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="header">
        <h1 class="logo">客戶資料管理系統</h1>
    </header>

    <div class="container">
        <h2>歡迎使用客戶資料管理系統！</h2>

        <nav class="top-nav">
            <ul>
                <li><a href="add_customer.php">新增客戶</a></li>
                <li><a href="view_customers.php">客戶列表</a></li>
                <li><a href="search_customers.php">搜尋客戶</a></li>
                <li><a href="backup_history.php">備份歷史</a></li>
                <li><a href="chatbot.php">機器人FAQ</a></li>
            </ul>
        </nav>

        <!-- 跑馬燈效果的<p>內容 -->
        <div class="marquee-container">
            <p class="marquee-content">
                這裡是您的客戶管理中心，您可以新增、查看、搜尋客戶資訊，以及管理備份歷史。
                請從選單按鈕選擇您需要的功能。可以添加最新消息在這個跑馬燈中。
                1.0 版本: 時間 2024/12/19
            </p>
        </div>

    </div>

    <!-- 機器人容器 -->
    <div id="robot-container">
        <div class="robot" id="robot1"></div>
        <div class="robot" id="robot2"></div>
        <div class="robot" id="robot3"></div>
        <div class="robot" id="robot4"></div>
    </div>

    <script>
        // JavaScript 如果需要可在此處加入更多互動效果或動態功能
    </script>

</body>

<footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-slogan">
                
                </div>
                <div class="footer-divider"></div>
                <div class="footer-text">
                    <p>&copy; 2024 客戶資料管理系統</p>
                    <p>用心服務 • 持續創新 • 永續經營</p>
                </div>
            </div>
        </div>
        
</footer>

</html>
