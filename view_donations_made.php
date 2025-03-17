<?php
session_start();
include("db_connect.php"); // Database connection

// Check if donor is logged in
if (!isset($_SESSION['donor_id'])) {
    header("Location: donor_login.php");
    exit();
}

$donor_id = $_SESSION['donor_id'];

// Fetch donations for the logged-in donor
$sql = "SELECT fd.food_description, fd.quantity, fd.expiry_date, d.address, fd.status, n.ngo_name
        FROM food_donations fd
        JOIN donors d ON fd.donor_id = d.id
        LEFT JOIN ngo_users n ON fd.ngo_id = n.id
        WHERE fd.donor_id = '$donor_id'
        ORDER BY fd.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Donations - FoodBridge</title>
    
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
        .container { margin-top: 50px; }
        .table { background-color: #1f1f1f; color: white; }
        .table th, .table td { border-color: #333; }
        .badge { font-size: 14px; padding: 6px 10px; }
        .badge-available { background-color: #ffc107; color: black; }
        .badge-accepted { background-color: #17a2b8; }
        .badge-completed { background-color: #28a745; }
        .badge-expired { background-color: #dc3545; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="donor_dashboard.html">FoodBridge | View Donations</a>
            <div>
                <a href="donor_dashboard.html" class="btn btn-outline-light me-2">Home</a>
                <a href="donate_food.php" class="btn btn-outline-light me-2">Donate Food</a>
                <a href="donor_logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Your Food Donations</h2>
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Food Description</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Pickup Location</th>
                    <th>NGO</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['food_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo $row['ngo_name'] ? htmlspecialchars($row['ngo_name']) : 'Not Yet Accepted'; ?></td>
                        <td>
                            <?php
                            if ($row['status'] == 'Available') {
                                echo '<span class="badge badge-available">Available</span>';
                            } elseif ($row['status'] == 'Accepted') {
                                echo '<span class="badge badge-accepted">Accepted</span>';
                            } elseif ($row['status'] == 'Completed') {
                                echo '<span class="badge badge-completed">Completed</span>';
                            } else {
                                echo '<span class="badge badge-expired">Expired</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap 5 Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
