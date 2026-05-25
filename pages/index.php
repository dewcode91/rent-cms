<?php
// pages/index.php
// Homepage with property listings

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = 'Home - Rent CMS';

// Get filter parameters
$city_id = isset($_GET['city']) ? intval($_GET['city']) : '';
$deal_type_id = isset($_GET['deal_type']) ? intval($_GET['deal_type']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Get cities, deal types, and categories for filters
$cities = $conn->query("SELECT * FROM cities ORDER BY name");
$deal_types = $conn->query("SELECT * FROM deal_types ORDER BY name");
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

// Build properties query
$query = "SELECT p.*, u.username, pi.image_path, ci.name as city_name, dt.name as deal_type_name, cat.name as category_name 
          FROM properties p 
          LEFT JOIN users u ON p.user_id = u.id
          LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
          LEFT JOIN cities ci ON p.city_id = ci.id
          LEFT JOIN deal_types dt ON p.deal_type_id = dt.id
          LEFT JOIN categories cat ON p.category_id = cat.id
          WHERE p.status = 'approved'";

$params = array();
$types = '';

if ($city_id) {
    $query .= " AND p.city_id = ?";
    $params[] = $city_id;
    $types .= 'i';
}

if ($deal_type_id) {
    $query .= " AND p.deal_type_id = ?";
    $params[] = $deal_type_id;
    $types .= 'i';
}

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if ($search) {
    $query .= " AND (MATCH(p.title, p.description, p.address) AGAINST(? IN BOOLEAN MODE) OR p.title LIKE ?)";
    $search_param = $search . '%';
    $params[] = $search;
    $params[] = $search_param;
    $types .= 'ss';
}

$query .= " ORDER BY p.featured DESC, p.created_at DESC LIMIT 12";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Find Your Perfect Property</h1>
        
        <form method="GET" action="" class="search-form">
            <div class="search-filters">
                <select name="city" class="filter-select">
                    <option value="">Select City</option>
                    <?php while($city = $cities->fetch_assoc()): ?>
                        <option value="<?php echo $city['id']; ?>" <?php echo ($city_id == $city['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitizeOutput($city['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="deal_type" class="filter-select">
                    <option value="">Deal Type</option>
                    <?php while($type = $deal_types->fetch_assoc()): ?>
                        <option value="<?php echo $type['id']; ?>" <?php echo ($deal_type_id == $type['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitizeOutput($type['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="category" class="filter-select">
                    <option value="">Category</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitizeOutput($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" class="btn-search">🔍 Search</button>
            </div>
        </form>
    </div>
</div>

<div class="container">
    <div class="listings-header">
        <h2>Popular Listing</h2>
        <a href="#" class="show-map">📍 Show on Map</a>
    </div>
    
    <div class="listings-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($property = $result->fetch_assoc()): ?>
                <div class="property-card">
                    <div class="property-image">
                        <?php if ($property['image_path']): ?>
                            <img src="/uploads/<?php echo sanitizeOutput($property['image_path']); ?>" 
                                 alt="<?php echo sanitizeOutput($property['title']); ?>">
                        <?php else: ?>
                            <img src="/assets/images/placeholder.jpg" alt="Property Image">
                        <?php endif; ?>
                        <span class="price-badge">
                            $<?php echo number_format($property['price'], 0); ?> / <?php echo ucfirst(str_replace('_', ' ', $property['price_unit'])); ?>
                        </span>
                    </div>
                    
                    <div class="property-details">
                        <h3><a href="/pages/property-detail.php?id=<?php echo $property['id']; ?>">
                            <?php echo sanitizeOutput($property['title']); ?>
                        </a></h3>
                        
                        <p class="property-address">
                            📍 <?php echo sanitizeOutput($property['address']); ?>, <?php echo sanitizeOutput($property['city_name']); ?>
                        </p>
                        
                        <div class="property-meta">
                            <span class="badge"><?php echo sanitizeOutput($property['deal_type_name']); ?></span>
                            <span class="badge"><?php echo sanitizeOutput($property['category_name']); ?></span>
                        </div>
                        
                        <div class="property-specs">
                            <?php if ($property['bedrooms']): ?>
                                <span>🛏️ Bedrooms: <?php echo $property['bedrooms']; ?></span>
                            <?php endif; ?>
                            <?php if ($property['bathrooms']): ?>
                                <span>🚿 Bathrooms: <?php echo $property['bathrooms']; ?></span>
                            <?php endif; ?>
                            <?php if ($property['square_feet']): ?>
                                <span>📐 <?php echo number_format($property['square_feet']); ?> sq ft</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-listings">
                <p>No properties found. Try adjusting your search filters.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
