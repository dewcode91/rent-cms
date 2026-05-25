<?php
// admin/settings.php
// System settings

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'System Settings - Rent CMS';

checkRole(['admin']);

$error = '';
$success = '';

// Get current settings
$settings_query = "SELECT * FROM admin_settings";
$settings_result = $conn->query($settings_query);
$settings = array();
while ($setting = $settings_result->fetch_assoc()) {
    $settings[$setting['setting_key']] = $setting['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = sanitizeInput($_POST['site_name'] ?? '');
    $site_description = sanitizeInput($_POST['site_description'] ?? '');
    $items_per_page = intval($_POST['items_per_page'] ?? 12);
    $require_approval = isset($_POST['require_approval']) ? 'true' : 'false';
    
    // Update settings
    $conn->query("UPDATE admin_settings SET setting_value = '$site_name' WHERE setting_key = 'site_name'");
    $conn->query("UPDATE admin_settings SET setting_value = '$site_description' WHERE setting_key = 'site_description'");
    $conn->query("UPDATE admin_settings SET setting_value = '$items_per_page' WHERE setting_key = 'items_per_page'");
    $conn->query("UPDATE admin_settings SET setting_value = '$require_approval' WHERE setting_key = 'require_approval'");
    
    $success = 'Settings updated successfully!';
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <ul class="admin-menu">
            <li><a href="/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/admin/users.php">Manage Users</a></li>
            <li><a href="/admin/properties.php">Manage Properties</a></li>
            <li><a href="/admin/inquiries.php">Inquiries</a></li>
            <li><a href="/admin/settings.php" class="active">Settings</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2>System Settings</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo sanitizeOutput($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo sanitizeOutput($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" class="settings-form">
            <div class="form-section">
                <h3>Site Information</h3>
                
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?php echo sanitizeOutput($settings['site_name'] ?? ''); ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="site_description">Site Description</label>
                    <textarea id="site_description" name="site_description" class="form-control" rows="4"><?php echo sanitizeOutput($settings['site_description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Display Settings</h3>
                
                <div class="form-group">
                    <label for="items_per_page">Items Per Page</label>
                    <input type="number" id="items_per_page" name="items_per_page" value="<?php echo $settings['items_per_page'] ?? 12; ?>" min="1" class="form-control">
                </div>
            </div>
            
            <div class="form-section">
                <h3>Moderation Settings</h3>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="require_approval" <?php echo ($settings['require_approval'] === 'true') ? 'checked' : ''; ?>>
                        Require approval for new listings
                    </label>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
