<?php
// auth/signup.php
include "../config/db.php";

$message = "";

if (isset($_POST['signup'])) {

    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $role       = $_POST['role'];
    $contact_no = trim($_POST['contact_no']);

    if ($name == "" || $email == "" || $password == "" || $role == "") {
        $message = "All fields are required";
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($role === "admin") {
            $table = "admins";
        } elseif ($role === "supplier") {
            $table = "suppliers";
        } else {
            $table = "users";
        }

        // check email already exists
        $check = mysqli_query($conn, "SELECT id FROM $table WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $message = "Email already registered";
        } else {

            // supplier ke liye contact number bhi save hoga
            if ($role === "supplier") {

                if ($contact_no == "") {
                    $message = "Contact number is required for supplier";
                } else {

                    $sql = "
                        INSERT INTO suppliers (name, email, password, contact_no)
                        VALUES ('$name', '$email', '$hashedPassword', '$contact_no')
                    ";

                    if (mysqli_query($conn, $sql)) {
                        $message = "Supplier signup successful. You can login now.";
                    } else {
                        $message = "Signup failed";
                    }
                }

            } else {

                // admin & user
                $sql = "
                    INSERT INTO $table (name, email, password)
                    VALUES ('$name', '$email', '$hashedPassword')
                ";

                if (mysqli_query($conn, $sql)) {
                    $message = ucfirst($role) . " signup successful. You can login now.";
                } else {
                    $message = "Signup failed";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>

    <script>
        function toggleContact() {
            var role = document.getElementById("role").value;
            var contactBox = document.getElementById("contactBox");

            if (role === "supplier") {
                contactBox.style.display = "block";
            } else {
                contactBox.style.display = "none";
            }
        }
    </script>
</head>
<body>

<h2>Signup</h2>

<form method="POST">

    <label>Name</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <label>Signup As</label><br>
    <select name="role" id="role" onchange="toggleContact()" required>
        <option value="">-- Select Role --</option>
        <option value="admin">Admin</option>
        <option value="supplier">Supplier</option>
        <option value="user">User</option>
    </select><br><br>

    <div id="contactBox" style="display:none;">
        <label>Contact Number (Supplier)</label><br>
        <input type="text" name="contact_no"><br><br>
    </div>

    <button type="submit" name="signup">Signup</button>

</form>

<p><?php echo $message; ?></p>

<a href="../index.php">Go to Login</a>

</body>
</html>
