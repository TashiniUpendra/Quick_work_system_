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

/* CUSTOMER COMPLAINTS */
$customer = $conn->query("
    SELECT c.*, u.name
    FROM complaints c
    JOIN users u ON c.user_id = u.user_id
    WHERE u.role='CUSTOMER'
    ORDER BY c.complaint_id DESC
");

/* WORKER COMPLAINTS */
$workerAll = $conn->query("
    SELECT c.*, u.name
    FROM complaints c
    JOIN users u ON c.user_id = u.user_id
    WHERE u.role='WORKER'
    ORDER BY c.complaint_id DESC
");

$page_title = 'Complaints Management';
$current_page = 'complaints';
require_once("includes/header.php");
require_once("includes/sidebar.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h2 class="fw-bold mb-1">Complaints Management</h2>
        <p class="text-muted mb-0">Overview of Customer & Worker complaints</p>
    </div>
</div>

<div class="row g-4 mb-5">
    
    <!-- CUSTOMER COMPLAINTS -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-exclamation me-2"></i>Customer Complaints</h5>
                <span class="badge bg-primary-subtle text-primary border ms-auto"><?php echo $customer->num_rows; ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($customer->num_rows > 0): ?>
                                <?php while($row = $customer->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="text-muted fw-bold">#<?php echo $row['complaint_id']; ?></span></td>
                                    <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td>
                                        <p class="mb-0 text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($row['message']); ?>">
                                            <?php echo htmlspecialchars($row['message']); ?>
                                        </p>
                                    </td>
                                    <td class="text-nowrap text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No customer complaints.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- WORKER COMPLAINTS -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-person-badge-fill me-2"></i>Worker Complaints</h5>
                <span class="badge bg-danger-subtle text-danger border ms-auto"><?php echo $workerAll->num_rows; ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Worker</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($workerAll->num_rows > 0): ?>
                                <?php while($row = $workerAll->fetch_assoc()): ?>
                                <?php
                                    $is_payment = (strpos(strtolower($row['message']), 'payment') !== false);
                                ?>
                                <tr>
                                    <td><span class="text-muted fw-bold">#<?php echo $row['complaint_id']; ?></span></td>
                                    <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td>
                                        <p class="mb-0 text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($row['message']); ?>">
                                            <?php echo htmlspecialchars($row['message']); ?>
                                        </p>
                                    </td>
                                    <td>
                                        <?php if($is_payment): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-credit-card me-1"></i>Payment Issue</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">General</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No worker complaints.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once("includes/footer.php"); ?>
