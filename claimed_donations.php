<?php
session_start();
include("db_connect.php"); // Database connection

// Check if NGO is logged in
if (!isset($_SESSION['ngo_id'])) {
    header("Location: ngo_login.php");
    exit();
}

$ngo_id = $_SESSION['ngo_id'];

// Fetch the NGO's location
$ngo_query = "SELECT address FROM ngo_users WHERE id = '$ngo_id'";
$ngo_result = mysqli_query($conn, $ngo_query);
$ngo_data = mysqli_fetch_assoc($ngo_result);
$ngo_location = $ngo_data['address'];

// Fetch claimed donations for the logged-in NGO
$sql = "SELECT fd.food_description, fd.quantity, fd.expiry_date, d.address, d.phone AS contact, fd.status
        FROM food_donations fd
        JOIN donors d ON fd.donor_id = d.id
        WHERE fd.ngo_id = '$ngo_id' AND d.address = '$ngo_location'
        ORDER BY fd.created_at DESC";

$result = mysqli_query($conn, $sql);

// ✅ Check if query executed successfully
if (!$result) {
    die("Error fetching claimed donations: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claimed Donations - FoodBridge</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #121212; color: white; font-family: Arial, sans-serif; }
        .container { margin-top: 50px; }
        .table { background-color: #1f1f1f; color: white; }
        .table th, .table td { border-color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        .badge { font-size: 14px; padding: 6px 10px; }
        .badge-accepted { background-color: #17a2b8; }
        .badge-completed { background-color: #28a745; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FoodBridge | Claimed Donations</a>
            <a href="ngo_dashboard.php" class="btn btn-outline-light">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <h2>Your Claimed Donations</h2>
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Food Description</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Pickup Location</th>
                    <th>Contact</th>
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
                        <td><?php echo htmlspecialchars($row['contact']); ?></td>
                        <td>
                            <?php
                            if ($row['status'] == 'Accepted') {
                                echo '<span class="badge badge-accepted">Accepted</span>';
                            } elseif ($row['status'] == 'Completed') {
                                echo '<span class="badge badge-completed">Completed</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>
