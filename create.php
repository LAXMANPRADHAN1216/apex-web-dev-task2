<?php
session_start();
require 'config.php';

// 🟢 SAFE GUIDE: লগইন না থাকলে পেজটি অ্যাক্সেস করা যাবে না
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 🟢 SAFE GUIDE: ইনপুট ডেটা পরিষ্কার ও ফিল্টার করা হলো
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));

    if (!empty($title) && !empty($content)) {
        // 🟢 SAFE GUIDE: SQL Injection রুখতে Prepared Statement ব্যবহার করা হলো
        $stmt = $pdo->prepare('INSERT INTO posts (title, content) VALUES (?, ?)');
        if ($stmt->execute([$title, $content])) {
            header("Location: index.php");
            exit;
        } else {
            $error = "পোস্টটি তৈরি করা যায়নি। আবার চেষ্টা করুন।";
        }
    } else {
        $error = "দয়া করে টাইটেল এবং কনটেন্ট দুটি ঘরই পূরণ করুন।";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Create Post - Blog Application</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 40px; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; resize: vertical; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        input[type="submit"]:hover { background-color: #45a049; }
        .btn-back { background-color: #777; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-left: 10px; font-weight: bold; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>নতুন ব্লগ পোস্ট তৈরি করুন</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="create.php" method="POST">
        <label for="title">পোস্টের টাইটেল (Title):</label>
        <input type="text" id="title" name="title" required placeholder="এখানে টাইটেল লিখুন...">

        <label for="content">পোস্টের বিস্তারিত তথ্য (Content):</label>
        <textarea id="content" name="content" required placeholder="এখানে আপনার ব্লগের বিস্তারিত লিখুন..."></textarea>

        <input type="submit" value="পোস্ট করুন">
        <a href="index.php" class="btn-back">ফিরে যান</a>
    </form>
</div>

</body>
</html>
