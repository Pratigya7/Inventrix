<?php
require_once '../admin/auth.php'; 
require_once '../admin/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['productId'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;

    $productId = intval($productId);
    $quantity = intval($quantity);

    if ($productId <= 0 || $quantity <= 0) {
        echo json_encode(['status'=>'error', 'message'=>'Invalid input']);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // ahile ko stock dekhauxa
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($currentStock);
    if ($stmt->fetch() === null) {
        echo json_encode(['status'=>'error', 'message'=>'Product not found']);
        exit;
    }
    $stmt->close();

    if ($quantity > $currentStock) {
        echo json_encode(['status'=>'error', 'message'=>'Quantity exceeds available stock']);
        exit;
    }

    // stock reduce garchha
    $newStock = $currentStock - $quantity;
    $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $newStock, $productId);
    if ($stmt->execute()) {
        echo json_encode(['status'=>'success', 'newStock'=>$newStock, 'quantity'=>$quantity]);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Database error: '.$stmt->error]);
    }
    $stmt->close();
    $conn->close();
}
