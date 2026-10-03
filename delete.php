<?php
session_start();
require 'config.php';

// 🟢 SAFE GUIDE: লগইন ছাড়া ডিলিট রিকোয়েস্ট ব্লক
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ইউজার কোন পোস্টটি ডিলিট করতে চায় তার আইডি চেক করা হচ্ছে
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // 🟢 SAFE GUIDE: SQL Injection রুখতে Prepared Statement দিয়ে ডিলিট কোয়েরি রান
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
}

// পোস্ট ডিলিট হওয়ার পর ইউজারকে সরাসরি হোমপেজে (index.php) ফেরত পাঠানো হবে
header("Location: index.php");
exit;
?>
