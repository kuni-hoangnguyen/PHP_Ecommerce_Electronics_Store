<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $userId = $_SESSION['user_id'] ?? null;

        $selectedRaw = trim((string) ($_GET['selected'] ?? ''));
        $selectedIds = [];

        if ($selectedRaw !== '') {
            $selectedIds = array_values(array_unique(array_filter(
                array_map('intval', explode(',', $selectedRaw)),
                static fn(int $id): bool => $id > 0
            )));
        }

        $cartItems = [];

        $checkoutStmt = $pdo->prepare(
            'SELECT c.quantity, p.id, p.name, p.stock, COALESCE(p.sale_price, p.price) AS price, pi.image_path
            FROM cart_items c
            JOIN products p ON c.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE c.user_id = :user_id'
        );
        $userStmt = $pdo->prepare('SELECT name, email FROM users WHERE id = :user_id');
        $checkoutStmt->execute(['user_id' => $userId ?? 0]);
        $cartItems = $checkoutStmt->fetchAll() ?: [];

        if ($selectedIds !== []) {
            $selectedLookup = array_flip($selectedIds);
            $cartItems = array_values(array_filter(
                $cartItems,
                static fn(array $item): bool => isset($selectedLookup[(int) ($item['id'] ?? 0)])
            ));
        }

        $userStmt->execute(['user_id' => $userId]);
        $user = $userStmt->fetch() ?: [];

        $this->view('checkout/index', [
            'title'     => 'Thanh toán - Almus Tech',
            'cartItems' => $cartItems,
            'user'      => $user,
        ]);
    }

    public function process(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $userId        = $_SESSION['user_id'] ?? null;
        $name          = $_POST['name'] ?? '';
        $email         = $_POST['email'] ?? '';
        $phone         = $_POST['phone'] ?? '';
        $address       = $_POST['address'] ?? '';
        $productIds    = $_POST['product_id'] ?? [];
        $quantities    = $_POST['quantity'] ?? [];
        $unit_prices   = $_POST['unit_price'] ?? [];
        $paymentMethod = $_POST['payment_method'] ?? '';

        if (! is_array($productIds) || $productIds === []) {
            header('Location: /cart');
            exit();
        }

        $orderStmt = $pdo->prepare(
            'INSERT INTO orders (user_id, name, email, phone, address, total_amount, payment_method, created_at)
            VALUES (:user_id, :name, :email, :phone, :address, :total_amount, :payment_method, NOW())'
        );
        $orderDetailStmt = $pdo->prepare(
            'INSERT INTO order_details (order_id, product_id, quantity, unit_price, sub_total)
            VALUES (:order_id, :product_id, :quantity, :unit_price, :sub_total)'
        );
        $updatePrdStmt = $pdo->prepare(
            'UPDATE products SET stock = stock - :quantity_deduct WHERE id = :product_id AND stock >= :quantity_check'
        );
        $deleteCartStmt = $pdo->prepare(
            'DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id'
        );

        $pdo->beginTransaction();

        try {
            $totalAmount = 0;
            foreach ($productIds as $index => $productId) {
                $quantity     = (int) ($quantities[$index] ?? 0);
                $unitPrice    = (float) ($unit_prices[$index] ?? 0);
                $totalAmount += $quantity * $unitPrice;
            }
            $orderStmt->execute([
                'user_id'        => $userId,
                'name'           => $name,
                'email'          => $email,
                'phone'          => $phone,
                'address'        => $address,
                'total_amount'   => $totalAmount,
                'payment_method' => $paymentMethod,
            ]);
            $orderId = (int) $pdo->lastInsertId();
            foreach ($productIds as $index => $productId) {
                $quantity  = (int) ($quantities[$index] ?? 0);
                $unitPrice = (float) ($unit_prices[$index] ?? 0);
                $subTotal  = $quantity * $unitPrice;
                $orderDetailStmt->execute([
                    'order_id'   => $orderId,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'sub_total'  => $subTotal,
                ]);
                $updatePrdStmt->execute([
                    'quantity_deduct' => $quantity,
                    'quantity_check'  => $quantity,
                    'product_id'      => $productId,
                ]);
                $deleteCartStmt->execute([
                    'user_id'    => $userId,
                    'product_id' => $productId,
                ]);
            }
            $pdo->commit();
            header('Location: /orders');
            exit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

    }
}
