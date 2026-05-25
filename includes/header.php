<?php
// includes/header.php
// Navigation and header component

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isLoggedIn();
$user_role = getUserRole();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? sanitizeOutput($page_title) : 'Rent CMS'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="/pages/index.php">Site name</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="/pages/index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">Homepage</a></li>
                <li><a href="/pages/about.php" class="<?php echo ($current_page === 'about.php') ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="/pages/contact.php" class="<?php echo ($current_page === 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
                
                <?php if ($is_logged_in): ?>
                    <li class="user-menu">
                        <span class="user-name">Welcome, <?php echo sanitizeOutput($_SESSION['username']); ?></span>
                        <ul class="dropdown-menu">
                            <?php if ($user_role === 'tenant' || $user_role === 'admin'): ?>
                                <li><a href="/pages/my-properties.php">My Properties</a></li>
                                <li><a href="/pages/add-property.php">Add Property</a></li>
                            <?php endif; ?>
                            
                            <?php if ($user_role === 'admin'): ?>
                                <li><a href="/admin/dashboard.php">Admin Dashboard</a></li>
                                <li><a href="/admin/users.php">Manage Users</a></li>
                                <li><a href="/admin/properties.php">Manage Properties</a></li>
                            <?php endif; ?>
                            
                            <li><a href="/pages/profile.php">Profile</a></li>
                            <li><a href="/includes/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/pages/login.php" class="login-btn">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</body>
</html>
