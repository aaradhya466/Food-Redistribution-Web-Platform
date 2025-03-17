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

// Check if NGO location query failed
if (!$ngo_result) {
    die("Error fetching NGO location: " . mysqli_error($conn));
}

$ngo_data = mysqli_fetch_assoc($ngo_result);
$ngo_location = $ngo_data['address'];

// Fetch available donations where donor's location matches NGO's location
$sql = "SELECT fd.id, fd.food_description, fd.quantity, fd.expiry_date, d.address, d.phone
        FROM food_donations fd
        JOIN donors d ON fd.donor_id = d.id
        WHERE fd.status = 'Available' AND d.address = '$ngo_location'
        ORDER BY fd.created_at DESC";


$result = mysqli_query($conn, $sql);

// Check if the donation query failed
if (!$result) {
    die("Error fetching available donations: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Donations - FoodBridge</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #121212; color: white; font-family: Arial, sans-serif; }
        .container { margin-top: 50px; }
        .table { background-color: #1f1f1f; color: white; }
        .table th, .table td { border-color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        .btn-claim { background-color: #28a745; color: white; }
        .btn-claim:hover { background-color: #218838; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FoodBridge | Available Donations</a>
            <a href="ngo_dashboard.html" class="btn btn-outline-light">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <h2>Available Food Donations</h2>
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Food Description</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Pickup Location</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['food_description']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td> 
                            <td>
                                <a href="claim_donation.php?id=<?php echo $row['id']; ?>" class="btn btn-claim">Claim</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-center">No available donations matching your location.</p>
        <?php } ?>
    </div>

</body>
</html>
