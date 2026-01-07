<?php
// admin/supplier_supply_report.php
session_start();
include "../config/db.php";

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}

// supplier filter (optional)
$filterSupplier = "";
if (isset($_GET['supplier_id']) && $_GET['supplier_id'] != "") {
    $supplier_id = (int)$_GET['supplier_id'];
    $filterSupplier = "WHERE supplier_items.supplier_id = $supplier_id";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Supply Report</title>
</head>
<body>

<h2>Supplier Supply Report</h2>

<!-- Supplier Filter -->
<form method="GET">
    <label>Select Supplier</label><br>
    <select name="supplier_id">
        <option value="">All Suppliers</option>
        <?php
        $suppliers = mysqli_query($conn, "SELECT id, name FROM suppliers");
        while ($s = mysqli_fetch_assoc($suppliers)) {
            $selected = (isset($supplier_id) && $supplier_id == $s['id']) ? "selected" : "";
            echo "<option value='".$s['id']."' $selected>".$s['name']."</option>";
        }
        ?>
    </select>
    <br><br>
    <button type="submit">View Report</button>
</form>

<br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Supplier Name</th>
        <th>Item Name</th>
        <th>Quantity Supplied</th>
        <th>Price / Unit</th>
        <th>Manufacturer</th>
        <th>Supply Date</th>
        <th>Best Before</th>
    </tr>

<?php
$sql = "
    SELECT
        suppliers.name AS supplier_name,
        items.item_name,
        supplier_items.quantity,
        supplier_items.price,
        supplier_items.manufacturer,
        supplier_items.supply_date,
        supplier_items.best_before
    FROM supplier_items
    JOIN suppliers ON supplier_items.supplier_id = suppliers.id
    JOIN items ON supplier_items.item_id = items.id
    $filterSupplier
    ORDER BY supplier_items.supply_date DESC
";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "<tr>";
        echo "<td>".$row['supplier_name']."</td>";
        echo "<td>".$row['item_name']."</td>";
        echo "<td>".$row['quantity']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "<td>".$row['manufacturer']."</td>";
        echo "<td>".$row['supply_date']."</td>";
        echo "<td>".$row['best_before']."</td>";
        echo "</tr>";
    }

} else {
    echo "<tr><td colspan='7'>No data found</td></tr>";
}
?>

</table>

<br>

<a href="dashboard.php">Back to Admin Dashboard</a>

</body>
</html>
