<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class ProductController extends Controller
{
    public function index(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $perPage           = 12;
        $step              = 100000;
        $requestedPage     = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $currentPage       = $requestedPage !== false && $requestedPage !== null && $requestedPage > 0 ? $requestedPage : 1;
        $query             = trim((string) filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW));
        $sort              = trim((string) filter_input(INPUT_GET, 'sort', FILTER_UNSAFE_RAW));
        $requestedCategory = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
        $currentCategoryId = $requestedCategory !== false && $requestedCategory !== null && $requestedCategory > 0
            ? $requestedCategory
            : null;

        $sortMap = [
            'newest'     => 'p.created_at DESC',
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
        ];
        $currentSort = array_key_exists($sort, $sortMap) ? $sort : 'newest';
        $orderBy     = $sortMap[$currentSort];

        $priceRangeStmt = $pdo->query('SELECT MIN(price) AS min_price, MAX(price) AS max_price, MIN(sale_price) AS min_sale_price, MAX(sale_price) AS max_sale_price FROM products WHERE status = 1');
        $priceRange     = $priceRangeStmt->fetch() ?: ['min_price' => 0, 'max_price' => 0, 'min_sale_price' => 0, 'max_sale_price' => 0];

        if ($priceRange['min_sale_price'] !== null && $priceRange['min_sale_price'] < $priceRange['min_price']) {
            $priceRange['min_price'] = $priceRange['min_sale_price'];
        }
        if ($priceRange['max_sale_price'] !== null && $priceRange['max_sale_price'] > $priceRange['max_price']) {
            $priceRange['max_price'] = $priceRange['max_sale_price'];
        }

        $priceFloor = floor(max(0, (int) floor((float) ($priceRange['min_price'] ?? 0))) / $step) * $step;
        $priceCeil  = ceil(max($priceFloor, (int) ceil((float) ($priceRange['max_price'] ?? 0))) / $step) * $step;

        if ($priceCeil === $priceFloor) {
            $priceCeil = $priceFloor + $step;
        }

        $requestedMinPrice = filter_input(INPUT_GET, 'min_price', FILTER_VALIDATE_INT);
        $requestedMaxPrice = filter_input(INPUT_GET, 'max_price', FILTER_VALIDATE_INT);

        $currentMinPrice = $requestedMinPrice !== false && $requestedMinPrice !== null
            ? (int) $requestedMinPrice
            : $priceFloor;
        $currentMaxPrice = $requestedMaxPrice !== false && $requestedMaxPrice !== null
            ? (int) $requestedMaxPrice
            : $priceCeil;

        $currentMinPrice = max($priceFloor, min($currentMinPrice, $priceCeil));
        $currentMaxPrice = max($priceFloor, min($currentMaxPrice, $priceCeil));
        if ($currentMinPrice > $currentMaxPrice) {
            [$currentMinPrice, $currentMaxPrice] = [$currentMaxPrice, $currentMinPrice];
        }

        $conditions = ['p.status = 1'];
        $bindings   = [];

        $conditions[]           = 'p.price >= :min_price';
        $conditions[]           = 'p.price <= :max_price';
        $bindings[':min_price'] = [$currentMinPrice, \PDO::PARAM_INT];
        $bindings[':max_price'] = [$currentMaxPrice, \PDO::PARAM_INT];

        if ($currentCategoryId !== null) {
            $conditions[]             = 'p.category_id = :category_id';
            $bindings[':category_id'] = [(int) $currentCategoryId, \PDO::PARAM_INT];
        }

        if ($query !== '') {
            $conditions[]                   = '(p.name LIKE :query_name OR p.brand LIKE :query_brand OR p.short_description LIKE :query_description)';
            $searchTerm                     = '%' . $query . '%';
            $bindings[':query_name']        = [$searchTerm, \PDO::PARAM_STR];
            $bindings[':query_brand']       = [$searchTerm, \PDO::PARAM_STR];
            $bindings[':query_description'] = [$searchTerm, \PDO::PARAM_STR];
        }

        $whereClause = implode(' AND ', $conditions);

        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM products p WHERE ' . $whereClause);
        foreach ($bindings as $param => [$value, $type]) {
            $countStmt->bindValue($param, $value, $type);
        }
        $countStmt->execute();
        $totalProducts = (int) $countStmt->fetchColumn();
        $totalPages    = max(1, (int) ceil($totalProducts / $perPage));
        $currentPage   = min($currentPage, $totalPages);
        $offset        = ($currentPage - 1) * $perPage;

        $prodStmt = $pdo->prepare(
            "SELECT p.*,
                    pi.image_path AS image_url
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE {$whereClause}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset"
        );
        foreach ($bindings as $param => [$value, $type]) {
            $prodStmt->bindValue($param, $value, $type);
        }
        $prodStmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $prodStmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $prodStmt->execute();
        $products = $prodStmt->fetchAll();

        $cateStmt   = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY name ASC");
        $categories = $cateStmt->fetchAll();

        $this->view('products/index', [
            'title'             => 'Danh sách sản phẩm - Almus Tech',
            'products'          => $products,
            'categories'        => $categories,
            'currentPage'       => $currentPage,
            'totalPages'        => $totalPages,
            'perPage'           => $perPage,
            'totalProducts'     => $totalProducts,
            'currentCategoryId' => $currentCategoryId,
            'query'             => $query,
            'currentSort'       => $currentSort,
            'priceFloor'        => $priceFloor,
            'priceCeil'         => $priceCeil,
            'currentMinPrice'   => $currentMinPrice,
            'currentMaxPrice'   => $currentMaxPrice,
        ]);
    }

    public function detail(int $productId): void
    {
        $pdo = \App\Core\Database::getInstance();

        $prdStmt = $pdo->prepare(
            "SELECT p.*
            FROM products p
            WHERE p.id = :product_id AND p.status = 1"
        );
        $imgStmt = $pdo->prepare(
            "SELECT id, image_path
            FROM product_images
            WHERE product_id = :product_id"
        );
        $prdStmt->bindValue(':product_id', $productId, \PDO::PARAM_INT);
        $prdStmt->execute();
        $product = $prdStmt->fetch();
        $imgStmt->bindValue(':product_id', $productId, \PDO::PARAM_INT);
        $imgStmt->execute();
        $img = $imgStmt->fetchAll();

        if (! $product) {
            http_response_code(404);
            echo 'Sản phẩm không tồn tại.';
            return;
        }

        $this->view('products/detail', [
            'title'   => htmlspecialchars($product['name'] ?? 'Chi tiết sản phẩm') . ' - Almus Tech',
            'product' => $product,
            'img'     => $img,
        ]);
    }

    public function category(int $categoryId): void
    {
        $query         = trim((string) filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW));
        $requestedPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

        $params = ['category' => $categoryId];
        if ($query !== '') {
            $params['q'] = $query;
        }
        if ($requestedPage !== false && $requestedPage !== null && $requestedPage > 1) {
            $params['page'] = $requestedPage;
        }

        header('Location: /products?' . http_build_query($params));
        exit;
    }

    public function search(): void
    {
        $query             = trim((string) filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW));
        $requestedPage     = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $requestedCategory = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);

        $params = [];
        if ($query !== '') {
            $params['q'] = $query;
        }
        if ($requestedCategory !== false && $requestedCategory !== null && $requestedCategory > 0) {
            $params['category'] = $requestedCategory;
        }
        if ($requestedPage !== false && $requestedPage !== null && $requestedPage > 1) {
            $params['page'] = $requestedPage;
        }

        $target = '/products';
        if ($params !== []) {
            $target .= '?' . http_build_query($params);
        }

        header('Location: ' . $target);
        exit;
    }
}
