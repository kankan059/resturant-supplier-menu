<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
$message = "";
$receipt = null;

if (isset($_POST['buy'])) {

    $item_id     = (int)$_POST['item_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $quantity    = (int)$_POST['quantity'];

    // fetch supplier item details
    $detailQ = mysqli_query($conn,"
        SELECT 
            supplier_items.price,
            supplier_items.manufacturer,
            supplier_items.best_before,
            items.item_name,
            suppliers.name AS supplier_name
        FROM supplier_items
        JOIN items ON supplier_items.item_id = items.id
        JOIN suppliers ON supplier_items.supplier_id = suppliers.id
        WHERE supplier_items.item_id = $item_id
        AND supplier_items.supplier_id = $supplier_id
    ");

    if (mysqli_num_rows($detailQ) == 1) {

        $d = mysqli_fetch_assoc($detailQ);

        $price_per_unit = $d['price'];
        $total_price = $price_per_unit * $quantity;

        // stock check
        $itemQ = mysqli_query($conn,"
            SELECT total_quantity, sold_quantity FROM items WHERE id = $item_id
        ");
        $item = mysqli_fetch_assoc($itemQ);
        $available = $item['total_quantity'] - $item['sold_quantity'];

        if ($quantity > $available) {
            $message = "Not enough stock available";
        } else {

            // insert order
            mysqli_query($conn,"
                INSERT INTO orders
                (user_id, item_id, supplier_id, quantity, price_per_unit, total_amount)
                VALUES
                ($user_id, $item_id, $supplier_id, $quantity, $price_per_unit, $total_price)
            ");

            // update sold quantity
            mysqli_query($conn,"
                UPDATE items
                SET sold_quantity = sold_quantity + $quantity
                WHERE id = $item_id
            ");

            // receipt data
            $receipt = [
                "item" => $d['item_name'],
                "supplier" => $d['supplier_name'],
                "price" => $price_per_unit,
                "quantity" => $quantity,
                "total" => $total_price,
                "manufacturer" => $d['manufacturer'],
                "best_before" => $d['best_before']
            ];
        }

    } else {
        $message = "Invalid selection";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy Item</title>
    <link rel="stylesheet" href="/assests/cs/buy.css">
    <script>
        function printReceipt() {
            var content = document.getElementById("receipt").innerHTML;
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

<h2>Buy Item</h2>

<form method="POST">

    <label>Item</label><br>
    <select name="item_id" required>
        <?php
        $items = mysqli_query($conn, "SELECT * FROM items");
        while ($i = mysqli_fetch_assoc($items)) {
            echo "<option value='".$i['id']."'>".$i['item_name']."</option>";
        }
        ?>
    </select><br><br>

    <label>Supplier</label><br>
    <select name="supplier_id" required>
        <?php
        $sup = mysqli_query($conn, "SELECT * FROM suppliers");
        while ($s = mysqli_fetch_assoc($sup)) {
            echo "<option value='".$s['id']."'>".$s['name']."</option>";
        }
        ?>
    </select><br><br>

    <label>Quantity</label><br>
    <input type="number" name="quantity" required><br><br>

    <button type="submit" name="buy">Buy</button>

</form>

<p><?php echo $message; ?></p>

<?php if ($receipt): ?>

<hr>

<div id="receipt">
    <h3>Purchase Receipt</h3>

    <p><b>Item:</b> <?php echo $receipt['item']; ?></p>
    <p><b>Supplier:</b> <?php echo $receipt['supplier']; ?></p>
    <p><b>Manufacturer:</b> <?php echo $receipt['manufacturer']; ?></p>
    <p><b>Best Before:</b> <?php echo $receipt['best_before']; ?></p>

    <hr>

    <p><b>Price per unit:</b> ₹<?php echo $receipt['price']; ?></p>
    <p><b>Quantity:</b> <?php echo $receipt['quantity']; ?></p>
    <p><b>Total Price:</b> ₹<?php echo $receipt['total']; ?></p>
</div>

<br>

<button onclick="printReceipt()">Print Receipt</button>

<?php endif; ?>

<br><br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
