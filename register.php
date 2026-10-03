<?php
// ডাটাবেস কানেকশন ফাইলটি যুক্ত করা হলো
require 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            // 🟢 SAFE GUIDE: পাসওয়ার্ড এনক্রিপ্ট বা হ্যাশ করা হলো
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 🟢 SAFE GUIDE: SQL Injection আটকাতে Prepared Statement ব্যবহার করা হলো
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            
            if ($stmt->execute([$username, $hashed_password])) {
                $message = "<div style='color: green;'>রেজিস্ট্রেশন সফল হয়েছে! এখন লগইন করতে পারেন।</div>";
                // রেজিস্ট্রেশন সফল হলে ২ সেকেন্ড পর লগইন পেজে নিয়ে যাবে
                header("refresh:2;url=login.php");
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { // এই কোডের মানে ইউজারনেমটি আগে থেকেই আছে
                $message = "<div style='color: red;'>এই ইউজারনেমটি ইতিমধ্যে ব্যবহার করা হয়েছে!</div>";
            } else {
                $message = "<div style='color: red;'>কিছু একটা সমস্যা হয়েছে। আবার চেষ্টা করুন।</div>";
            }
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
    <title>Registration - Blog Application</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 50px; }
        .form-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { width: 100%; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #45a049; }
        .link { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>নিবন্ধন করুন (Register)</h2>
    
    <!-- সফল বা ভুলের মেসেজ দেখানোর জন্য -->
    <?php echo $message; ?>

    <form action="register.php" method="POST">
        <label for="username">ইউজারনেম (Username):</label>
        <input type="text" id="username" name="username" required autocomplete="off">

        <label for="password">পাসওয়ার্ড (Password):</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Sign Up">
    </form>
    
    <div class="link">
        <p>ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="login.php">এখানে লগইন করুন</a></p>
    </div>
</div>

</body>
</html>
