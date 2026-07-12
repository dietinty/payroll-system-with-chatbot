<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=payroll_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database" . $e->getMessage());
}
?>