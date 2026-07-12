<!DOCTYPE html>
<html>
<head>
    <title>Payroll System Login Page</title>
    <link rel="stylesheet" type="text/css" href="../design/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Payroll System</h1>
        <form action="../controller/controller.php" method="POST">
            <label for="emp_id">Employee Number</label>
            <input type="text" name="emp_id" id="emp_id" placeholder="Enter your employee number" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
