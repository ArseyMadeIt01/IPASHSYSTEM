<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "username", "password", "database_name");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle report filtering
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'all';

// Handle report search
$searchQuery = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// SQL query to fetch reports based on type and search query
$sql = "SELECT * FROM reports WHERE (type = ? OR ? = 'all') AND (title LIKE ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $reportType, $reportType, $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .report-item {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">View Reports</h1>
                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <section class="max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold mb-6">Reports Overview</h2>
                
                <!-- Filter and Search -->
                <form method="GET" class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex flex-col md:flex-row md:items-center">
                        <label for="report-type" class="mr-2 font-semibold">Filter by Type:</label>
                        <select id="report-type" name="report_type" class="bg-white border border-gray-300 rounded-lg px-4 py-2">
                            <option value="all">All</option>
                            <option value="appointments" <?= $reportType === 'appointments' ? 'selected' : '' ?>>Appointments</option>
                            <option value="health-metrics" <?= $reportType === 'health-metrics' ? 'selected' : '' ?>>Health Metrics</option>
                            <option value="system-usage" <?= $reportType === 'system-usage' ? 'selected' : '' ?>>System Usage</option>
                        </select>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <input type="text" name="search" placeholder="Search Reports" value="<?= htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '') ?>" class="bg-white border border-gray-300 rounded-lg px-4 py-2 w-full md:w-64" />
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4 md:mt-0">Apply Filters</button>
                </form>

                <!-- Reports List -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="report-item">
                                <h3 class="text-xl font-bold mb-4"><?= htmlspecialchars($row['title']) ?></h3>
                                <p class="font-semibold">Report Date: <?= htmlspecialchars($row['date']) ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($row['description']) ?></p>
                                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="window.location.href='report_details.php?id=<?= $row['id'] ?>'">View Details</button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-600">No reports found matching your criteria.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.getElementById('report-type').addEventListener('change', function () {
            this.form.submit();
        });
    </script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>
