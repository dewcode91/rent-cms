<?php
// pages/add-property.php
// Add new property (Tenant only)

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Add Property - Rent CMS';

checkRole(['tenant', 'admin']);

$error = '';
$success = '';

// Get dropdown data
$cities = $conn->query("SELECT * FROM cities ORDER BY name");
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$deal_types = $conn->query("SELECT * FROM deal_types ORDER BY name");

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
    $latitude = floatval($_POST['latitude'] ?? 0);
    $longitude = floatval($_POST['longitude'] ?? 0);
    
    if (empty($title) || empty($address) || !$city_id || !$category_id || !$deal_type_id || !$price) {
        $error = 'Please fill in all required fields';
    } else {
        // Insert property
        $query = "INSERT INTO properties (user_id, title, description, address, city_id, category_id, deal_type_id, price, price_unit, bedrooms, bathrooms, square_feet, amenities, latitude, longitude, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issiiiiiddddsdd', 
            $_SESSION['user_id'], $title, $description, $address, $city_id, $category_id, $deal_type_id,
            $price, $price_unit, $bedrooms, $bathrooms, $square_feet, $amenities, $latitude, $longitude
        );
        
        if ($stmt->execute()) {
            $property_id = $conn->insert_id;
            
            // Handle file uploads
            if (isset($_FILES['images'])) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $is_primary = true;
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if (!empty($tmp_name)) {
                        $filename = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                        if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
                            $query = "INSERT INTO property_images (property_id, image_path, is_primary) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param('isi', $property_id, $filename, $is_primary);
                            $stmt->execute();
                            $is_primary = false;
                        }
                    }
                }
            }
            
            $success = 'Property added successfully! It is pending approval.';
        } else {
            $error = 'Failed to add property';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container add-property-container">
    <h2>Add New Property</h2>
    
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
                <input type="text" id="title" name="title" required class="form-control" placeholder="e.g., Beautiful Villa in Tampa">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5" placeholder="Describe your property..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="address">Address *</label>
                <input type="text" id="address" name="address" required class="form-control" placeholder="Street address">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="city_id">City *</label>
                    <select id="city_id" name="city_id" required class="form-control">
                        <option value="">Select City</option>
                        <?php while ($city = $cities->fetch_assoc()): ?>
                            <option value="<?php echo $city['id']; ?>"><?php echo sanitizeOutput($city['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" required class="form-control">
                        <option value="">Select Category</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo sanitizeOutput($category['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="deal_type_id">Deal Type *</label>
                    <select id="deal_type_id" name="deal_type_id" required class="form-control">
                        <option value="">Select Deal Type</option>
                        <?php while ($type = $deal_types->fetch_assoc()): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo sanitizeOutput($type['name']); ?></option>
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
                    <input type="number" id="price" name="price" required step="0.01" class="form-control" placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <label for="price_unit">Price Unit *</label>
                    <select id="price_unit" name="price_unit" class="form-control">
                        <option value="per_month">Per Month</option>
                        <option value="per_day">Per Day</option>
                        <option value="per_sqft">Per Sq Ft</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" min="0" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="bathrooms">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" min="0" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="square_feet">Square Feet</label>
                    <input type="number" id="square_feet" name="square_feet" step="0.01" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Images</h3>
            
            <div class="form-group">
                <label for="images">Upload Images</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*" class="form-control">
                <small>You can select multiple images. The first image will be the primary image.</small>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Additional Information</h3>
            
            <div class="form-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <textarea id="amenities" name="amenities" class="form-control" rows="3" placeholder="e.g., WiFi, Parking, Pool, Air Conditioning"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="number" id="latitude" name="latitude" step="0.000001" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="number" id="longitude" name="longitude" step="0.000001" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Property</button>
            <a href="/pages/my-properties.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
