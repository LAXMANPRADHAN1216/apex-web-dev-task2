<?php
// সেশন এবং ডাটাবেস কানেকশন যুক্ত করা হলো
session_start();
require 'config.php';

// 🟢 পেজ সিকিউরিটি চেক
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ------------------ [ পেজিনেশন সেটআপ ] ------------------
$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $limit;

// ------------------ [ সার্চ ফাংশনালিটি ] ------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
    $count_stmt->execute(["%$search%", "%$search%"]);
    $total_posts = $count_stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} else {
    $total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
}

$total_pages = ceil($total_posts / $limit);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blog Application</title>
    <!-- 🟢 ইন্টারনাল প্রফেশনাল সিএসএস ডিজাইন (কোনো ইন্টারনেটের প্রয়োজন নেই) -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .header-card { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 20px 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .header-card h2 { margin: 0; color: #0d6efd; font-size: 26px; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; cursor: pointer; transition: background 0.2s; }
        .btn-success { background: #198754; color: white; margin-right: 10px; }
        .btn-success:hover { background: #157347; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #bb2d3b; }
        .btn-warning { border: 1px solid #ff9800; color: #ff9800; background: none; margin-right: 8px; padding: 6px 12px; border-radius: 6px; }
        .btn-warning:hover { background: #ff9800; color: white; }
        .btn-outline-danger { border: 1px solid #dc3545; color: #dc3545; background: none; padding: 6px 12px; border-radius: 6px; }
        .btn-outline-danger:hover { background: #dc3545; color: white; }
        .search-box { display: flex; max-width: 500px; margin: 0 auto 30px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 30px; overflow: hidden; background: #fff; }
        .search-box input { flex: 1; padding: 12px 20px; border: none; outline: none; font-size: 15px; }
        .search-box button { padding: 12px 25px; background: #0d6efd; color: white; border: none; cursor: pointer; font-weight: bold; font-size: 15px; }
        .search-box button:hover { background: #0b5ed7; }
        .btn-secondary { background: #6c757d; color: white; border-radius: 0; display: flex; align-items: center; border: none; padding: 0 15px; text-decoration: none; }
        .post-card { background: white; border-left: 5px solid #0d6efd; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 25px; transition: transform 0.2s; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 6px 12px rgba(0,0,0,0.1); }
        .post-card h4 { margin: 0 0 12px 0; color: #212529; font-size: 22px; }
        .post-card p { color: #555; margin: 0 0 20px 0; line-height: 1.6; font-size: 16px; }
        .post-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f1f1; padding-top: 15px; }
        .post-footer small { color: #888; font-size: 13px; }
        .pagination { display: flex; list-style: none; padding: 0; justify-content: center; margin-top: 40px; }
        .pagination li { margin: 0 5px; }
        .pagination a { padding: 10px 18px; text-decoration: none; background: #fff; border: 1px solid #dee2e6; color: #0d6efd; border-radius: 6px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .pagination a:hover { background: #e9ecef; }
        .pagination .active a { background: #0d6efd; color: white; border-color: #0d6efd; }
        .pagination .disabled a { color: #6c757d; pointer-events: none; background: #e9ecef; border-color: #dee2e6; }
        .section-title { color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 10px; margin-bottom: 25px; font-size: 20px; }
    </style>
</head>
<body>

<div class="container">
    <!-- ড্যাশবোর্ড হেডার -->
    <div class="header-card">
        <h2>👋 স্বাগতম, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div>
            <a href="create.php" class="btn btn-success">+ নতুন পোস্ট লিখুন</a>
            <a href="logout.php" class="btn btn-danger">লগআউট</a>
        </div>
    </div>

    <!-- সার্চ ফর্ম ইন্টারফেস -->
    <form action="index.php" method="GET" class="search-box">
        <input type="text" name="search" placeholder="টাইটেল বা কনটেন্ট লিখে খুঁজুন..." value="<?php echo htmlspecialchars($search); ?>">
        <?php if (!empty($search)): ?>
            <a href="index.php" class="btn-secondary">মুছুন</a>
        <?php endif; ?>
        <button type="submit">সার্চ</button>
    </form>

    <!-- ব্লগ পোস্টের তালিকা প্রদর্শন -->
    <div class="section-title">সব ব্লগ পোস্ট (Total: <?php echo $total_posts; ?>)</div>

    <?php if (empty($posts)): ?>
        <div style="background: #fff3cd; color: #856404; padding: 20px; border-radius: 8px; text-align: center; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            কোনো ব্লগ পোস্ট খুঁজে পাওয়া যায়নি!
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                
                <div class="post-footer">
                    <small>📅 পোস্টের সময়: <?php echo $post['created_at']; ?></small>
                    <div>
                        <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-warning">এডিট</a>
                        <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('আপনি কি নিশ্চিত যে এই পোস্টটি ডিলিট করতে চান?');">ডিলিট</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- পেজিনেশন নেভিগেশন লিঙ্ক ইন্টারফেস -->
        <?php if ($total_pages > 1): ?>
            <ul class="pagination">
                <!-- Previous বাটন -->
                <li class="<?php if($page <= 1){ echo 'disabled'; } ?>">
                    <a href="?page=<?php echo $page - 1; ?><?php if(!empty($search)){ echo '&search='.urlencode($search); } ?>">পূর্ববর্তী</a>
                </li>
                
                <!-- পেজ নাম্বারের লুপ -->
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="<?php if($page == $i){ echo 'active'; } ?>">
                        <a href="?page=<?php echo $i; ?><?php if(!empty($search)){ echo '&search='.urlencode($search); } ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <!-- Next বাটন -->
                <li class="<?php if($page >= $total_pages){ echo 'disabled'; } ?>">
                    <a href="?page=<?php echo $page + 1; ?><?php if(!empty($search)){ echo '&search='.urlencode($search); } ?>">পরবর্তী</a>
                </li>
            </ul>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
</html>
