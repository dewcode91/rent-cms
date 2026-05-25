<?php
// pages/delete-property.php
// Delete property

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

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

// Delete property and related images
$conn->query("DELETE FROM property_images WHERE property_id = $property_id");
$conn->query("DELETE FROM inquiries WHERE property_id = $property_id");
$conn->query("DELETE FROM properties WHERE id = $property_id");

header('Location: /pages/my-properties.php');
exit;
?>
