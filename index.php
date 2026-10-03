<?php
// সেশন এবং ডাটাবেস কানেকশন যুক্ত করা হলো
session_start();
require 'config.php';

// 🟢 SAFE GUIDE: ইউজার লগইন না থাকলে তাকে লগইন পেজে পাঠিয়ে দেওয়া হবে
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ডাটাবেস থেকে সব পোস্ট নতুন থেকে পুরনো ক্রমানুসারে (Descending Order) নিয়ে আসা হলো
$stmt = $pdo->query('SELECT * FROM posts ORDER BY created_at DESC');
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Blog Application</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .btn { padding: 10px 15px; text-decoration: none; border-radius: 4px; color: white; font-weight: bold; }
        .btn-create { background-color: #4CAF50; }
        .btn-logout { background-color: #f44336; }
        .btn-edit { background-color: #ff9800; padding: 5px 10px; font-size: 14px; }
        .btn-delete { background-color: #f44336; padding: 5px 10px; font-size: 14px; }
        .post-card { background: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 5px solid #008CBA; border-radius: 4px; }
        .post-actions { margin-top: 10px; }
        .no-posts { text-align: center; color: #777; margin: 40px 0; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <!-- 🟢 SAFE GUIDE: htmlspecialchars দিয়ে লগইন থাকা ইউজারের নাম প্রিন্ট করা হলো -->
        <h2>স্বাগতম, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div>
            <a href="create.php" class="btn btn-create">+ নতুন পোস্ট লিখুন</a>
            <a href="logout.php" class="btn btn-logout">লগআউট</a>
        </div>
    </div>

    <h3>সব ব্লগ পোস্ট (Blog Posts)</h3>

    <?php if (empty($posts)): ?>
        <p class="no-posts">এখনো কোনো ব্লগ পোস্ট তৈরি করা হয়নি। ওপরে ক্লিক করে প্রথম পোস্টটি তৈরি করুন!</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <!-- 🟢 SAFE GUIDE: XSS Attack রুখতে htmlspecialchars ব্যবহার করা হয়েছে -->
                <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <small style="color: #888;">পোস্টের সময়: <?php echo $post['created_at']; ?></small>
                
                <div class="post-actions">
                    <!-- এডিট এবং ডিলিটের জন্য নির্দিষ্ট পোস্টের ID লিঙ্কে পাঠানো হচ্ছে -->
                    <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">এডিট (Edit)</a>
                    <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-delete" onclick="return confirm('আপনি কি নিশ্চিত যে এই পোস্টটি ডিলিট করতে চান?');">ডিলিট (Delete)</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
