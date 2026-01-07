<?php
// admin/remove_item.php
session_start();
include "../config/db.php";

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}

$message = "";

/* ---------- DELETE ITEM LOGIC ---------- */
if (isset($_GET['delete'])) {

    $item_id = (int)$_GET['delete'];

    // check if item has any orders (sold)
    $checkOrders = mysqli_query($conn,
        "SELECT id FROM orders WHERE item_id = $item_id LIMIT 1"
    );

    if (mysqli_num_rows($checkOrders) > 0) {
        $message = "Item cannot be removed because it has sales records";
    } else {

        // delete supplier supply records first
        mysqli_query($conn,
            "DELETE FROM supplier_items WHERE item_id = $item_id"
        );

        // then delete item
        mysqli_query($conn,
            "DELETE FROM items WHERE id = $item_id"
        );

        $message = "Item removed successfully";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Remove Item</title>
</head>
<body>

<h2>Remove Item</h2>

<?php if ($message != ""): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Item Name</th>
        <th>Total Quantity</th>
        <th>Sold Quantity</th>
        <th>Action</th>
    </tr>

<?php
$items = mysqli_query($conn, "SELECT * FROM items");

if (mysqli_num_rows($items) > 0) {

    while ($row = mysqli_fetch_assoc($items)) {

        echo "<tr>";
        echo "<td>".$row['item_name']."</td>";
        echo "<td>".$row['total_quantity']."</td>";
        echo "<td>".$row['sold_quantity']."</td>";
        echo "<td>
                <a href='remove_item.php?delete=".$row['id']."'
                   onclick=\"return confirm('Are you sure you want to remove this item?')\">
                   Remove
                </a>
              </td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='4'>No items found</td></tr>";
}
?>

</table>

<br>

<a href="dashboard.php">Back to Admin Dashboard</a>

</body>
</html>
