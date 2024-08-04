<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    try {
        $pdo->beginTransaction();
        
        // Hapus data terkait di tabel bookings
        $stmt = $pdo->prepare('DELETE FROM bookings WHERE train_id = ?');
        $stmt->execute([$id]);
        
        // Hapus data di tabel trains
        $stmt = $pdo->prepare('DELETE FROM trains WHERE id = ?');
        $stmt->execute([$id]);
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}

$stmt = $pdo->query('SELECT * FROM trains');
$trains = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Tickets | Trains Booking</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h2>Manage Tickets</h2>
    <a href="add_ticket.php" class="btn btn-primary mb-3">Add New Ticket</a>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Number</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Seats</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count = 1; // Inisialisasi variabel counter
            foreach ($trains as $train): ?>
                <tr>
                    <td><?php echo $count++; ?></td> <!-- Menampilkan nomor urutan dan increment counter -->
                    <td><?php echo $train['name']; ?></td>
                    <td><?php echo $train['number']; ?></td>
                    <td><?php echo $train['source']; ?></td>
                    <td><?php echo $train['destination']; ?></td>
                    <td><?php echo $train['departure_time']; ?></td>
                    <td><?php echo $train['arrival_time']; ?></td>
                    <td><?php echo $train['seats']; ?></td>
                    <td>
                        <a href="edit_ticket.php?id=<?php echo $train['id']; ?>" class="btn btn-warning">Edit</a>
                        <form method="post" style="display:inline;" onsubmit="return confirmDelete()">
                            <input type="hidden" name="id" value="<?php echo $train['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this item?');
    }
</script>
</body>
</html>
