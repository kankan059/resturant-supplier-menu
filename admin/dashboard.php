<?php
// admin/dashboard.php
session_start();

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}

$adminName = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Admin Dashboard</h2>

<p>Welcome, <?php echo $adminName; ?></p>

<hr>

<h3>Item Management</h3>
<ul>
    <li>
        <a href="add_item.php">Add New Item</a>
    </li>
    <li>
        <a href="remove_item.php">Remove Item</a>
    </li>
</ul>

<hr>

<h3>Supplier Management</h3>
<ul>
    <li>
        <a href="view_suppliers.php">View All Suppliers</a>
    </li>
    <li>
        <a href="supplier_supply_report.php">View Supplier Supply Details</a>
    </li>
    <li>
        <a href="remove_supplier.php">Remove Supplier</a>
    </li>
</ul>

<hr>

<h3>Reports</h3>
<ul>
    <li>
        <a href="sales_report.php">Sales & Stock Report</a>
    </li>
</ul>

<hr>

<a href="../auth/logout.php">Logout</a>

</body>
</html>
