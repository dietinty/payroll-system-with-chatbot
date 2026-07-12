<?php

session_start();

require_once '../dbcon/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/login.php");
    exit();
}

if (empty($_POST['emp_id']) || empty($_POST['password'])) {
    header("Location: ../view/login.php?error=Please enter both Employee ID and Password");
    exit();
}

$emp_id = $_POST['emp_id'];
$password = $_POST['password'];

try {
    $sql = "SELECT * FROM employee WHERE emp_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$emp_id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if ($password == $row['password']) {
            $_SESSION['emp_id'] = $row['emp_id'];
            $_SESSION['role'] = $row['role'];

            if ($_SESSION['role'] == 'Admin') {
                header("Location: ../view/employerDashboard.php");
                exit();
            } else {
                header("Location: ../view/employee.php");
                exit();
            }
        } else {
               echo "<script>
                alert('Error: Incorrect password. Please try again.');
                window.location.href = '../view/loginPage.php'; 
              </script>";
        exit();
        }
    } else {
        echo "<script>
                alert('Error: Incorrect username. Please try again.');
                window.location.href = '../view/loginPage.php'; 
              </script>";
        exit;
    }
     
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: ../view/login.php?error=System error");
    exit();
}
?>