<?php
// pages/register.php - already included above, here's the corrected version
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Register - Rent CMS';

if (isLoggedIn()) {
    header('Location: /pages/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $result = register($conn, $username, $email, $password, $first_name, $last_name);
        if ($result['success']) {
            $success = 'Registration successful! You can now <a href="/pages/login.php">login</a>';
        } else {
            $error = $result['message'];
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="register-container">
    <div class="register-box">
        <h2>Create Your Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo sanitizeOutput($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        
        <p class="form-footer">
            Already have an account? <a href="/pages/login.php">Login here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
