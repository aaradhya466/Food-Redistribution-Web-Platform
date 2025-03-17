<?php
session_start();
require 'db_connect.php'; // Database connection

if (isset($_GET['week_start']) && !empty($_GET['week_start'])) {
    // Get selected week start date from the form
    $week_start = $_GET['week_start'];
    $week_end = date("Y-m-d", strtotime($week_start . " +6 days"));
} else {
    // Default to current week's data
    $week_start = date("Y-m-d", strtotime("monday this week"));
    $week_end = date("Y-m-d", strtotime("sunday this week"));
}

// Fetch donation summary (chart data)
$queryChart = "SELECT food_donations.location, COUNT(*) AS donation_count
               FROM food_donations
               WHERE food_donations.donation_date BETWEEN ? AND ?
               GROUP BY food_donations.location";

$stmtChart = $conn->prepare($queryChart);
$stmtChart->bind_param("ss", $week_start, $week_end);
$stmtChart->execute();
$resultChart = $stmtChart->get_result();

$chartData = [];
while ($row = $resultChart->fetch_assoc()) {
    $chartData[] = $row;
}
$stmtChart->close();

// Fetch detailed donation data
$queryDetails = "SELECT donors.name AS donor_name, ngos.name AS ngo_name, food_donations.location, 
                 food_donations.donation_date, food_donations.food_quantity
                 FROM food_donations
                 JOIN donors ON food_donations.donor_id = donors.id
                 JOIN ngos ON food_donations.ngo_id = ngos.id
                 WHERE food_donations.donation_date BETWEEN ? AND ?
                 ORDER BY food_donations.donation_date DESC";

$stmtDetails = $conn->prepare($queryDetails);
$stmtDetails->bind_param("ss", $week_start, $week_end);
$stmtDetails->execute();
$resultDetails = $stmtDetails->get_result();

$data = [];
while ($row = $resultDetails->fetch_assoc()) {
    $data[] = $row;
}
$stmtDetails->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FoodBridge</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">FoodBridge Admin</a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="admin_logout.php" class="btn btn-danger navbar-btn">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center">Admin Dashboard</h2>

    <!-- Filter for selecting previous weeks -->
    <form method="GET" action="admin_dashboard.php" class="form-inline text-center">
        <label for="week">Select Week:</label>
        <input type="date" name="week_start" id="week" class="form-control" required>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <br>

    <!-- Chart Section -->
    <canvas id="donationChart"></canvas>

    <!-- Table for Donors and NGOs -->
    <h3>Donations List</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>NGO Name</th>
                <th>Location</th>
                <th>Donation Date</th>
                <th>Food Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($data as $row) {
                echo "<tr>
                        <td>{$row['donor_name']}</td>
                        <td>{$row['ngo_name']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['donation_date']}</td>
                        <td>{$row['food_quantity']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
// Fetch data from PHP
const chartData = <?php echo json_encode($chartData); ?>;

// Extract locations and donation counts
const locations = chartData.map(item => item.location);
const counts = chartData.map(item => item.donation_count);

// Create chart
const ctx = document.getElementById('donationChart').getContext('2d');
const donationChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: locations,
        datasets: [{
            label: 'Number of Donations',
            data: counts,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>
