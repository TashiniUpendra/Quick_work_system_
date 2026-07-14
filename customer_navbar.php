<?php
// Shared Bootstrap sidebar for all customer pages
// Usage: $current_page = 'dashboard'; require_once("includes/customer_navbar.php");

$nav_user = $_SESSION['user'] ?? null;
$nav_name = $nav_user['name'] ?? 'User';

// Get unread notification count
$nav_notif_count = 0;
if(isset($conn) && $nav_user){
    $nq = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE user_id=".(int)$nav_user['user_id']." AND is_read=0");
    if($nq) $nav_notif_count = $nq->fetch_assoc()['c'];
}

$pages = [
    'dashboard'  => ['icon' => 'bi-house-door-fill',    'label' => 'Dashboard',      'url' => 'customer.php'],
    'search'     => ['icon' => 'bi-search',              'label' => 'Search Workers',  'url' => 'search.php'],
    'bookings'   => ['icon' => 'bi-calendar-check-fill', 'label' => 'My Bookings',     'url' => 'my_bookings.php'],
    'payments'   => ['icon' => 'bi-credit-card-fill',    'label' => 'Payments',        'url' => 'payment.php'],
    'feedback'   => ['icon' => 'bi-star-fill',           'label' => 'Feedback',        'url' => 'feedback.php'],
    'complaints' => ['icon' => 'bi-exclamation-triangle-fill', 'label' => 'Complaints', 'url' => 'complaints.php'],
    'profile'    => ['icon' => 'bi-person-fill',         'label' => 'Profile',         'url' => 'profile.php'],
];
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #f0f2f5; margin: 0; }

    /* Sidebar */
    .qw-sidebar {
        position: fixed; top: 0; left: 0;
        width: 260px; height: 100vh;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #fff; padding: 0;
        z-index: 1000; overflow-y: auto;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
    }
    .qw-sidebar .brand {
        padding: 28px 24px 20px;
        font-size: 1.5rem; font-weight: 700;
        letter-spacing: -0.5px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .qw-sidebar .brand i { color: #38bdf8; margin-right: 8px; }
    .qw-sidebar .nav-links { padding: 16px 12px; list-style: none; margin: 0; }
    .qw-sidebar .nav-links li { margin-bottom: 4px; }
    .qw-sidebar .nav-links a {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; border-radius: 10px;
        color: #94a3b8; text-decoration: none;
        font-size: 0.9rem; font-weight: 500;
        transition: all 0.2s ease;
    }
    .qw-sidebar .nav-links a:hover {
        background: rgba(255,255,255,0.08);
        color: #e2e8f0;
    }
    .qw-sidebar .nav-links a.active {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }
    .qw-sidebar .nav-links a i { font-size: 1.1rem; width: 22px; text-align: center; }
    .qw-sidebar .nav-footer {
        position: absolute; bottom: 0; width: 100%;
        padding: 16px 12px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .qw-sidebar .nav-footer a {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; border-radius: 10px;
        color: #f87171; text-decoration: none;
        font-size: 0.9rem; font-weight: 500;
        transition: all 0.2s;
    }
    .qw-sidebar .nav-footer a:hover { background: rgba(248,113,113,0.1); }

    /* Main Content */
    .qw-main {
        margin-left: 260px; padding: 0;
        min-height: 100vh;
    }

    /* Top Bar */
    .qw-topbar {
        background: #fff;
        padding: 16px 32px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
        position: sticky; top: 0; z-index: 100;
    }
    .qw-topbar h5 { margin: 0; font-weight: 600; color: #1e293b; }
    .qw-topbar .user-info { display: flex; align-items: center; gap: 16px; }
    .qw-topbar .notif-btn {
        position: relative; background: none; border: none;
        font-size: 1.3rem; color: #64748b; cursor: pointer;
        padding: 8px; border-radius: 8px; transition: all 0.2s;
    }
    .qw-topbar .notif-btn:hover { background: #f1f5f9; color: #1e293b; }
    .qw-topbar .notif-badge {
        position: absolute; top: 2px; right: 2px;
        background: #ef4444; color: #fff;
        border-radius: 50%; width: 18px; height: 18px;
        font-size: 0.65rem; display: flex;
        align-items: center; justify-content: center;
        font-weight: 700;
    }
    .qw-topbar .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: #fff; display: flex; align-items: center;
        justify-content: center; font-weight: 600; font-size: 0.85rem;
    }

    .qw-content { padding: 32px; }

    /* Responsive */
    @media (max-width: 768px) {
        .qw-sidebar { width: 70px; }
        .qw-sidebar .brand span, .qw-sidebar .nav-links a span,
        .qw-sidebar .nav-footer a span { display: none; }
        .qw-sidebar .nav-links a, .qw-sidebar .nav-footer a {
            justify-content: center; padding: 14px;
        }
        .qw-main { margin-left: 70px; }
    }
</style>

<!-- SIDEBAR -->
<div class="qw-sidebar">
    <div class="brand">
        <i class="bi bi-lightning-charge-fill"></i><span>QuickWorks</span>
    </div>
    <ul class="nav-links">
        <?php foreach($pages as $key => $page): ?>
        <li>
            <a href="<?php echo $page['url']; ?>" class="<?php echo (isset($current_page) && $current_page == $key) ? 'active' : ''; ?>">
                <i class="bi <?php echo $page['icon']; ?>"></i>
                <span><?php echo $page['label']; ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="nav-footer">
        <a href="logout.php">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- MAIN WRAPPER -->
<div class="qw-main">
    <!-- TOP BAR -->
    <div class="qw-topbar">
        <h5><?php echo $page_title ?? 'Dashboard'; ?></h5>
        <div class="user-info">
            <button class="notif-btn" title="Notifications">
                <i class="bi bi-bell-fill"></i>
                <?php if($nav_notif_count > 0): ?>
                    <span class="notif-badge"><?php echo $nav_notif_count; ?></span>
                <?php endif; ?>
            </button>
            <div class="user-avatar"><?php echo strtoupper(substr($nav_name, 0, 1)); ?></div>
            <span style="font-weight:500; color:#334155;"><?php echo htmlspecialchars($nav_name); ?></span>
        </div>
    </div>
    <div class="qw-content">
