<?php
// pages/edit-property.php
// Edit property

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Edit Property - Rent CMS';

checkRole(['tenant', 'admin']);

$property_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

if (!$property_id) {
    header('Location: /pages/my-properties.php');
    exit;
}

// Get property
$result = $conn->query("SELECT * FROM properties WHERE id = $property_id");
$property = $result->fetch_assoc();

if (!$property) {
    header('Location: /pages/my-properties.php');
    exit;
}

// Check authorization
if ($_SESSION['role'] !== 'admin' && $property['user_id'] != $user_id) {
    header('Location: /pages/index.php');
    exit;
}

$error = '';
$success = '';

// Get dropdown data
$cities = $conn->query("SELECT * FROM cities ORDER BY name");
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$deal_types = $conn->query("SELECT * FROM deal_types ORDER BY name");
$images = $conn->query("SELECT * FROM property_images WHERE property_id = $property_id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $city_id = intval($_POST['city_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $deal_type_id = intval($_POST['deal_type_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $price_unit = sanitizeInput($_POST['price_unit'] ?? 'per_month');
    $bedrooms = intval($_POST['bedrooms'] ?? 0);
    $bathrooms = intval($_POST['bathrooms'] ?? 0);
    $square_feet = floatval($_POST['square_feet'] ?? 0);
    $amenities = sanitizeInput($_POST['amenities'] ?? '');
    
    if (empty($title) || empty($address) || !$city_id || !$category_id || !$deal_type_id || !$price) {
        $error = 'Please fill in all required fields';
    } else {
        $query = "UPDATE properties SET title = ?, description = ?, address = ?, city_id = ?, category_id = ?, deal_type_id = ?, price = ?, price_unit = ?, bedrooms = ?, bathrooms = ?, square_feet = ?, amenities = ? WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssiiiiddiidsi', 
            $title, $description, $address, $city_id, $category_id, $deal_type_id,
            $price, $price_unit, $bedrooms, $bathrooms, $square_feet, $amenities, $property_id
        );
        
        if ($stmt->execute()) {
            $success = 'Property updated successfully!';
            $property = $conn->query("SELECT * FROM properties WHERE id = $property_id")->fetch_assoc();
        } else {
            $error = 'Failed to update property';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container edit-property-container">
    <h2>Edit Property</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo sanitizeOutput($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo sanitizeOutput($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="property-form">
        <div class="form-section">
            <h3>Basic Information</h3>
            
            <div class="form-group">
                <label for="title">Property Title *</label>
                <input type="text" id="title" name="title" required value="<?php echo sanitizeOutput($property['title']); ?>" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?php echo sanitizeOutput($property['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="address">Address *</label>
                <input type="text" id="address" name="address" required value="<?php echo sanitizeOutput($property['address']); ?>" class="form-control">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="city_id">City *</label>
                    <select id="city_id" name="city_id" required class="form-control">
                        <option value="">Select City</option>
                        <?php while ($city = $cities->fetch_assoc()): ?>
                            <option value="<?php echo $city['id']; ?>" <?php echo ($property['city_id'] == $city['id']) ? 'selected' : ''; ?>><?php echo sanitizeOutput($city['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" required class="form-control">
                        <option value="">Select Category</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($property['category_id'] == $category['id']) ? 'selected' : ''; ?>><?php echo sanitizeOutput($category['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="deal_type_id">Deal Type *</label>
                    <select id="deal_type_id" name="deal_type_id" required class="form-control">
                        <option value="">Select Deal Type</option>
                        <?php while ($type = $deal_types->fetch_assoc()): ?>
                            <option value="<?php echo $type['id']; ?>" <?php echo ($property['deal_type_id'] == $type['id']) ? 'selected' : ''; ?>><?php echo sanitizeOutput($type['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Pricing & Details</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price *</label>
                    <input type="number" id="price" name="price" required step="0.01" value="<?php echo $property['price']; ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="price_unit">Price Unit *</label>
                    <select id="price_unit" name="price_unit" class="form-control">
                        <option value="per_month" <?php echo ($property['price_unit'] == 'per_month') ? 'selected' : ''; ?>>Per Month</option>
                        <option value="per_day" <?php echo ($property['price_unit'] == 'per_day') ? 'selected' : ''; ?>>Per Day</option>
                        <option value="per_sqft" <?php echo ($property['price_unit'] == 'per_sqft') ? 'selected' : ''; ?>>Per Sq Ft</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" min="0" value="<?php echo $property['bedrooms']; ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="bathrooms">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" min="0" value="<?php echo $property['bathrooms']; ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="square_feet">Square Feet</label>
                    <input type="number" id="square_feet" name="square_feet" step="0.01" value="<?php echo $property['square_feet']; ?>" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Current Images</h3>
            <div class="images-list">
                <?php while ($image = $images->fetch_assoc()): ?>
                    <div class="image-item">
                        <img src="/uploads/<?php echo sanitizeOutput($image['image_path']); ?>" alt="Property Image">
                        <p><?php echo sanitizeOutput($image['image_path']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Additional Information</h3>
            
            <div class="form-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <textarea id="amenities" name="amenities" class="form-control" rows="3"><?php echo sanitizeOutput($property['amenities']); ?></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/pages/my-properties.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
