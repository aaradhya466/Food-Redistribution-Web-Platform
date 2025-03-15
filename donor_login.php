<?php
require 'db_connect.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Prepare SQL to check email existence
        $stmt = $conn->prepare("SELECT id, password FROM donors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                session_start();
                session_regenerate_id(true); // Secure session
                $_SESSION['donor_id'] = $id;
                $_SESSION['donor_email'] = $email;
                header("Location: donor_dashboard.html");
                exit();
            } else {
                header("Location: donor_login.html?error=invalid_credentials");
                exit();

            }
        } else {
            header("Location: donor_login.html?error=user_not_found");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: donor_login.html?error=empty_fields");
        exit();
    }
}

if (isset($conn)) {
    $conn->close();
}
?>
