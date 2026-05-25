<?php
// admin/inquiries.php
// Manage inquiries

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Manage Inquiries - Rent CMS';

checkRole(['admin']);

// Handle inquiry status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $inquiry_id = intval($_GET['id']);
    
    if ($_GET['action'] === 'read') {
        $conn->query("UPDATE inquiries SET status = 'read' WHERE id = $inquiry_id");
    } elseif ($_GET['action'] === 'archive') {
        $conn->query("UPDATE inquiries SET status = 'archived' WHERE id = $inquiry_id");
    } elseif ($_GET['action'] === 'delete') {
        $conn->query("DELETE FROM inquiries WHERE id = $inquiry_id");
    }
    
    header('Location: /admin/inquiries.php');
    exit;
}

// Get all inquiries
$inquiries = $conn->query("SELECT i.*, p.title as property_title, u.username FROM inquiries i LEFT JOIN properties p ON i.property_id = p.id LEFT JOIN users u ON i.sender_id = u.id ORDER BY i.created_at DESC");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <ul class="admin-menu">
            <li><a href="/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/admin/users.php">Manage Users</a></li>
            <li><a href="/admin/properties.php">Manage Properties</a></li>
            <li><a href="/admin/inquiries.php" class="active">Inquiries</a></li>
            <li><a href="/admin/settings.php">Settings</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>Manage Inquiries</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Email</th>
                    <th>Property</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($inquiry = $inquiries->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo sanitizeOutput($inquiry['sender_name']); ?></td>
                        <td><?php echo sanitizeOutput($inquiry['sender_email']); ?></td>
                        <td><?php echo sanitizeOutput($inquiry['property_title']); ?></td>
                        <td><?php echo substr(sanitizeOutput($inquiry['message']), 0, 50) . '...'; ?></td>
                        <td><span class="status-badge status-<?php echo $inquiry['status']; ?>"><?php echo ucfirst($inquiry['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                        <td>
                            <?php if ($inquiry['status'] === 'new'): ?>
                                <a href="/admin/inquiries.php?action=read&id=<?php echo $inquiry['id']; ?>" class="btn-small">Mark Read</a>
                            <?php endif; ?>
                            <a href="/admin/inquiries.php?action=archive&id=<?php echo $inquiry['id']; ?>" class="btn-small">Archive</a>
                            <a href="/admin/inquiries.php?action=delete&id=<?php echo $inquiry['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this inquiry?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
