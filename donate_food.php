<?php
session_start();
include("db_connect.php");

// Ensure donor is logged in
if (!isset($_SESSION['donor_id'])) {
    header("Location: donor_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = $_SESSION['donor_id'];
    $food_description = mysqli_real_escape_string($conn, $_POST['food_description']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $expiry_date = $_POST['expiry_date'];
    $pickup_location = mysqli_real_escape_string($conn, $_POST['address']); 

    // Debugging donor_id
    if (!$donor_id) {
        echo "<script>alert('Error: Donor is not logged in.');</script>";
        exit();
    }

    // Ensure donor exists in database
    $checkDonor = mysqli_query($conn, "SELECT id FROM donors WHERE id = '$donor_id'");
    if (mysqli_num_rows($checkDonor) == 0) {
        echo "<script>alert('Error: Donor ID ($donor_id) not found.');</script>";
        exit();
    }

    // Insert into database with ngo_id and pickup_time as NULL initially
    $sql = "INSERT INTO food_donations (donor_id, food_description, quantity, expiry_date, status, ngo_id, pickup_time) 
            VALUES ('$donor_id', '$food_description', '$quantity', '$expiry_date', 'Available', NULL, NULL)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Food donation added successfully!'); window.location='donor_dashboard.html';</script>";
    } else {
        echo "<script>alert('Error inserting data: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Food - FoodBridge</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        /* Dark Theme */
        body { background-color: #121212; color: white; font-family: Arial, sans-serif; }
        
        /* Navbar */
        .navbar { background-color: #1f1f1f; padding: 12px; }
        .navbar-brand { font-size: 20px; }
        .btn-outline-light:hover { background-color: #28a745; color: white; }
        .logout-btn { background-color: #e63946; color: white; border-radius: 8px; }
        .logout-btn:hover { background-color: #c82333; }

        /* Container */
        .container { margin-top: 50px; max-width: 500px; }
        .form-control, .form-label { background-color: #1f1f1f; color: white; border: 1px solid #333; }
        .form-control:focus { background-color: #2a2a2a; color: white; border-color: #28a745; }
        .btn-primary { background-color: #28a745; border: none; transition: 0.3s; }
        .btn-primary:hover { background-color: #218838; transform: scale(1.05); }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="donor_dashboard.html">FoodBridge | Donate Food</a>
            <div>
                <a href="donor_dashboard.html" class="btn btn-outline-light me-2">Home</a>
                <a href="view_donations_made.php" class="btn btn-outline-light me-2">View Donations</a>
                <a href="donor_logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Donation Form -->
    <div class="container">
        <h2>Donate Food</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Food Description:</label>
                <input type="text" name="food_description" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity:</label>
                <input type="text" name="quantity" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Expiry Date:</label>
                <input type="date" name="expiry_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Pickup Location:</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Donate Now</button>
        </form>
    </div>

    <!-- Bootstrap 5 Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
