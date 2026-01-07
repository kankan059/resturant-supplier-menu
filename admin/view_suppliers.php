<?php
// admin/view_suppliers.php
session_start();
include "../config/db.php";

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}

/* ---------- SUPPLIER DELETE LOGIC ---------- */
if (isset($_GET['delete'])) {

    $supplier_id = (int)$_GET['delete'];

    // first delete supplier supplied items
    mysqli_query($conn,
        "DELETE FROM supplier_items WHERE supplier_id = $supplier_id"
    );

    // then delete supplier
    mysqli_query($conn,
        "DELETE FROM suppliers WHERE id = $supplier_id"
    );

    header("Location: view_suppliers.php");
    exit;
}

/* ---------- SUPPLIER SUPPLY VIEW ---------- */
$viewSupplierId = null;
if (isset($_GET['view'])) {
    $viewSupplierId = (int)$_GET['view'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Suppliers</title>
</head>
<body>

<h2>Suppliers List</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Supplier Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

<?php
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers");

if (mysqli_num_rows($suppliers) > 0) {

    while ($s = mysqli_fetch_assoc($suppliers)) {

        echo "<tr>";
        echo "<td>
                <a href='view_suppliers.php?view=".$s['id']."'>
                    ".$s['name']."
                </a>
              </td>";
        echo "<td>".$s['email']."</td>";
        echo "<td>
                <a href='view_suppliers.php?delete=".$s['id']."'
                   onclick=\"return confirm('Remove this supplier?')\">
                   Remove
                </a>
              </td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='3'>No suppliers found</td></tr>";
}
?>

</table>

<hr>

<?php if ($viewSupplierId): ?>

<h3>Supplied Items</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Manufacturer</th>
        <th>Supply Date</th>
        <th>Best Before</th>
    </tr>

<?php
$supply = mysqli_query($conn, "
    SELECT 
        items.item_name,
        supplier_items.quantity,
        supplier_items.price,
        supplier_items.manufacturer,
        supplier_items.supply_date,
        supplier_items.best_before
    FROM supplier_items
    JOIN items ON supplier_items.item_id = items.id
    WHERE supplier_items.supplier_id = $viewSupplierId
");

if (mysqli_num_rows($supply) > 0) {

    while ($row = mysqli_fetch_assoc($supply)) {

        echo "<tr>";
        echo "<td>".$row['item_name']."</td>";
        echo "<td>".$row['quantity']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "<td>".$row['manufacturer']."</td>";
        echo "<td>".$row['supply_date']."</td>";
        echo "<td>".$row['best_before']."</td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='6'>No supply data found</td></tr>";
}
?>

</table>

<?php endif; ?>

<br>

<a href="dashboard.php">Back to Admin Dashboard</a>

</body>
</html>
