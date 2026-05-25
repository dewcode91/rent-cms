<?php
// pages/about.php
// About page

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'About Us - Rent CMS';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="about-content">
        <h1>About Rent CMS</h1>
        
        <section class="about-section">
            <h2>Welcome to Rent CMS</h2>
            <p>
                Rent CMS is a professional property management system designed to streamline the rental market. 
                Whether you're a property owner, tenant, or just looking for your perfect home, our platform provides 
                everything you need in one convenient place.
            </p>
        </section>
        
        <section class="about-section">
            <h2>Our Mission</h2>
            <p>
                To revolutionize the way properties are rented and managed by providing a transparent, 
                user-friendly platform that connects property owners with tenants and streamlines the entire process.
            </p>
        </section>
        
        <section class="about-section">
            <h2>Key Features</h2>
            <ul>
                <li>Easy property listings for owners and managers</li>\n                <li>Advanced search and filtering for tenants</li>
                <li>Secure user authentication and role-based access</li>
                <li>Direct communication between owners and tenants</li>
                <li>Image gallery and detailed property information</li>
                <li>Admin dashboard for system management</li>
            </ul>
        </section>
        
        <section class="about-section\">
            <h2>How It Works</h2>
            <ol>
                <li><strong>Register:</strong> Create an account as a Tenant or Admin</li>
                <li><strong>Browse:</strong> Explore available properties with filters</li>
                <li><strong>List:</strong> Tenants can list properties for rent</li>
                <li><strong>Inquire:</strong> Contact property owners directly</li>
                <li><strong>Manage:</strong> Track your properties and inquiries</li>
            </ol>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
