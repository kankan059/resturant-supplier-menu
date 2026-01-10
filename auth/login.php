<?php
// index.php
session_start();
include "../config/db.php";

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === "admin") {
        $table = "admins";
        $redirect = "/admin/dashboard.php";
    } elseif ($role === "supplier") {
        $table = "suppliers";
        $redirect = "/supplier/dashboard.php";
    } else {
        $table = "users";
        $redirect = "/user/dashboard.php";
    }

    $sql = "SELECT * FROM $table WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {

        $data = mysqli_fetch_assoc($result);

        if (password_verify($password, $data['password'])) {

            $_SESSION['id'] = $data['id'];
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $data['name'];

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
    <link rel="stylesheet" href="login.css">
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
        <a href="signup.php">Create account</a>
    </p>

</body>
</html>
