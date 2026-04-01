<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class CartController extends Controller
{
    public function index(): void
    {
        $pdo = \App\Core\Database::getInstance();

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
        header('Location: /cart');
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
