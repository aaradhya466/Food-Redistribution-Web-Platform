<?php
session_start();
require 'db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $donor_type = $_POST['donor_type'];
    $address = $_POST['address'];
    $location = $_POST['location'];

    // Validate phone number
    if (!is_numeric($phone)) {
        die("Invalid phone number. It must be numeric.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO donors (name, email, password, phone, donor_type, address, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $name, $email, $hashed_password, $phone, $donor_type, $address, $location);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['registered_success'] = true;
        header("Location: donor_login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
