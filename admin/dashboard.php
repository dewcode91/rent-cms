<?php
// admin/dashboard.php
// Admin dashboard

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Admin Dashboard - Rent CMS';

checkRole(['admin']);

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_properties = $conn->query("SELECT COUNT(*) as count FROM properties")->fetch_assoc()['count'];
$pending_properties = $conn->query("SELECT COUNT(*) as count FROM properties WHERE status = 'pending'")->fetch_assoc()['count'];
$total_inquiries = $conn->query("SELECT COUNT(*) as count FROM inquiries")->fetch_assoc()['count'];
$new_inquiries = $conn->query("SELECT COUNT(*) as count FROM inquiries WHERE status = 'new'")->fetch_assoc()['count'];

// Get recent activities
$recent_properties = $conn->query("SELECT p.*, u.username, COUNT(pi.id) as images FROM properties p LEFT JOIN users u ON p.user_id = u.id LEFT JOIN property_images pi ON p.id = pi.property_id WHERE p.status = 'pending' GROUP BY p.id ORDER BY p.created_at DESC LIMIT 5");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <ul class="admin-menu">
            <li><a href="/admin/dashboard.php" class="active">Dashboard</a></li>
            <li><a href="/admin/users.php">Manage Users</a></li>
            <li><a href="/admin/properties.php">Manage Properties</a></li>
            <li><a href="/admin/inquiries.php">Inquiries</a></li>
            <li><a href="/admin/settings.php">Settings</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>Admin Dashboard</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p class="stat-value"><?php echo $total_users; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Properties</h3>
                <p class="stat-value"><?php echo $total_properties; ?></p>
            </div>
            
            <div class="stat-card alert">
                <h3>Pending Properties</h3>
                <p class="stat-value"><?php echo $pending_properties; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Inquiries</h3>
                <p class="stat-value"><?php echo $total_inquiries; ?></p>
            </div>
            
            <div class="stat-card alert">
                <h3>New Inquiries</h3>
                <p class="stat-value"><?php echo $new_inquiries; ?></p>
            </div>
        </div>
        
        <div class="recent-section">
            <h3>Pending Properties</h3>
            
            <?php if ($recent_properties->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Owner</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($property = $recent_properties->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo sanitizeOutput($property['title']); ?></td>
                                <td><?php echo sanitizeOutput($property['username']); ?></td>
                                <td>$<?php echo number_format($property['price']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($property['created_at'])); ?></td>
                                <td>
                                    <a href="/admin/approve-property.php?id=<?php echo $property['id']; ?>" class="btn-small">Approve</a>
                                    <a href="/admin/reject-property.php?id=<?php echo $property['id']; ?>" class="btn-small btn-danger">Reject</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending properties</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
