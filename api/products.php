<?php

require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

try {

    $sql = "
        SELECT
            p.product_id,
            p.name,
            p.description,
            p.price,
            p.stock_quantity,
            p.image_url,
            c.name AS category_name
        FROM products p
        LEFT JOIN categories c
            ON p.category_id = c.category_id
        ORDER BY p.created_at DESC
    ";

    $result = $conn->query($sql);

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode([
        'success' => true,
        'count' => count($products),
        'data' => $products
    ]);
} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve products'
    ]);
}
