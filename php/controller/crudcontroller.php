<?php
require_once '../model/model.php';

switch ($_POST['action']) {
    case 'add':
        addEmployee(
            $_POST['emp_id'],
            $_POST['password'],
            $_POST['name'],
            $_POST['position'],
            $_POST['role']
        );
        header('Location: ../view/employerDashboard.php');
        exit();

    case 'update':
        updateEmployee(
            $_POST['emp_id'],
            $_POST['password'],
            $_POST['name'],
            $_POST['position'],
            $_POST['role']
        );
        header('Location: ../view/employerDashboard.php');
        exit();

    case 'delete':
        deleteEmployee($_POST['emp_id']);
        header('Location: ../view/employerDashboard.php');
        exit();

    case 'compute':
        computeSalary(
            $_POST['emp_id'],
            $_POST['daysOfWork'],
            $_POST['award']
        );
        header('Location: ../view/employerDashboard.php');
        exit();

    case 'request':
        requestSalary(
            $_POST['emp_id']
        );
        header('Location: ../view/employee.php');
        exit();    
}
?>