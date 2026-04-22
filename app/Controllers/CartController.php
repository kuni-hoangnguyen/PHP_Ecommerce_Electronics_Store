<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class CartController extends Controller
{
    public function index(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $flash = $_SESSION['cart_flash'] ?? null;
        unset($_SESSION['cart_flash']);

        $cartItems = [];

        $stmt = $pdo->prepare(
            'SELECT c.quantity, p.id, p.name, p.stock, COALESCE(p.sale_price, p.price) AS price, pi.image_path
            FROM cart_items c
            JOIN products p ON c.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE c.user_id = :user_id'
        );
        $stmt->execute(['user_id' => $_SESSION['user_id'] ?? 0]);
        $cartItems = $stmt->fetchAll() ?: [];

        $this->view('cart/index', [
            'title'     => 'Giỏ hàng - Almus Tech',
            'cartItems' => $cartItems,
            'flash'     => $flash,
        ]);
    }

    public function cartUpdate(): void
    {

        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? null;

        $payload = json_decode(file_get_contents('php://input'), true);

        $pdo = \App\Core\Database::getInstance();

        $productId = (int) ($payload['product_id'] ?? 0);
        $quantity  = (int) ($payload['quantity'] ?? 1);
        $quantity  = max(1, $quantity);

        $stmt = $pdo->prepare(
            'UPDATE cart_items
            SET quantity = :quantity
            WHERE user_id = :user_id AND product_id = :product_id'
        );
        $stmt->execute([
            'quantity'   => $quantity,
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);
        echo json_encode(['success' => true]);
    }

    public function summary(): void
    {
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? null;
        if (! $userId) {
            echo json_encode([
                'success' => false,
                'items' => [],
                'total_quantity' => 0,
                'total_amount' => 0,
            ]);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $rawProductIds = is_array($payload['product_ids'] ?? null) ? $payload['product_ids'] : [];
        $productIds = array_values(array_unique(array_filter(
            array_map('intval', $rawProductIds),
            static fn(int $id): bool => $id > 0
        )));

        if ($productIds === []) {
            echo json_encode([
                'success' => true,
                'items' => [],
                'total_quantity' => 0,
                'total_amount' => 0,
            ]);
            return;
        }

        $pdo = \App\Core\Database::getInstance();
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = sprintf(
            'SELECT c.product_id, c.quantity, p.name, COALESCE(p.sale_price, p.price) AS price
             FROM cart_items c
             JOIN products p ON p.id = c.product_id
             WHERE c.user_id = ? AND c.product_id IN (%s)',
            $placeholders
        );

        $stmt = $pdo->prepare($sql);
        $bindValues = array_merge([(int) $userId], $productIds);
        $stmt->execute($bindValues);
        $rows = $stmt->fetchAll() ?: [];

        $items = [];
        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($rows as $row) {
            $quantity = (int) ($row['quantity'] ?? 0);
            $price = (int) ($row['price'] ?? 0);
            $subTotal = $quantity * $price;

            $items[] = [
                'product_id' => (int) ($row['product_id'] ?? 0),
                'name' => (string) ($row['name'] ?? ''),
                'quantity' => $quantity,
                'price' => $price,
                'sub_total' => $subTotal,
            ];

            $totalQuantity += $quantity;
            $totalAmount += $subTotal;
        }

        echo json_encode([
            'success' => true,
            'items' => $items,
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
        ]);
    }

    public function addToCart(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $userId    = $_SESSION['user_id'] ?? null;

        if (! $userId) {
            header('Location: /login');
            exit;
        }

        $pdo  = \App\Core\Database::getInstance();
        $stmt = $pdo->prepare(
            'INSERT INTO cart_items (user_id, product_id, quantity)
            VALUES (:user_id, :product_id, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1'
        );
        $stmt->execute([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);

        $_SESSION['cart_flash'] = [
            'type'    => 'success',
            'message' => 'Thêm vào giỏ hàng thành công.',
        ];

        header('Location: /products/' . $productId);
        exit;
    }

    public function removeFromCart(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $userId    = $_SESSION['user_id'] ?? null;

        if (! $userId) {
            header('Location: /login');
            exit;
        }

        $pdo  = \App\Core\Database::getInstance();
        $stmt = $pdo->prepare(
            'DELETE FROM cart_items
            WHERE user_id = :user_id AND product_id = :product_id'
        );
        $stmt->execute([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);
        header('Location: /cart');
        exit;
    }
}
