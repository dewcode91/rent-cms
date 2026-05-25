<?php
// pages/my-properties.php
// View and manage user's properties (Tenant)

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'My Properties - Rent CMS';

checkRole(['tenant', 'admin']);

$user_id = $_SESSION['user_id'];

// Get user's properties
if ($_SESSION['role'] === 'admin') {
    $query = "SELECT p.*, u.username, c.name as city_name, COUNT(pi.id) as image_count 
              FROM properties p
              LEFT JOIN users u ON p.user_id = u.id
              LEFT JOIN cities c ON p.city_id = c.id
              LEFT JOIN property_images pi ON p.id = pi.property_id
              GROUP BY p.id
              ORDER BY p.created_at DESC";
    $result = $conn->query($query);
} else {
    $query = "SELECT p.*, u.username, c.name as city_name, COUNT(pi.id) as image_count 
              FROM properties p
              LEFT JOIN users u ON p.user_id = u.id
              LEFT JOIN cities c ON p.city_id = c.id
              LEFT JOIN property_images pi ON p.id = pi.property_id
              WHERE p.user_id = ?
              GROUP BY p.id
              ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container my-properties-container">
    <div class="page-header">
        <h2>My Properties</h2>
        <a href="/pages/add-property.php" class="btn btn-primary">+ Add New Property</a>
    </div>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="properties-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($property = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo sanitizeOutput($property['title']); ?></strong></td>
                            <td><?php echo sanitizeOutput($property['city_name']); ?></td>
                            <td>$<?php echo number_format($property['price'], 0); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $property['status']; ?>">
                                    <?php echo ucfirst($property['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $property['views']; ?></td>
                            <td><?php echo $property['image_count']; ?></td>
                            <td>
                                <a href="/pages/property-detail.php?id=<?php echo $property['id']; ?>" class="btn-small">View</a>
                                <a href="/pages/edit-property.php?id=<?php echo $property['id']; ?>" class="btn-small">Edit</a>
                                <a href="/pages/delete-property.php?id=<?php echo $property['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this property?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>You haven't added any properties yet.</p>
            <a href="/pages/add-property.php" class="btn btn-primary">Add Your First Property</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
