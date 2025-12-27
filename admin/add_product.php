<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required = ['product_name', 'unit_price', 'category', 'stock', 'supplier'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            echo json_encode(['success' => false, 'message' => "$field is required"]);
            exit;
        }
    }

    $product_name = $input['product_name'];
    $unit_price   = $input['unit_price'];
    $category     = $input['category'];
    $stock        = $input['stock'];
    $supplier     = $input['supplier'];

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO products (product_name, unit_price, category, stock, supplier, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sdsis", $product_name, $unit_price, $category, $stock, $supplier);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
