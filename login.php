<?php
// ডাটাবেস কানেকশন ফাইল যুক্ত করা হলো
require 'config.php';

// সেশন চালু করা হলো যাতে লগইন ইনফরমেশন মনে রাখা যায়
session_start();

$message = "";

// ইউজার যদি ইতিমধ্যে লগইন করে থাকে, তবে তাকে সরাসরি হোমপেজে পাঠিয়ে দেওয়া হবে
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // 🟢 SAFE GUIDE: SQL Injection আটকাতে Prepared Statement ব্যবহার করা হলো
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // 🟢 SAFE GUIDE: password_verify দিয়ে এনক্রিপ্টেড পাসওয়ার্ড চেক করা হচ্ছে
        if ($user && password_verify($password, $user['password'])) {
            // সেশনে ইউজারের তথ্য সেভ করা হলো
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            $message = "<div style='color: green;'>লগইন সফল হয়েছে! হোমপেজে নিয়ে যাওয়া হচ্ছে...</div>";
            header("refresh:2;url=index.php");
        } else {
            $message = "<div style='color: red;'>ভুল ইউজারনেম অথবা পাসওয়ার্ড!</div>";
        }
    } else {
        $message = "<div style='color: red;'>দয়া করে সবকটি ঘর পূরণ করুন!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Login - Blog Application</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 50px; }
        .form-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { width: 100%; background-color: #008CBA; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #007B9A; }
        .link { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>লগইন করুন (Login)</h2>
    
    <?php echo $message; ?>

    <form action="login.php" method="POST">
        <label for="username">ইউজারনেম (Username):</label>
        <input type="text" id="username" name="username" required autocomplete="off">

        <label for="password">পাসওয়ার্ড (Password):</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Sign In">
    </form>
    
    <div class="link">
        <p>অ্যাকাউন্ট নেই? <a href="register.php">এখানে নতুন অ্যাকাউন্ট তৈরি করুন</a></p>
    </div>
</div>

</body>
</html>
