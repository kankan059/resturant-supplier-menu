<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "supplier") {
    header("Location: ../index.php");
    exit;
}

$supplier_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Supplied Items</title>
</head>
<body>

<h2>My Supplied Items</h2>

<h3>Active (Not Expired)</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Manufacturer</th>
        <th>Supply Date</th>
        <th>Best Before</th>
        <th>Status</th>
    </tr>

<?php
$active = mysqli_query($conn, "
    SELECT 
        items.item_name,
        supplier_items.quantity,
        supplier_items.price,
        supplier_items.manufacturer,
        supplier_items.supply_date,
        supplier_items.best_before,
        DATEDIFF(supplier_items.best_before, CURDATE()) AS days_left
    FROM supplier_items
    JOIN items ON supplier_items.item_id = items.id
    WHERE supplier_items.supplier_id = $supplier_id
    AND supplier_items.best_before >= CURDATE()
    ORDER BY supplier_items.best_before ASC
");

if (mysqli_num_rows($active) > 0) {

    while ($row = mysqli_fetch_assoc($active)) {

        $status = ($row['days_left'] == 0)
            ? "Expires Today"
            : $row['days_left'] . " days left";

        echo "<tr>";
        echo "<td>{$row['item_name']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>{$row['price']}</td>";
        echo "<td>{$row['manufacturer']}</td>";
        echo "<td>{$row['supply_date']}</td>";
        echo "<td>{$row['best_before']}</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='7'>No active items</td></tr>";
}
?>

</table>

<hr>

<h3>Expired Items</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Manufacturer</th>
        <th>Best Before</th>
        <th>Status</th>
    </tr>

<?php
$expired = mysqli_query($conn, "
    SELECT 
        items.item_name,
        supplier_items.quantity,
        supplier_items.manufacturer,
        supplier_items.best_before,
        DATEDIFF(CURDATE(), supplier_items.best_before) AS days_expired
    FROM supplier_items
    JOIN items ON supplier_items.item_id = items.id
    WHERE supplier_items.supplier_id = $supplier_id
    AND supplier_items.best_before < CURDATE()
    ORDER BY supplier_items.best_before ASC
");

if (mysqli_num_rows($expired) > 0) {

    while ($row = mysqli_fetch_assoc($expired)) {

        echo "<tr>";
        echo "<td>{$row['item_name']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>{$row['manufacturer']}</td>";
        echo "<td>{$row['best_before']}</td>";
        echo "<td>Expired {$row['days_expired']} days ago</td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='5'>No expired items</td></tr>";
}
?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
