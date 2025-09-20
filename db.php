<?php
$host = 'localhost';
$db = 'dbkpdatxiabvqn';
$user = 'ubpkik01jujna';
$pass = 'f0ahnf2qsque';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
