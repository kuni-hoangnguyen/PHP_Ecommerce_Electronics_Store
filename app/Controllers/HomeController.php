<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $stmt     = $pdo->query(
            "SELECT p.*,
                    pi.image_path AS image_url
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE status = 1 
            ORDER BY created_at DESC 
            LIMIT 8");
        $products = $stmt->fetchAll();

        $stmt       = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY name ASC");
        $categories = $stmt->fetchAll();

        $this->view('home/index', [
            'title'      => 'Trang chủ - Almus Tech',
            'products'   => $products,
            'categories' => $categories,
        ]);
    }
}
