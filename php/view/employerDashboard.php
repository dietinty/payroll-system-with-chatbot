<?php
session_start();

$xml = new DOMDocument();

if (!@$xml->load('http://localhost/php/view/employerXML.php')) {
    die("Failed to load employee data. Check your employer.php path.");
}

$employees = $xml->getElementsByTagName('employee');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../design/eD.css">
</head>
<body>

<h2>Employee List</h2>

<table class="employee-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Role</th>
            <th>Pasahod</th>
            <th>Delete</th>
            <th>Current Salary</th>
            <th>Request</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $emp): 
            $emp_id   = $emp->getElementsByTagName('emp_id')->item(0)->nodeValue;
            $name     = $emp->getElementsByTagName('name')->item(0)->nodeValue;
            $position = $emp->getElementsByTagName('position')->item(0)->nodeValue;
            $role     = $emp->getElementsByTagName('role')->item(0)->nodeValue;
            $salary   = $emp->getElementsByTagName('salary')->item(0)->nodeValue;
            $didRequest   = $emp->getElementsByTagName('didRequest')->item(0)->nodeValue;
        ?>  
        <tr>
            <td><?= htmlspecialchars($emp_id) ?></td>
            <td><?= htmlspecialchars($name) ?></td>
            <td><?= htmlspecialchars($position) ?></td>
            <td><?= htmlspecialchars($role) ?></td>
            <td>
                <form method="POST" action="sahodpage.php">
                    <input type="hidden" name="emp_id" value="<?= htmlspecialchars($emp_id) ?>">
                    <button type="submit" class="btn btn-primary">Pasahod</button>
                </form>
            </td>
            <td>
                <form method="POST" action="../controller/crudcontroller.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="emp_id" value="<?= htmlspecialchars($emp_id) ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
            <td><?= htmlspecialchars($salary) ?></td>
            <td><?= htmlspecialchars($didRequest) ?></td>
        </tr>   
        <?php endforeach; ?>
    </tbody>
</table>

<div class="form-section">
    <h2>Add Employee</h2>
    <form action="../controller/crudcontroller.php" method="POST">
        <input type="hidden" name="action" value="add">

        <label for="emp_id">Employee ID:</label>
        <input type="text" id="emp_id" name="emp_id" required>

        <label for="password">Password:</label>
        <input type="text" id="password" name="password" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="position">Position:</label>
        <input type="text" id="position" name="position" required>

        <label for="role">Role:</label>
        <input type="text" id="role" name="role" required>

        <button type="submit" class="btn btn-success">Add Employee</button>
    </form>
</div>

<div class="form-section">
    <h3>Update Employee</h3>
    <form action="../controller/crudcontroller.php" method="POST">
        <input type="hidden" name="action" value="update">

        <label for="emp_id">Employee ID:</label>
        <input type="text" id="emp_id" name="emp_id" required>

        <label for="password">Password:</label>
        <input type="text" id="password" name="password" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="position">Position:</label>
        <input type="text" id="position" name="position" required>

        <label for="role">Role:</label>
        <input type="text" id="role" name="role" required>

        <button type="submit" class="btn btn-warning">Update Employee</button>
    </form>
</div>

<form action="loginPage.php" method="POST">
    <button type="submit" name="logout_btn" class="btn btn-secondary">Log Out</button>
</form>

</body>
</html>
