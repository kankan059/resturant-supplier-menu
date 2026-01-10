<?php
// supplier/supply_item.php
session_start();
include "../config/db.php";

// supplier protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "supplier") {
    header("Location: ../index.php");
    exit;
}

$supplier_id = $_SESSION['id'];
$message = "";

if (isset($_POST['supply'])) {

    $item_id      = (int)$_POST['item_id'];
    $quantity     = (int)$_POST['quantity'];
    $price        = (float)$_POST['price'];
    $manufacturer = trim($_POST['manufacturer']);
    $supply_date  = $_POST['supply_date'];
    $best_before  = $_POST['best_before'];

    if (
        $item_id <= 0 ||
        $quantity <= 0 ||
        $price <= 0 ||
        $manufacturer == "" ||
        $supply_date == "" ||
        $best_before == ""
    ) {
        $message = "All fields are required";
    } else {

        /*
         RULE:
         1. Same supplier + item AND NOT expired  -> UPDATE
         2. Same supplier + item BUT expired      -> NEW INSERT
        */

        // check for active (not expired) record only
        $check = mysqli_query($conn, "
            SELECT id FROM supplier_items
            WHERE supplier_id = $supplier_id
            AND item_id = $item_id
            AND best_before >= CURDATE()
            LIMIT 1
        ");

        if (mysqli_num_rows($check) === 1) {

            // UPDATE existing active record
            mysqli_query($conn, "
                UPDATE supplier_items
                SET
                    quantity = quantity + $quantity,
                    price = $price,
                    manufacturer = '$manufacturer',
                    supply_date = '$supply_date',
                    best_before = '$best_before'
                WHERE supplier_id = $supplier_id
                AND item_id = $item_id
                AND best_before >= CURDATE()
            ");

            $message = "Existing item updated successfully";

        } else {

            // INSERT new record (expired or fresh supply)
            mysqli_query($conn, "
                INSERT INTO supplier_items
                (supplier_id, item_id, quantity, price, manufacturer, supply_date, best_before)
                VALUES
                ($supplier_id, $item_id, $quantity, $price, '$manufacturer', '$supply_date', '$best_before')
            ");

            $message = "New item supplied successfully";
        }

        // update total stock (always increase)
        mysqli_query($conn, "
            UPDATE items
            SET total_quantity = total_quantity + $quantity
            WHERE id = $item_id
        ");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supply Item</title>
</head>
<body>

<h2>Supply Item</h2>
<link rel="stylesheet" href="/assests/cs/supply.css">
<form method="POST">

    <label>Select Item</label><br>
    <select name="item_id" required>
        <option value="">-- Select Item --</option>
        <?php
        $items = mysqli_query($conn, "SELECT id, item_name FROM items");
        while ($row = mysqli_fetch_assoc($items)) {
            echo "<option value='".$row['id']."'>".$row['item_name']."</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Quantity</label><br>
    <input type="number" name="quantity" required>
    <br><br>

    <label>Price (per unit)</label><br>
    <input type="number" step="0.01" name="price" required>
    <br><br>

    <label>Manufacturer</label><br>
    <input type="text" name="manufacturer" required>
    <br><br>

    <label>Supply Date</label><br>
    <input type="date" name="supply_date" required>
    <br><br>

    <label>Best Before</label><br>
    <input type="date" name="best_before" required>
    <br><br>

    <button type="submit" name="supply">Supply Item</button>

</form>

<p><?php echo $message; ?></p>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
