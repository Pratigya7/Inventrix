<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id']) || empty($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit;
    }

    $id = $input['id'];
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

    $stmt = $conn->prepare("UPDATE products SET product_name=?, unit_price=?, category=?, stock=?, supplier=? WHERE id=?");
    $stmt->bind_param("ssdssi", $product_name, $unit_price, $category, $stock, $supplier, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update product']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
