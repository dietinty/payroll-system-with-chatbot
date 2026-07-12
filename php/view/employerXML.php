<?php
header("Content-Type: text/xml; charset=UTF-8");
require_once "../dbcon/dbcon.php";

echo "<?xml version='1.0' encoding='UTF-8'?>";
echo "<employees>";

$stmt = $pdo->prepare("SELECT emp_id, name, role, position, salary, didRequest,	currentIncentives password FROM employee");
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<employee>";
    echo "<emp_id>"   . htmlspecialchars($row['emp_id'])   . "</emp_id>";
    echo "<name>"     . htmlspecialchars($row['name'])     . "</name>";
    echo "<position>" . htmlspecialchars($row['position']) . "</position>";
    echo "<password>" . htmlspecialchars($row['password']) . "</password>";
    echo "<role>"     . htmlspecialchars($row['role'])     . "</role>";
    echo "<salary>"   . htmlspecialchars($row['salary'])     . "</salary>";
    echo "<currentIncentives>". htmlspecialchars($row['currentIncentives'])     . "</currentIncentives>";
    echo "<didRequest>". htmlspecialchars($row['didRequest'])     . "</didRequest>";
    echo "</employee>";

}

echo "</employees>";
?>

