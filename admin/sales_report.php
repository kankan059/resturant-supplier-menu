<?php
// admin/sales_report.php
session_start();
include "../config/db.php";

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales & Stock Report</title>
</head>
<body>

<h2>Sales & Stock Report</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Item Name</th>
        <th>Total Quantity</th>
        <th>Sold Quantity</th>
        <th>Remaining Quantity</th>
    </tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM items");

while ($row = mysqli_fetch_assoc($result)) {

    $remaining = $row['total_quantity'] - $row['sold_quantity'];

    echo "<tr>";
    echo "<td>".$row['item_name']."</td>";
    echo "<td>".$row['total_quantity']."</td>";
    echo "<td>".$row['sold_quantity']."</td>";
    echo "<td>".$remaining."</td>";
    echo "</tr>";
}
?>
</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
