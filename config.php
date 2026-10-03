<?php
// ডাটাবেস কানেকশন ইনফরমেশন
$host = 'localhost';
$db   = 'blog';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // PDO কানেকশন তৈরি করা (SQL Injection থেকে বাঁচার নিরাপদ উপায়)
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // কানেকশন এরর হলে মেসেজ দেখাবে
     die("ডাটাবেস কানেকশন ব্যর্থ হয়েছে: " . $e->getMessage());
}
?>
