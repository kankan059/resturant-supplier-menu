<?php
// index.php
session_start();
include "config/db.php";

// agar already login hai to redirect kar do
if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] === "admin") {
        header("Location: admin/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] === "supplier") {
        header("Location: supplier/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] === "user") {
        header("Location: user/dashboard.php");
        exit;
    }
}

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === "admin") {
        $table = "admins";
        $redirect = "admin/dashboard.php";
    } elseif ($role === "supplier") {
        $table = "suppliers";
        $redirect = "supplier/dashboard.php";
    } else {
        $table = "users";
        $redirect = "user/dashboard.php";
    }

    $sql = "SELECT * FROM $table WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {

        $data = mysqli_fetch_assoc($result);

        if (password_verify($password, $data['password'])) {

            $_SESSION['id'] = $data['id'];
            $_SESSION['name'] = $data['name'];
            $_SESSION['role'] = $role;

            header("Location: $redirect");
            exit;

        } else {
            $message = "Invalid password";
        }

    } else {
        $message = "Account not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- CSS FILE LINK -->
    <link rel="stylesheet" href="assests/cs/auth.css">
</head>
<body>

    <h2>Login</h2>

    <form method="POST">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Login As</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="supplier">Supplier</option>
            <option value="user">User</option>
        </select>

        <button type="submit" name="login">Login</button>

    </form>

    <p><?php echo $message; ?></p>

    <p>
        New user?
        <a href="auth/signup.php">Create account</a>
    </p>

</body>
</html>
