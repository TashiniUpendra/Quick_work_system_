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

/* APPROVE USER */
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET status='ACTIVE' WHERE user_id=$id");
    header("Location: users.php");
    exit();
}

/* REJECT USER */
if(isset($_GET['reject'])){
    $id = intval($_GET['reject']);
    $conn->query("DELETE FROM users WHERE user_id=$id");
    header("Location: users.php");
    exit();
}

/* BLOCK USER */
if(isset($_GET['block'])){
    $id = intval($_GET['block']);
    $conn->query("UPDATE users SET status='BLOCKED' WHERE user_id=$id");
    header("Location: users.php");
    exit();
}

/* UNBLOCK USER */
if(isset($_GET['unblock'])){
    $id = intval($_GET['unblock']);
    $conn->query("UPDATE users SET status='ACTIVE' WHERE user_id=$id");
    header("Location: users.php");
    exit();
}

/* ACTIVE USERS */
$activeUsers = $conn->query("SELECT * FROM users WHERE status='ACTIVE'");

/* PENDING USERS */
$pendingUsers = $conn->query("SELECT * FROM users WHERE status='PENDING'");

$page_title = 'User Management';
$current_page = 'users';
require_once("includes/header.php");
require_once("includes/sidebar.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h2 class="fw-bold mb-1">User Management</h2>
        <p class="text-muted mb-0">Approve or manage registered users</p>
    </div>
</div>

<!-- Pending Approvals -->
<div class="card mb-5">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-hourglass-split me-2"></i>Pending Approvals</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($pendingUsers->num_rows > 0): ?>
                        <?php while($row = $pendingUsers->fetch_assoc()): ?>
                        <tr>
                            <td><span class="text-muted fw-bold">#<?php echo $row['user_id']; ?></span></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge bg-light text-secondary border"><?php echo $row['role']; ?></span></td>
                            <td class="text-end">
                                <a href="view_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                                <a href="?approve=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                <a href="?reject=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this user?')">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No pending users.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Active Users -->
<div class="card">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="fw-bold mb-0 text-success"><i class="bi bi-people me-2"></i>Active Users</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($activeUsers->num_rows > 0): ?>
                        <?php while($row = $activeUsers->fetch_assoc()): ?>
                        <tr>
                            <td><span class="text-muted fw-bold">#<?php echo $row['user_id']; ?></span></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge bg-light text-secondary border"><?php echo $row['role']; ?></span></td>
                            <td><span class="badge bg-success-subtle text-success">Active</span></td>
                            <td class="text-end">
                                <a href="?block=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-slash-circle"></i> Block
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No active users.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
