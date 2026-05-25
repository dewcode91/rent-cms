<?php
// pages/contact.php
// Contact form page

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Contact Us - Rent CMS';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } else {
        // Here you would typically send an email or store the message
        // For now, we'll just show a success message
        $success = 'Thank you for your message! We will get back to you soon.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container contact-container">
    <div class="contact-content">
        <h1>Contact Us</h1>
        <p>Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        
        <div class="contact-grid">
            <div class="contact-form">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo sanitizeOutput($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo sanitizeOutput($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required class="form-control" rows="6"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info">
                <h3>Get in Touch</h3>
                
                <div class="info-item">
                    <h4>📍 Address</h4>
                    <p>123 Main Street<br>Tampa, FL 33602<br>United States</p>
                </div>
                
                <div class="info-item">
                    <h4>📞 Phone</h4>
                    <p>+1 (800) 123-4567</p>
                </div>
                
                <div class="info-item">
                    <h4>📧 Email</h4>
                    <p>info@rentcms.com<br>support@rentcms.com</p>
                </div>
                
                <div class="info-item">
                    <h4>⏰ Business Hours</h4>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
