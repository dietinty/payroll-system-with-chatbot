<?php
require_once "../dbcon/dbcon.php";
require_once "../controller/crudcontroller.php";

function addEmployee($emp_id, $password, $name, $position, $role) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO employee (emp_id, password, name, position, role) VALUES (:emp_id, :password, :name, :position, :role)");
    $stmt->execute(['emp_id' => $emp_id, 'password' => $password,'name' => $name,'position' => $position, 'role' => $role]);
}

function updateEmployee($emp_id, $password, $name, $position, $role) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE employee SET password = :password, name = :name, position = :position, role = :role WHERE emp_id = :emp_id");
    $stmt->execute(['emp_id'   => $emp_id, 'password' => $password, 'name' => $name, 'position' => $position, 'role' => $role]);
}

function deleteEmployee($emp_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM employee WHERE emp_id = :emp_id");
    $stmt->execute(['emp_id' => $emp_id]);
}

function computeSalary($emp_id, $daysOfWork, $award) { 
    global $pdo;

    $stmt = $pdo->prepare("SELECT position FROM employee WHERE emp_id = :emp_id");
    $stmt->execute(['emp_id' => $emp_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT rate FROM job_positions WHERE job = :job");
    $stmt2->execute(['job' => $employee['position']]);
    $job = $stmt2->fetch(PDO::FETCH_ASSOC);

    $baseSalary = ($job['rate'] * 8) * $daysOfWork;
    
    $bonus = 0;
    switch ($award) {
        case 'Employee Of The Month':
            $bonus = 5000; 
            break;
        case 'Team Player':
            $bonus = 2000;
            break;
        case 'Perfect Attendace':
            $bonus = 3000;
            break;
        case 'No Award':
        default:
            $bonus = 0;
            break;
    }
    
    $finalSalary = $baseSalary + $bonus;

    $stmt3 = $pdo->prepare("UPDATE employee SET    salary = :salary,  didRequest = 'Payroll Deposited',  currentIncentives = :award   WHERE emp_id = :emp_id");
    $stmt3->execute(['salary' => $finalSalary, 'award'  => $award,'emp_id' => $emp_id]);
    
    return $finalSalary;
}

function requestSalary() {
    global $pdo;
    $emp_id = $_POST['reqSalary'];
    $stmt = $pdo->prepare("UPDATE employee SET didRequest = 'Request Salary' WHERE emp_id = :emp_id");
    $stmt->execute(['emp_id' => $emp_id]);
}

?>