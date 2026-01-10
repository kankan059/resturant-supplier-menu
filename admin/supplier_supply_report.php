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
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", system-ui, sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc, #eef2f7);
    padding: 3rem 1rem;
    color: #111827;
}

/* Page Title */
h2 {
    text-align: center;
    font-size: 2.1rem;
    font-weight: 600;
    margin-bottom: 1.8rem;
}

/* Filter Form */
form {
    max-width: 520px;
    margin: 0 auto 2rem;
    background: #ffffff;
    padding: 1.5rem 1.8rem;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    text-align: center;
}

/* Label */
label {
    font-size: 0.9rem;
    font-weight: 500;
    color: #374151;
}

/* Select */
select {
    width: 100%;
    margin-top: 0.6rem;
    padding: 0.7rem 0.9rem;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    font-size: 0.95rem;
    color: #111827;
    outline: none;
    transition: all 0.25s ease;
}

select:focus {
    background: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

/* Button */
button {
    margin-top: 1.2rem;
    padding: 0.75rem 1.6rem;
    border-radius: 14px;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
    transition: all 0.3s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(37, 99, 235, 0.45);
}

/* Table */
table {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto 2rem;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.08);
}

/* Head */
th {
    background: #f1f5f9;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1f2937;
    padding: 0.9rem;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}

/* Body */
td {
    padding: 0.85rem 0.9rem;
    font-size: 0.95rem;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

tr:last-child td {
    border-bottom: none;
}

/* Hover */
tr:hover td {
    background: #f9fafb;
}

/* Empty */
td[colspan] {
    text-align: center;
    color: #6b7280;
    padding: 1.2rem;
}

/* Back link */
a[href*="dashboard"] {
    display: block;
    max-width: 300px;
    margin: 2.5rem auto 0;
    text-align: center;
    padding: 0.75rem;
    border-radius: 14px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
    transition: all 0.3s ease;
}

a[href*="dashboard"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(37, 99, 235, 0.45);
}

/* Mobile */
@media (max-width: 768px) {
    table {
        font-size: 0.85rem;
    }

    th, td {
        padding: 0.7rem;
    }

    h2 {
        font-size: 1.8rem;
    }
}

    </style>

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
