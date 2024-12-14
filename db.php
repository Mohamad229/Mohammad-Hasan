<?php
$host = 'localhost';
$db = 'BWP501_HW';
$user = 'root'; // اسم المستخدم الافتراضي في XAMPP
$password = ''; // كلمة المرور الافتراضية في XAMPP (غالبًا فارغة)

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
