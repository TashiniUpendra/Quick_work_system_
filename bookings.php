<?php
session_start();
require_once("../config/Database.php");

$db = new Database();
$conn = $db->connect();

/* AUTH CHECK */
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != "ADMIN"){
    header("Location: ../login.php");
    exit();
}

/* DELETE BOOKING ONLY */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM job_requests WHERE job_id=$id");
    header("Location: bookings.php");
    exit();
}

/* FETCH BOOKINGS */
$result = $conn->query("
    SELECT 
        jr.*, 
        c.name AS customer_name, 
        w.name AS worker_name
    FROM job_requests jr
    JOIN users c ON jr.customer_id = c.user_id
    JOIN users w ON jr.worker_id = w.user_id
    ORDER BY jr.job_id DESC
");

$page_title = 'Booking Management';
$current_page = 'bookings';
require_once("includes/header.php");
require_once("includes/sidebar.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h2 class="fw-bold mb-1">Booking Management</h2>
        <p class="text-muted mb-0">View and manage all system bookings</p>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar-check me-2"></i>All Bookings</h5>
        <span class="badge bg-primary rounded-pill ms-3"><?php echo $result->num_rows; ?> Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Worker</th>
                        <th>Description</th>
                        <th>Date & Duration</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><span class="text-muted fw-bold">#<?php echo $row['job_id']; ?></span></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['worker_name']); ?></td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($row['job_date'])); ?>
                                <?php if(isset($row['duration']) && isset($row['duration_type'])): ?>
                                    <br><small class="text-muted fw-semibold"><?php echo $row['duration'] . ' ' . ucfirst(strtolower($row['duration_type'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $s = $row['status'];
                                    $badge_class = match($s){
                                        'PENDING' => 'bg-warning-subtle text-warning-emphasis',
                                        'ACCEPTED' => 'bg-info-subtle text-info-emphasis',
                                        'COMPLETED' => 'bg-success-subtle text-success-emphasis',
                                        'REJECTED' => 'bg-danger-subtle text-danger-emphasis',
                                        default => 'bg-secondary-subtle text-secondary-emphasis'
                                    };
                                ?>
                                <span class="badge border <?php echo $badge_class; ?>"><?php echo $s; ?></span>
                            </td>
                            <td class="text-end">
                                <a href="?delete=<?php echo $row['job_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to permanently delete this booking?')">
                                    <i class="bi bi-trash3"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
