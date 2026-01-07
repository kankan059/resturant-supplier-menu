<?php
// admin/expiry_alert.php
session_start();
include "../config/db.php";

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}

// alert window (days)
$alert_days = 7;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expiry Alert</title>
</head>
<body>

<h2>Expiry Alert (Next <?php echo $alert_days; ?> Days)</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Item Name</th>
        <th>Supplier</th>
        <th>Contact</th>
        <th>Manufacturer</th>
        <th>Quantity</th>
        <th>Best Before</th>
        <th>Status</th>
    </tr>

<?php
$sql = "
    SELECT
        items.item_name,
        suppliers.name AS supplier_name,
        suppliers.email,
        suppliers.contact_no,
        supplier_items.manufacturer,
        supplier_items.quantity,
        supplier_items.best_before,
        DATEDIFF(supplier_items.best_before, CURDATE()) AS days_left
    FROM supplier_items
    JOIN items ON supplier_items.item_id = items.id
    JOIN suppliers ON supplier_items.supplier_id = suppliers.id
    WHERE supplier_items.best_before <= DATE_ADD(CURDATE(), INTERVAL $alert_days DAY)
    ORDER BY supplier_items.best_before ASC
";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0):

    while ($row = mysqli_fetch_assoc($result)) {

        if ($row['days_left'] < 0) {
            $status = "Expired";
        } elseif ($row['days_left'] == 0) {
            $status = "Expires Today";
        } else {
            $status = $row['days_left'] . " days left";
        }

        echo "<tr>";
        echo "<td>".$row['item_name']."</td>";
        echo "<td>".$row['supplier_name']."</td>";
        echo "<td>".$row['email']." / ".$row['contact_no']."</td>";
        echo "<td>".$row['manufacturer']."</td>";
        echo "<td>".$row['quantity']."</td>";
        echo "<td>".$row['best_before']."</td>";
        echo "<td>".$status."</td>";
        echo "</tr>";
    }

else:
    echo "<tr><td colspan='7'>No expiry alerts</td></tr>";
endif;
?>

</table>

<br>

<a href="dashboard.php">Back to Admin Dashboard</a>

</body>
</html>
