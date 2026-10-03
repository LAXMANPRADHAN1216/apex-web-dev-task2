<?php
session_start();
require 'config.php';

// 🟢 SAFE GUIDE: লগইন না থাকলে পেজ অ্যাক্সেস লক
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";

// হোমপেজ থেকে পাঠানো আইডি (ID) চেক করা হচ্ছে
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // ডাটাবেস থেকে ওই নির্দিষ্ট পোস্টের ডেটা তুলে আনা হচ্ছে
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        die("পোস্টটি খুঁজে পাওয়া যায়নি!");
    }
} else {
    header("Location: index.php");
    exit;
}

// ইউজার যখন এডিট করে ফর্ম সাবমিট করবে
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));

    if (!empty($title) && !empty($content)) {
        // 🟢 SAFE GUIDE: ডাটাবেস আপডেট করার জন্য Prepared Statement
        $stmt = $pdo->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?');
        if ($stmt->execute([$title, $content, $id])) {
            header("Location: index.php");
            exit;
        } else {
            $error = "পোস্টটি আপডেট করা যায়নি। আবার চেষ্টা করুন।";
        }
    } else {
        $error = "দয়া করে সবকটি ঘর পূরণ করুন।";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Post - Blog Application</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 40px; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; resize: vertical; }
        input[type="submit"] { background-color: #ff9800; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        input[type="submit"]:hover { background-color: #e68a00; }
        .btn-back { background-color: #777; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-left: 10px; font-weight: bold; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>ব্লগ পোস্ট এডিট করুন</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="title">পোস্টের টাইটেল (Title):</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

        <label for="content">পোস্টের বিস্তারিত তথ্য (Content):</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>

        <input type="submit" value="আপডেট করুন">
        <a href="index.php" class="btn-back">বাতিল করুন</a>
    </form>
</div>

</body>
</html>
