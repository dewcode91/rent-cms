<?php
// pages/property-detail.php
// Property detail page with inquiry form

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$property_id) {
    header('Location: /pages/index.php');
    exit;
}

// Get property details
$query = "SELECT p.*, u.username, u.email, u.phone, 
          GROUP_CONCAT(pi.image_path SEPARATOR ',') as images,
          c.name as city_name, dt.name as deal_type_name, cat.name as category_name
          FROM properties p
          LEFT JOIN users u ON p.user_id = u.id
          LEFT JOIN property_images pi ON p.id = pi.property_id
          LEFT JOIN cities c ON p.city_id = c.id
          LEFT JOIN deal_types dt ON p.deal_type_id = dt.id
          LEFT JOIN categories cat ON p.category_id = cat.id
          WHERE p.id = ? AND p.status = 'approved'
          GROUP BY p.id";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: /pages/index.php');
    exit;
}

$property = $result->fetch_assoc();
$images = $property['images'] ? explode(',', $property['images']) : array();

// Update views count
$conn->query("UPDATE properties SET views = views + 1 WHERE id = $property_id");

// Handle inquiry submission
$inquiry_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry'])) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        $sender_id = isLoggedIn() ? $_SESSION['user_id'] : null;
        
        $query = "INSERT INTO inquiries (property_id, sender_id, sender_name, sender_email, sender_phone, message) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sissss', $property_id, $sender_id, $name, $email, $phone, $message);
        
        if ($stmt->execute()) {
            $inquiry_message = 'Your inquiry has been sent successfully!';
        }
    }
}

$page_title = sanitizeOutput($property['title']) . ' - Rent CMS';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="property-detail-container">
    <div class="property-gallery">
        <div class="main-image">
            <?php if (!empty($images)): ?>
                <img id="main-image" src="/uploads/<?php echo sanitizeOutput($images[0]); ?>" alt="Property">
            <?php else: ?>
                <img src="/assets/images/placeholder.jpg" alt="Property">
            <?php endif; ?>
        </div>
        
        <?php if (count($images) > 1): ?>
            <div class="thumbnails">
                <?php foreach ($images as $image): ?>
                    <img src="/uploads/<?php echo sanitizeOutput($image); ?>" 
                         alt="Thumbnail" 
                         onclick="changeImage(this.src)">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="property-info">
        <h1><?php echo sanitizeOutput($property['title']); ?></h1>
        
        <div class="property-header">
            <span class="price">$<?php echo number_format($property['price'], 0); ?> / <?php echo ucfirst(str_replace('_', ' ', $property['price_unit'])); ?></span>
            <span class="views">👁️ <?php echo $property['views']; ?> views</span>
        </div>
        
        <div class="property-location">
            <p>📍 <?php echo sanitizeOutput($property['address']); ?>, <?php echo sanitizeOutput($property['city_name']); ?></p>
        </div>
        
        <div class="property-badges">
            <span class="badge"><?php echo sanitizeOutput($property['deal_type_name']); ?></span>
            <span class="badge"><?php echo sanitizeOutput($property['category_name']); ?></span>
        </div>
        
        <div class="property-specs-detail">
            <?php if ($property['bedrooms']): ?>
                <div class="spec"><strong>Bedrooms:</strong> <?php echo $property['bedrooms']; ?></div>
            <?php endif; ?>
            <?php if ($property['bathrooms']): ?>
                <div class="spec"><strong>Bathrooms:</strong> <?php echo $property['bathrooms']; ?></div>
            <?php endif; ?>
            <?php if ($property['square_feet']): ?>
                <div class="spec"><strong>Square Feet:</strong> <?php echo number_format($property['square_feet']); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="property-description">
            <h3>Description</h3>
            <p><?php echo nl2br(sanitizeOutput($property['description'])); ?></p>
        </div>
        
        <?php if ($property['amenities']): ?>
            <div class="property-amenities">
                <h3>Amenities</h3>
                <ul>
                    <?php foreach (explode(',', $property['amenities']) as $amenity): ?>
                        <li><?php echo sanitizeOutput(trim($amenity)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="inquiry-section">
            <h3>Contact Property Owner</h3>
            
            <?php if ($inquiry_message): ?>
                <div class="alert alert-success"><?php echo $inquiry_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="inquiry-form">
                <input type="hidden" name="inquiry" value="1">
                
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required class="form-control" rows="5"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Inquiry</button>
            </form>
            
            <div class="owner-info">
                <h4>Posted by: <?php echo sanitizeOutput($property['username']); ?></h4>
                <?php if ($property['phone']): ?>
                    <p>📞 <?php echo sanitizeOutput($property['phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function changeImage(src) {
    document.getElementById('main-image').src = src;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
