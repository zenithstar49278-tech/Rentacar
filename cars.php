<?php
include 'db.php';

$location = isset($_GET['location']) ? $_GET['location'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$fuel = isset($_GET['fuel']) ? $_GET['fuel'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : 10000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';

$where = "WHERE 1=1";
if ($location) $where .= " AND location LIKE :location";
if ($type) $where .= " AND type = :type";
if ($fuel) $where .= " AND fuel = :fuel";
if ($brand) $where .= " AND brand = :brand";
$where .= " AND price_per_day BETWEEN :min_price AND :max_price";

if ($start_date && $end_date) {
    $where .= " AND id NOT IN (SELECT car_id FROM bookings WHERE NOT (end_date < :start_date OR start_date > :end_date))";
}

$order = ($sort == 'price_asc') ? 'price_per_day ASC' : 'price_per_day DESC';

$stmt = $pdo->prepare("SELECT * FROM cars $where ORDER BY $order");
if ($location) $stmt->bindValue(':location', "%$location%");
if ($type) $stmt->bindValue(':type', $type);
if ($fuel) $stmt->bindValue(':fuel', $fuel);
if ($brand) $stmt->bindValue(':brand', $brand);
$stmt->bindValue(':min_price', $min_price);
$stmt->bindValue(':max_price', $max_price);
if ($start_date && $end_date) {
    $stmt->bindValue(':start_date', $start_date);
    $stmt->bindValue(':end_date', $end_date);
}
$stmt->execute();
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">RentACar Pro</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Available Cars</h1>
        <form method="GET" class="row g-3 mb-4">
            <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Car Type</option>
                    <option value="Sedan" <?php if($type=='Sedan') echo 'selected'; ?>>Sedan</option>
                    <option value="SUV" <?php if($type=='SUV') echo 'selected'; ?>>SUV</option>
                    <option value="Sports" <?php if($type=='Sports') echo 'selected'; ?>>Sports</option>
                    <option value="Luxury" <?php if($type=='Luxury') echo 'selected'; ?>>Luxury</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="fuel" class="form-select">
                    <option value="">Fuel Type</option>
                    <option value="Petrol" <?php if($fuel=='Petrol') echo 'selected'; ?>>Petrol</option>
                    <option value="Diesel" <?php if($fuel=='Diesel') echo 'selected'; ?>>Diesel</option>
                    <option value="Electric" <?php if($fuel=='Electric') echo 'selected'; ?>>Electric</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="brand" class="form-select">
                    <option value="">Brand</option>
                    <option value="Ferrari" <?php if($brand=='Ferrari') echo 'selected'; ?>>Ferrari</option>
                    <option value="Porsche" <?php if($brand=='Porsche') echo 'selected'; ?>>Porsche</option>
                    <option value="Tesla" <?php if($brand=='Tesla') echo 'selected'; ?>>Tesla</option>
                    <option value="BMW" <?php if($brand=='BMW') echo 'selected'; ?>>BMW</option>
                    <option value="Toyota" <?php if($brand=='Toyota') echo 'selected'; ?>>Toyota</option>
                    <option value="Honda" <?php if($brand=='Honda') echo 'selected'; ?>>Honda</option>
                    <option value="Audi" <?php if($brand=='Audi') echo 'selected'; ?>>Audi</option>
                    <option value="Rolls Royce" <?php if($brand=='Rolls Royce') echo 'selected'; ?>>Rolls Royce</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="<?php echo $min_price; ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="<?php echo $max_price; ?>">
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="price_asc" <?php if($sort=='price_asc') echo 'selected'; ?>>Price Low to High</option>
                    <option value="price_desc" <?php if($sort=='price_desc') echo 'selected'; ?>>Price High to Low</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="row">
            <?php foreach ($cars as $car): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($car['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($car['name']); ?></h5>
                            <p class="card-text secondary-text"><?php echo htmlspecialchars($car['description']); ?></p>
                            <p class="card-text">$<?php echo number_format($car['price_per_day'], 2); ?> / day</p>
                            <p class="card-text secondary-text">Location: <?php echo htmlspecialchars($car['location']); ?></p>
                            <a href="book.php?car_id=<?php echo $car['id']; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($cars)): ?>
                <p class="text-center">No cars available for the selected criteria.</p>
            <?php endif; ?>
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
