<?php
// 引入資料庫連線和日誌記錄功能
require_once 'db_connect.php';
require_once 'log_message.php';

// 設定是否為除錯模式（開發時設為 true，上線時設為 false）
$isDebugMode = true;

// 建立資料庫連線
$conn = getConnection();

// 檢查連線是否正常
if (!$conn) {
    die("資料庫連線失敗");
} elseif ($isDebugMode) {
    logMessage("資料庫連線成功!");
}

// 分頁參數
$limit = 10; // 每頁顯示的記錄數量
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 查詢客戶資料
$sql = "SELECT * FROM customers LIMIT $limit OFFSET $offset";
if ($isDebugMode) {
    logMessage("SQL: " . $sql);
}

$result = $conn->query($sql);
$customers = [];

if ($result === false) {
    die("資料查詢失敗：" . $conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

if ($isDebugMode) {
    logMessage("查詢結果: " . print_r($customers, true));
}

// 獲取總記錄數以進行分頁
$totalResult = $conn->query("SELECT COUNT(*) as total FROM customers");
$total = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客戶列表</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .pagination {
            text-align: center;
            margin: 20px 0;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #3498db;
        }
        .pagination a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>客戶列表</h1>

    <div class="navigation">
        <a href="add_customer.php">新增客戶</a>
        <a href="index.php">返回首頁</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>客戶姓名</th>
                <th>聯絡資訊</th>
                <th>公司名稱</th>
                <th>備註</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($customers)): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer["id"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($customer["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($customer["contact_info"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($customer["company_name"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($customer["notes"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="edit_customer.php?id=<?php echo $customer["id"]; ?>">編輯</a> |
                            <a href="delete_customer.php?id=<?php echo $customer["id"]; ?>" onclick="confirmDelete(event, this.href)">刪除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="no-data">沒有客戶資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

    <script>
        function confirmDelete(event, url) {
            event.preventDefault(); // 阻止預設的超連結跳轉
            Swal.fire({
                title: "您確定要刪除這筆資料嗎？",
                text: "這將無法復原！",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "是的，刪除！",
                cancelButtonText: "取消"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url; // 跳轉到刪除連結
                }
            });
        }
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
