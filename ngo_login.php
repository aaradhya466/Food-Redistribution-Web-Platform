<?php
session_start();
include 'db_connect.php'; // Ensure this file correctly initializes $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email_id']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in all fields.";
        header("Location: ngo_login.html");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Invalid email format.";
        header("Location: ngo_login.html");
        exit();
    }

    // Query the database for the user
    $query = "SELECT * FROM ngo_users WHERE email = ?";  // Fixed column name
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists, verify password
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['ngo_id'] = $user['id'];
            $_SESSION['ngo_name'] = $user['ngo_name'];  // Fixed column name
            header("Location: ngo_dashboard.html");
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password. Please try again.";
            header("Location: ngo_login.html");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No account found with that email. Please sign up.";
        header("Location: ngo_login.html");
        exit();
    }
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
