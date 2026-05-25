<?php
// pages/login.php
// User login page

$page_title = 'Login - Rent CMS';

if (isLoggedIn()) {
    header('Location: /pages/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = login($conn, $email, $password);
        if ($result['success']) {
            header('Location: /pages/index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <h2>Login to Your Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo sanitizeOutput($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo sanitizeOutput($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
        <p class="form-footer">
            Don't have an account? <a href="/pages/register.php">Register here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
