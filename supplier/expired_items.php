<?php
// supplier/expired_items.php
session_start();
include "../config/db.php";

// supplier protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "supplier") {
    header("Location: ../index.php");
    exit;
}

$supplier_id = $_SESSION['id'];
$message = "";

/* ---------- REMOVE EXPIRED ITEM ---------- */
if (isset($_GET['remove'])) {

    $remove_id = (int)$_GET['remove'];

    mysqli_query($conn, "
        DELETE FROM supplier_items
        WHERE id = $remove_id
        AND supplier_id = $supplier_id
    ");

    $message = "Expired item removed successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expired Supplied Items</title>
    <link rel="stylesheet" href="/assests/cs/expired.css">
</head>
<body>
  
<h2>Expired Supplied Items</h2>

<?php if ($message != ""): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Manufacturer</th>
        <th>Best Before</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

<?php
$expired = mysqli_query($conn, "
    SELECT 
        supplier_items.id,
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

if (mysqli_num_rows($expired) > 0):

    while ($row = mysqli_fetch_assoc($expired)) {

        echo "<tr>";
        echo "<td>".$row['item_name']."</td>";
        echo "<td>".$row['quantity']."</td>";
        echo "<td>".$row['manufacturer']."</td>";
        echo "<td>".$row['best_before']."</td>";
        echo "<td>Expired ".$row['days_expired']." days ago</td>";
        echo "<td>
                <a href='expired_items.php?remove=".$row['id']."'
                   onclick=\"return confirm('Remove this expired item?')\">
                   Remove
                </a>
              </td>";
        echo "</tr>";
    }

else:
    echo "<tr><td colspan='6'>No expired items found</td></tr>";
endif;
?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
