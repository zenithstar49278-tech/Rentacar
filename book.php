<?php
include 'db.php';

if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();

    if (!$car) {
        die("Car not found.");
    }
} else {
    die("No car selected.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['name'];
    $customer_email = $_POST['email'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Calculate days and total
    $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
    $total_price = $car['price_per_day'] * $days;

    // Check availability again
    $check_stmt = $pdo->prepare("SELECT * FROM bookings WHERE car_id = ? AND NOT (end_date < ? OR start_date > ?)");
    $check_stmt->execute([$car_id, $start_date, $end_date]);
    if ($check_stmt->rowCount() > 0) {
        $message = "Sorry, this car is no longer available for these dates.";
    } else {
        $insert_stmt = $pdo->prepare("INSERT INTO bookings (car_id, customer_name, customer_email, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([$car_id, $customer_name, $customer_email, $start_date, $end_date, $total_price]);
        $message = "Booking confirmed! Total: $" . number_format($total_price, 2) . ". A confirmation email would be sent in a real system.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">RentACar Pro</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Book <?php echo htmlspecialchars($car['name']); ?></h1>
        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="img-fluid mb-3" style="max-width: 400px;">
        <p class="secondary-text"><?php echo htmlspecialchars($car['description']); ?></p>
        <p>Price: $<?php echo number_format($car['price_per_day'], 2); ?> / day</p>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2025 RentACar Pro. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
