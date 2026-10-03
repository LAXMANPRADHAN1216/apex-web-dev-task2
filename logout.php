<?php
session_start(); // সেশন চালু করা হলো
session_unset(); // সেশনের সমস্ত ভেরিয়েবল খালি করা হলো
session_destroy(); // সেশনটি পুরোপুরি ধ্বংস করা হলো

// লগআউট হওয়ার পর ইউজারকে সরাসরি লগইন পেজে পাঠিয়ে দেওয়া হবে
header("Location: login.php");
exit;
?>
