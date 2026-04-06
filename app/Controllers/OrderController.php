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

    public function detail(int $orderId): void
    {
        $pdo = \App\Core\Database::getInstance();

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
            WHERE o.id = :order_id AND o.user_id = :user_id
            GROUP BY o.id'
        );
        $stmt->execute([
            'order_id' => $orderId,
            'user_id'  => $_SESSION['user_id'] ?? 0,
        ]);
        $order = $stmt->fetch();

        if (! $order) {
            http_response_code(404);
            echo 'Đơn hàng không tồn tại.';
            return;
        }

        $this->view('order/detail', [
            'title' => 'Chi tiết đơn hàng #' . (int) ($order['id'] ?? 0) . ' - Almus Tech',
            'order' => $order,
        ]);
    }
}