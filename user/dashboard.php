<?php
// user/dashboard.php
session_start();

// user protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: ../index.php");
    exit;
}

$userName = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>

<h2>User Dashboard</h2>

<p>Welcome, <?php echo $userName; ?></p>

<hr>

<h3>User Actions</h3>

<ul>
    <li>
        <a href="buy_item.php">Buy Items</a>
    </li>
    <li>
        <a href="my_orders.php">My Orders</a>
    </li>
</ul>

<hr>

<a href="../auth/logout.php">Logout</a>

</body>
</html>
