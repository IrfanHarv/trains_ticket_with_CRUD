<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

function getLastTicketNumber($pdo) {
    $stmt = $pdo->query('SELECT ticket_number FROM bookings ORDER BY id DESC LIMIT 1');
    return $stmt->fetchColumn();
}

function generateTicketNumber($lastTicket) {
    $lastNumber = $lastTicket ? intval(substr($lastTicket, 1, 4)) + 1 : 1;
    $formattedNumber = str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
    $date = date('dmY');
    return "T{$formattedNumber}{$date}";
}

function bookTrain($pdo, $userId, $trainId, $seatNumber, $ticketNumber) {
    $stmt = $pdo->prepare('INSERT INTO bookings (user_id, train_id, booking_date, seat_number, ticket_number) VALUES (?, ?, CURDATE(), ?, ?)');
    $stmt->execute([$userId, $trainId, $seatNumber, $ticketNumber]);
}
if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['message']; ?></div>
<?php
    unset($_SESSION['message']);
endif;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['train_id'])) {
    $train_id = $_POST['train_id'];
    $seat_number = $_POST['seat_number'];
    $user_id = $_SESSION['user_id'];

    $lastTicket = getLastTicketNumber($pdo);
    $ticket_number = generateTicketNumber($lastTicket);

    bookTrain($pdo, $user_id, $train_id, $seat_number, $ticket_number);

    $message = 'Booking successful!';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['train_id'])) {
    $train_id = $_GET['train_id'];

    // Get total seats from the train
    $stmt = $pdo->prepare('SELECT seats FROM trains WHERE id = ?');
    $stmt->execute([$train_id]);
    $totalSeats = $stmt->fetchColumn();

    // Get occupied seats
    $stmt = $pdo->prepare('SELECT seat_number FROM bookings WHERE train_id = ?');
    $stmt->execute([$train_id]);
    $occupiedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['totalSeats' => $totalSeats, 'occupiedSeats' => $occupiedSeats]);
    exit;
}

$stmt = $pdo->query('SELECT * FROM trains');
$trains = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Train Booking</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h2>Book a Train</h2>
    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <form id="booking-form" method="post" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="train_id" class="form-label">Train</label>
            <select class="form-control" id="train_id" name="train_id" required onchange="loadSeats()">
                <option value="">Select a train</option>
                <?php foreach ($trains as $train): ?>
                    <option value="<?php echo $train['id']; ?>"><?php echo $train['name']; ?> (<?php echo $train['number']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="seat-selection" class="mb-3" style="display:none;">
            <label for="seat_number" class="form-label">Seat Number</label>
            <div id="seats" class="d-flex flex-wrap"></div>
        </div>
        <input type="hidden" id="seat_number" name="seat_number" required>
        <button type="submit" class="btn btn-primary">Book</button>
    </form>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
<script>
function loadSeats() {
    const trainId = document.getElementById('train_id').value;
    if (!trainId) return;

    fetch(`?train_id=${trainId}`)
        .then(response => response.json())
        .then(data => {
            const seatsContainer = document.getElementById('seats');
            seatsContainer.innerHTML = '';
            const seatSelection = document.getElementById('seat-selection');
            seatSelection.style.display = 'block';

            const occupiedSeats = data.occupiedSeats.map(seat => parseInt(seat, 10)); // Ensure occupiedSeats are integers
            const totalSeats = data.totalSeats;

            for (let i = 1; i <= totalSeats; i++) {
                const seatButton = document.createElement('button');
                seatButton.type = 'button';
                seatButton.className = 'btn m-1';
                seatButton.textContent = i;

                if (occupiedSeats.includes(i)) {
                    seatButton.classList.add('btn-secondary');
                    seatButton.disabled = true;
                } else {
                    seatButton.classList.add('btn-info');
                    seatButton.onclick = () => selectSeat(seatButton, i);
                }

                seatsContainer.appendChild(seatButton);
            }
        });
}

function selectSeat(button, seatNumber) {
    document.getElementById('seat_number').value = seatNumber;
    const buttons = document.querySelectorAll('#seats button');
    buttons.forEach(btn => {
        btn.classList.remove('btn-success');
        if (!btn.disabled) {
            btn.classList.add('btn-info');
        }
    });
    button.classList.remove('btn-info');
    button.classList.add('btn-success');
}

function validateForm() {
    const seatNumber = document.getElementById('seat_number').value;
    if (!seatNumber) {
        alert('Please select a seat.');
        return false;
    }
    return true;
}

</script>
</body>
</html>
