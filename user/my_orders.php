<?php
// user/my_orders.php
session_start();
include "../config/db.php";

// user protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>

    <script>
        function toggleDetails(id) {
            var box = document.getElementById("details-" + id);
            box.style.display = (box.style.display === "none") ? "block" : "none";
        }

        function printReceipt(id) {
            var content = document.getElementById("details-" + id).innerHTML;
            var win = window.open("", "", "width=600,height=600");
            win.document.write("<html><head><title>Receipt</title></head><body>");
            win.document.write(content);
            win.document.write("</body></html>");
            win.document.close();
            win.print();
        }
    </script>
</head>
<body>

<h2>My Orders</h2>

<?php
$orders = mysqli_query($conn, "
    SELECT
        orders.id,
        orders.order_date,
        orders.quantity,
        orders.price_per_unit,
        orders.total_amount,

        items.item_name,

        suppliers.name AS supplier_name,
        suppliers.email AS supplier_email,
        suppliers.contact_no,

        supplier_items.manufacturer,
        supplier_items.best_before

    FROM orders
    JOIN items ON orders.item_id = items.id
    JOIN suppliers ON orders.supplier_id = suppliers.id
    JOIN supplier_items
        ON supplier_items.item_id = orders.item_id
        AND supplier_items.supplier_id = orders.supplier_id

    WHERE orders.user_id = $user_id
    ORDER BY orders.order_date DESC
");

if (mysqli_num_rows($orders) > 0):

    while ($row = mysqli_fetch_assoc($orders)):
?>

<hr>

<!-- ORDER SUMMARY (CLICKABLE) -->
<div onclick="toggleDetails(<?php echo $row['id']; ?>)" style="cursor:pointer;">
    <p>
        <b>Order ID:</b> <?php echo $row['id']; ?> |
        <b>Item:</b> <?php echo $row['item_name']; ?> |
        <b>Total:</b> ₹<?php echo $row['total_amount']; ?> |
        <b>Ordered At:</b> <?php echo $row['order_date']; ?>
    </p>
</div>

<!-- ORDER DETAILS -->
<div id="details-<?php echo $row['id']; ?>" style="display:none; margin-left:20px;">

    <h4>Supplier Details</h4>
    <p><b>Name:</b> <?php echo $row['supplier_name']; ?></p>
    <p><b>Email:</b> <?php echo $row['supplier_email']; ?></p>
    <p><b>Contact No:</b> <?php echo $row['contact_no']; ?></p>

    <hr>

    <p><b>Manufacturer:</b> <?php echo $row['manufacturer']; ?></p>
    <p><b>Best Before:</b> <?php echo $row['best_before']; ?></p>

    <hr>

    <p><b>Price per unit:</b> ₹<?php echo $row['price_per_unit']; ?></p>
    <p><b>Quantity:</b> <?php echo $row['quantity']; ?></p>
    <p><b>Total Amount:</b> ₹<?php echo $row['total_amount']; ?></p>

    <button onclick="printReceipt(<?php echo $row['id']; ?>)">Print Receipt</button>

</div>

<?php
    endwhile;
else:
    echo "<p>No orders found</p>";
endif;
?>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
