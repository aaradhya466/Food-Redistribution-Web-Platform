<?php
session_start();
include 'db_connect.php'; // Ensure this file correctly initializes $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ngo_name = trim($_POST['ngo_name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Secure password hashing
    $phone = trim($_POST['phone']);
    $ngo_type = trim($_POST['ngo_type']);
    $address = trim($_POST['address']);
    $location = trim($_POST['location']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registration_error'] = "Invalid email format.";
        header("Location: ngo_register.html");
        exit();
    }

    // Check if email already exists in ngo_users
    $check_query = "SELECT * FROM ngo_users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['registration_error'] = "Email already registered. Please log in.";
        header("Location: ngo_register.html");
        exit();
    } else {
        // Insert new NGO user into ngo_users table
        $query = "INSERT INTO ngo_users (ngo_name, email, password, phone, ngo_type, address, location) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $ngo_name, $email, $password, $phone, $ngo_type, $address, $location);

        if ($stmt->execute()) {
            $_SESSION['registration_success'] = "Successfully registered! Please log in.";
            header("Location: ngo_login.html");
            exit();
        } else {
            $_SESSION['registration_error'] = "Registration failed. Please try again.";
            header("Location: ngo_register.html");
            exit();
        }
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>
