<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Rental - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">RentACar Pro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="cars.php">Browse Cars</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Find Your Perfect Rental Car</h1>
        <form action="cars.php" method="GET" class="search-form row g-3 justify-content-center mt-4">
            <div class="col-md-3">
                <input type="text" name="location" class="form-control" placeholder="Pickup Location" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>

        <h2 class="mt-5">Featured Cars</h2>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT * FROM cars LIMIT 4");
            while ($car = $stmt->fetch()) {
                echo '
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="' . htmlspecialchars($car['image_url']) . '" class="card-img-top" alt="' . htmlspecialchars($car['name']) . '">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($car['name']) . '</h5>
                            <p class="card-text secondary-text">' . htmlspecialchars($car['description']) . '</p>
                            <p class="card-text">$'. number_format($car['price_per_day'], 2) .' / day</p>
                            <a href="book.php?car_id=' . $car['id'] . '" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; 2025 RentACar Pro. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
