<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM trains WHERE id = ?');
$stmt->execute([$id]);
$train = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $seats = $_POST['seats'];

    $stmt = $pdo->prepare('UPDATE trains SET name = ?, number = ?, source = ?, destination = ?, departure_time = ?, arrival_time = ?, seats = ? WHERE id = ?');
    $stmt->execute([$name, $number, $source, $destination, $departure_time, $arrival_time, $seats, $id]);

    header('Location: manage_tickets.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Ticket | Trains Booking</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h2>Edit Ticket</h2>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $train['name']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="number" class="form-label">Number</label>
            <input type="text" class="form-control" id="number" name="number" value="<?php echo $train['number']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="source" class="form-label">Source</label>
            <input type="text" class="form-control" id="source" name="source" value="<?php echo $train['source']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="destination" class="form-label">Destination</label>
            <input type="text" class="form-control" id="destination" name="destination" value="<?php echo $train['destination']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="departure_time" class="form-label">Departure Time</label>
            <input type="time" class="form-control" id="departure_time" name="departure_time" value="<?php echo $train['departure_time']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="arrival_time" class="form-label">Arrival Time</label>
            <input type="time" class="form-control" id="arrival_time" name="arrival_time" value="<?php echo $train['arrival_time']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="seats" class="form-label">Seats</label>
            <input type="number" class="form-control" id="seats" name="seats" value="<?php echo $train['seats']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Ticket</button>
    </form>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
