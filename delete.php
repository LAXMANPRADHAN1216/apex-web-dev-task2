<?php
session_start();
require 'config.php';

// 🟢 সিকিউরিটি চেক ১: ইউজার লগইন না থাকলে সরাসরি লগইন পেজে রিডাইরেক্ট
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

   $user_stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $current_user = $user_stmt->fetch();
    $user_role = $current_user ? $current_user['role'] : 'editor';


// 🟢 TASK 4 NEW (RBAC): রোল যদি 'admin' না হয়, তবে ডিলিট অ্যাকশন পুরোপুরি ব্লক (Access Denied)
if ($user_role !== 'admin') {
    echo "<div style='color: red; font-family: Arial; text-align: center; margin-top: 50px;'>";
    echo "<h2>🚫 অ্যাক্সেস ডিনাইড (Access Denied)</h2>";
    echo "<p>আপনার কাছে এই পোস্টটি ডিলিট করার অনুমতি নেই। শুধুমাত্র অ্যাডমিনরাই ডিলিট করতে পারবেন।</p>";
    echo "<a href='index.php'>ড্যাশবোর্ডে ফিরে যান</a>";
    echo "</div>";
    exit;
}

// ইউজার অ্যাডমিন হলে তবেই নিচের ডিলিট কোডটি এক্সিকিউট হবে
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // SQL Injection রুখতে Prepared Statement দিয়ে ডিলিট করা
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
}

// ডিলিট সফল হওয়ার পর সরাসরি হোমপেজে ফেরত পাঠানো
header("Location: index.php");
exit;
?>
