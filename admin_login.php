<?php
// Start the session
session_start();

// Hardcoded admin credentials
$admin_email = "aaradhya.82005@gmail.com";
$admin_password = "aaradhya1982";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate credentials
    if ($email === $admin_email) {
        if ($password === $admin_password) {
            // Successful login, set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $admin_email;

            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Incorrect password
            header("Location: admin_login.html?error=incorrect_password");
            exit();
        }
    } else {
        // Email not found
        header("Location: admin_login.html?error=no_account");
        exit();
    }
} else {
    // Redirect if accessed without form submission
    header("Location: admin_login.html");
    exit();
}
?>
