<?php
// pages/profile.php
// User profile page

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'My Profile - Rent CMS';

if (!isLoggedIn()) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = getUserById($conn, $user_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $bio = sanitizeInput($_POST['bio'] ?? '');
    
    $query = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, bio = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssi', $first_name, $last_name, $phone, $bio, $user_id);
    
    if ($stmt->execute()) {
        $success = 'Profile updated successfully!';
        $user = getUserById($conn, $user_id);
    } else {
        $error = 'Failed to update profile';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container profile-container">
    <div class="profile-header">
        <h2>My Profile</h2>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo sanitizeOutput($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo sanitizeOutput($success); ?></div>
    <?php endif; ?>
    
    <div class="profile-content">
        <form method="POST" class="profile-form">
            <div class="form-section">
                <h3>Account Information</h3>
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo sanitizeOutput($user['username']); ?>" disabled class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo sanitizeOutput($user['email']); ?>" disabled class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled class="form-control">
                </div>
            </div>
            
            <div class="form-section">
                <h3>Personal Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo sanitizeOutput($user['first_name']); ?>" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo sanitizeOutput($user['last_name']); ?>" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo sanitizeOutput($user['phone']); ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" rows="4"><?php echo sanitizeOutput($user['bio']); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/pages/index.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
