<?php
session_start();

$xml = new DOMDocument();
if (!@$xml->load('http://localhost/php/view/employerXML.php')) {
    die("Failed to load employee data.");
}
$search_id = $_POST['emp_id'];
$found = null;

$employees = $xml->getElementsByTagName('employee');
foreach ($employees as $emp) {
    $id = $emp->getElementsByTagName('emp_id')->item(0)->nodeValue;
    if ($id == $search_id) {
        $found = [
            'emp_id'   => $emp->getElementsByTagName('emp_id')->item(0)->nodeValue,
            'name'     => $emp->getElementsByTagName('name')->item(0)->nodeValue,
            'position' => $emp->getElementsByTagName('position')->item(0)->nodeValue,
            'salary'   => $emp->getElementsByTagName('salary')->item(0)->nodeValue,
        ];
        break;
    }
}
if (!$found) die("Employee not found.");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payroll System</title>
    <link rel="stylesheet" type="text/css" href="../design/sahodPage.css">
</head>
<body>
<div class="payroll-container">
    <h2>Payroll</h2>

    <table class="employee-table">
        <tr><th>ID</th><td><?= htmlspecialchars($found['emp_id']) ?></td></tr>
        <tr><th>Name</th><td><?= htmlspecialchars($found['name']) ?></td></tr>
        <tr><th>Position</th><td><?= htmlspecialchars($found['position']) ?></td></tr>
    </table>

    <div class="form-section">
        <h2>Payroll Computation</h2>
        <form action="../controller/crudcontroller.php" method="POST">
            <input type="hidden" name="action" value="compute">
            <input type="hidden" name="emp_id" value="<?= htmlspecialchars($found['emp_id']) ?>">

            <label for="daysOfWork">Days of Work:</label>
            <input type="text" id="daysOfWork" name="daysOfWork" required>

            <label for="award">Award:</label>
            <select name="award" id="award" required>
                <option value="">Select Award</option>
                <option value="No Award">No Award</option>
                <option value="Employee Of The Month">Employee of the Month</option>
                <option value="Team Player">Team Player</option>
                <option value="Perfect Attendance">Perfect Attendance</option>
            </select>

            <button type="submit" class="btn btn-primary">Compute</button>
        </form>
    </div>
</div>
</body>
</html>
