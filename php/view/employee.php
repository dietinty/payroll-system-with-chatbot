<?php
session_start();
include 'chat_widget.php';
if (!isset($_SESSION['emp_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../dbcon/dbcon.php';

$emp_id = $_SESSION['emp_id'];
$employee = null;

try {
    $sql = "SELECT * FROM employee WHERE emp_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$emp_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$employee) {
        session_destroy();
        header("Location: login.php?error=Employee record not found");
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Employee fetch error: " . $e->getMessage());
    die("System error, please try again later");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../design/employee.css">
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($employee['name'] ?? $employee['first_name'] ?? $emp_id); ?>!</h2>

    <h3>Your Employee Details</h3>

    <table class="employee-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Role</th>
                <th>Current Salary</th>
                <th>Your Current Incentives</th>
                <th>Request Salary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($employee['emp_id']); ?></td>
                <td>
                    <?php 
                    if (isset($employee['first_name']) && isset($employee['last_name'])) {
                        echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']);
                    } elseif (isset($employee['name'])) {
                        echo htmlspecialchars($employee['name']);
                    } else {
                        echo htmlspecialchars($emp_id);
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($employee['position'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($employee['role'] ?? 'Employee'); ?></td>
                <td><?php echo htmlspecialchars($employee['salary'] ?? '0'); ?></td>
                <td><?php echo htmlspecialchars($employee['currentIncentives'] ?? '0'); ?></td>
                <td>
                    <form method="POST" action="../controller/crudcontroller.php">
                        <input type="hidden" name="action" value="request">
                        <input type="hidden" name="reqSalary" value="<?php echo htmlspecialchars($employee['emp_id']); ?>">
                        <button type="submit" class="btn btn-primary">Request Salary</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>

    <form action="loginPage.php" method="POST">
        <button type="submit" name="logout_btn" class="btn btn-secondary">Log Out</button>
    </form>
</div>

</body>
</html>
