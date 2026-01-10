<?php
// admin/add_item.php
session_start();
include "../config/db.php";

// admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit;
}

$message = "";

if (isset($_POST['add_item'])) {

    $item_name = trim($_POST['item_name']);

    if ($item_name == "") {
        $message = "Item name is required";
    } else {

        // check duplicate item
        $check = mysqli_query($conn,
            "SELECT id FROM items WHERE item_name='$item_name'"
        );

        if (mysqli_num_rows($check) > 0) {
            $message = "Item already exists";
        } else {

            $sql = "INSERT INTO items (item_name, total_quantity, sold_quantity)
                    VALUES ('$item_name', 0, 0)";

            if (mysqli_query($conn, $sql)) {
                $message = "Item added successfully";
            } else {
                $message = "Failed to add item";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title>
    <link rel="stylesheet" href="/assests/cs/added.css">
</head>
<body>

<h2>Add New Item</h2>

<form method="POST">

    <label>Item Name</label><br>
    <input type="text" name="item_name" required><br><br>

    <button type="submit" name="add_item">Add Item</button>

</form>

<p><?php echo $message; ?></p>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
