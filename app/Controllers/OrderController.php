<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class OrderController extends Controller
{
    public function index(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $orders = [];
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare(
                'SELECT o.id, o.name, o.email, o.phone, o.address, o.total_amount, o.status, o.created_at,
                    GROUP_CONCAT(
                        CONCAT(
                            REPLACE(REPLACE(COALESCE(p.name, CONCAT("SP #", od.product_id)), "|||", " "), ":::", " "),
                            ":::",
                            od.quantity,
                            ":::",
                            od.unit_price
                        )
                        SEPARATOR "|||"
                    ) AS products
                FROM orders o
                JOIN order_details od ON o.id = od.order_id
                LEFT JOIN products p ON p.id = od.product_id
                WHERE o.user_id = :user_id
                GROUP BY o.id
                ORDER BY o.created_at DESC'
            );
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $orders = $stmt->fetchAll() ?: [];
        }

        $this->view('order/index', [
            'title'  => 'Đơn hàng của tôi - Almus Tech',
            'orders' => $orders,
        ]);
    }
}