<?php
session_start();
include('db.php');

$message = "";

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $phone_number = trim($_POST['phone_number']);
    $role = $_POST['role']; // Role selected by the user
    $secret_code = isset($_POST['secret_code']) ? $_POST['secret_code'] : ''; // Secret code input

    // Validate inputs
    if (empty($username)) {
        $message = "Username is required.";
    } elseif (!$email) {
        $message = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } elseif (empty($phone_number) || !preg_match('/^\d{10}$/', $phone_number)) {
        $message = "A valid 10-digit phone number is required.";
    } elseif ($role === 'admin' && $secret_code !== 'SJCIT') { // Replace 'SJCIT' with the actual secret code
        $message = "Invalid secret code for admin registration.";
    } else {
        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email or Username already registered.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO Users (username, email, password, phone_number, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $phone_number, $role);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['role'] = $role;
                header('Location: index1.php'); // Redirect to the homepage or dashboard
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <?php if ($message) { echo "<p>$message</p>"; } ?>
	<link rel="stylesheet" href="register.css">

    <!-- Registration Form -->
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="phone_number" placeholder="Phone Number (10 digits)" required><br>
        <select name="role" required id="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br>

        <div id="admin-secret-code" style="display: none;">
            <input type="text" name="secret_code" placeholder="Secret Code"><br>
        </div>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

    <script>
        // JavaScript to toggle the display of the secret code input field based on role selection
        document.getElementById('role').addEventListener('change', function() {
            var adminSecretCodeDiv = document.getElementById('admin-secret-code');
            if (this.value === 'admin') {
                adminSecretCodeDiv.style.display = 'block';
            } else {
                adminSecretCodeDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
