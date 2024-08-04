<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    try {
        $pdo->beginTransaction();

        // Hapus data booking berdasarkan ID
        $stmt = $pdo->prepare('DELETE FROM bookings WHERE id = ?');
        $stmt->execute([$id]);

        // Commit transaksi setelah penghapusan berhasil
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to delete booking: " . $e->getMessage();
    }
}

$userId = $_SESSION['user_id'];

// Ambil data bookings untuk user yang sedang login
$stmt = $pdo->prepare('SELECT b.id, t.name, t.number, t.source, t.destination, t.departure_time, t.arrival_time, b.booking_date, b.seat_number, b.ticket_number
                       FROM bookings b
                       JOIN trains t ON b.train_id = t.id
                       WHERE b.user_id = ?');
$stmt->execute([$userId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Bookings | Trains Booking</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h2>My Bookings</h2>
    <?php if (count($bookings) > 0): ?>
        <table class="table table-bordered">
            <thead class="text-white bg-primary">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Train Name</th>
                    <th rowspan="2">Seat Number</th>
                    <th rowspan="2">Ticket Number</th>
                    <th colspan="4" class="text-center">Itinerary</th>
                    <th rowspan="2">Booking Time</th>
                    <th rowspan="2">Actions</th>
                </tr>
                <tr>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $index => $booking): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($booking['name']).' ('. htmlspecialchars($booking['number']).')'; ?></td>
                        <td><?php echo htmlspecialchars($booking['seat_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['ticket_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['source']); ?></td>
                        <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                        <td><?php echo htmlspecialchars($booking['departure_time']); ?></td>
                        <td><?php echo htmlspecialchars($booking['arrival_time']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                        <td>
                            <form method="post" style="display:inline;" onsubmit="return confirmCancel()">
                                <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Cancel</button>
                            </form>
                         </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no bookings.</p>
    <?php endif; ?>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
<script>
    function confirmCancel() {
        return confirm('Are you sure you want to cancel this booking?');
    }
</script>
</body>
</html>
