<?php
require_once 'db_connect.php';

$keyword = "";
$searchResult = [];
$errors = [];

// 搜尋請求
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);

    // 限制字串長度和格式
    if (strlen($keyword) > 50) {
        $errors[] = "關鍵字過長，請輸入 50 個字以內的搜尋字串。";
    } elseif (!preg_match('/^[\p{L}\p{N}\s\-_\p{Han}]+$/u', $keyword)) {
        $errors[] = "錯誤的輸入不符合關鍵字檢查。";
    } elseif (!empty($keyword)) {
        // 建立資料庫連線
        $conn = getConnection();

        // 搜尋客戶資料
        $sql = "SELECT * FROM customers WHERE id = ? OR name LIKE ?";
        $stmt = $conn->prepare($sql);

        // 如果輸入是數字，嘗試匹配 ID，否則匹配客戶名稱
        if (is_numeric($keyword)) {
            $id = intval($keyword);
            $likeKeyword = "%{$keyword}%";
            $stmt->bind_param("is", $id, $likeKeyword);
        } else {
            $id = 0; // 占位符
            $likeKeyword = "%{$keyword}%";
            $stmt->bind_param("is", $id, $likeKeyword);
        }

        // 執行 SQL 查詢
        $stmt->execute();
        $result = $stmt->get_result();

        // 處理結果
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $searchResult[] = $row;
            }
        } else {
            $errors[] = "找不到與關鍵字「" . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . "」相符的客戶資料。";
        }

        // 關閉資料庫連線
        $stmt->close();
        $conn->close();
    } else {
        $errors[] = "請輸入關鍵字進行搜尋。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>搜尋客戶資料</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-top: 50px;
            font-size: 2.5rem;
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
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #1abc9c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        td a {
            color: #3498db;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
        .no-data {
            text-align: center;
            color: #999;
        }
        #back-link {
            text-align: center;
            margin-top: 25px;
        }
        #back-link a {
            display: inline-block;
            padding: 10px 25px;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #1abc9c, #16a085);
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(26, 188, 156, 0.75);
            transition: all 0.3s ease;
        }
        #back-link a:hover {
            background: linear-gradient(135deg, #16a085, #1abc9c);
            box-shadow: 0 6px 20px rgba(26, 188, 156, 0.9);
            transform: scale(1.05);
        }

        #search-form {
            display: flex;                /* 使用 flexbox 排版 */
            justify-content: center;      /* 水平置中 */
            align-items: center;          /* 垂直置中 */
            margin-top: 20px;             /* 頁面頂部與搜索欄的間距 */
        }

        #search-form input[type="text"] {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;                 /* 設定搜索框寬度 */
        }

        #search-form input[type="submit"] {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #1abc9c;
            color: white;
            cursor: pointer;
        }

        #search-form input[type="submit"]:hover {
            background-color: #FF3737;
        }


    </style>
</head>
<body>
    <div id="container">
        <h1>搜尋客戶資料</h1>
        
        <form method="get" id="search-form">
            <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" placeholder="請輸入關鍵字">
            <input type="submit" value="搜尋">
        </form>

        <!-- 返回首頁連結 -->
        <div id="back-link">
            <a href="index.php">返回首頁</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($searchResult)): ?>
            <table border="1" id="search-result-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>客戶姓名</th>
                        <th>聯絡資訊</th>
                        <th>公司名稱</th>
                        <th>備註</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResult as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer["id"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["contact_info"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["company_name"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["notes"], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
