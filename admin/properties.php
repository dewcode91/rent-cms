<?php
// admin/properties.php
// Manage all properties

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Manage Properties - Rent CMS';

checkRole(['admin']);

// Handle approval/rejection
if (isset($_GET['action'])) {
    $property_id = intval($_GET['id'] ?? 0);
    
    if ($_GET['action'] === 'approve') {
        $conn->query("UPDATE properties SET status = 'approved' WHERE id = $property_id");
    } elseif ($_GET['action'] === 'reject') {
        $conn->query("UPDATE properties SET status = 'rejected' WHERE id = $property_id");
    } elseif ($_GET['action'] === 'delete') {
        $conn->query("DELETE FROM properties WHERE id = $property_id");
    }
    
    header('Location: /admin/properties.php');
    exit;
}

// Get all properties
$properties = $conn->query("SELECT p.*, u.username, c.name as city_name FROM properties p LEFT JOIN users u ON p.user_id = u.id LEFT JOIN cities c ON p.city_id = c.id ORDER BY p.created_at DESC");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <ul class="admin-menu">
            <li><a href="/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/admin/users.php">Manage Users</a></li>
            <li><a href="/admin/properties.php" class="active">Manage Properties</a></li>
            <li><a href="/admin/inquiries.php">Inquiries</a></li>
            <li><a href="/admin/settings.php">Settings</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>Manage Properties</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Owner</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($property = $properties->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo sanitizeOutput($property['title']); ?></td>
                        <td><?php echo sanitizeOutput($property['username']); ?></td>
                        <td><?php echo sanitizeOutput($property['city_name']); ?></td>
                        <td>$<?php echo number_format($property['price']); ?></td>
                        <td><span class="status-badge status-<?php echo $property['status']; ?>"><?php echo ucfirst($property['status']); ?></span></td>
                        <td>
                            <?php if ($property['status'] === 'pending'): ?>
                                <a href="/admin/properties.php?action=approve&id=<?php echo $property['id']; ?>" class="btn-small">Approve</a>
                                <a href="/admin/properties.php?action=reject&id=<?php echo $property['id']; ?>" class="btn-small btn-danger">Reject</a>
                            <?php endif; ?>
                            <a href="/admin/properties.php?action=delete&id=<?php echo $property['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this property?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
