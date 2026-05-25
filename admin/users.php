<?php
// admin/users.php
// Manage users

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Manage Users - Rent CMS';

checkRole(['admin']);

// Handle user status update
if (isset($_GET['action']) && $_GET['action'] === 'toggle-status' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $result = $conn->query("SELECT status FROM users WHERE id = $user_id");
    $user = $result->fetch_assoc();
    $new_status = ($user['status'] === 'active') ? 'inactive' : 'active';
    $conn->query("UPDATE users SET status = '$new_status' WHERE id = $user_id");
    header('Location: /admin/users.php');
    exit;
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <ul class="admin-menu">
            <li><a href="/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/admin/users.php" class="active">Manage Users</a></li>
            <li><a href="/admin/properties.php">Manage Properties</a></li>
            <li><a href="/admin/inquiries.php">Inquiries</a></li>
            <li><a href="/admin/settings.php">Settings</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>Manage Users</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo sanitizeOutput($user['username']); ?></td>
                        <td><?php echo sanitizeOutput($user['email']); ?></td>
                        <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                        <td><span class="status-badge status-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="/admin/users.php?action=toggle-status&id=<?php echo $user['id']; ?>" class="btn-small">
                                <?php echo ($user['status'] === 'active') ? 'Deactivate' : 'Activate'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
