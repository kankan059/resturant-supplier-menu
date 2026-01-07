<?php
// supplier/dashboard.php
session_start();

// supplier protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "supplier") {
    header("Location: ../index.php");
    exit;
}

$supplierName = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Dashboard</title>
</head>
<body>

<h2>Supplier Dashboard</h2>

<p>Welcome, <?php echo $supplierName; ?></p>

<hr>

<h3>Supplier Actions</h3>

<ul>
    <li>
        <a href="supply_item.php">Supply Item</a>
    </li>
    <li>
        <a href="view_supplied_items.php">View Supplied Items</a>
    </li>
    <li><a href="expired_items.php">Expired Items</a></li>
</ul>

<hr>

<a href="../auth/logout.php">Logout</a>

</body>
</html>
