<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;

final class AdminController extends Controller
{
    public function index(): void
    {
        header('Location: /admin/dashboard');
        exit;
    }

    public function dashboard(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $this->ensureSchema($pdo);

        $groupBy = strtolower(trim((string) ($_GET['group_by'] ?? 'day')));
        if (! in_array($groupBy, ['day', 'month', 'year'], true)) {
            $groupBy = 'day';
        }

        $today = date('Y-m-d');
        $firstDayOfMonth = date('Y-m-01');
        $currentMonth = date('Y-m');
        $currentYear = date('Y');

        $from = $firstDayOfMonth;
        $to = $today;

        $filter = [
            'group_by' => $groupBy,
            'from_day' => $firstDayOfMonth,
            'to_day' => $today,
            'from_month' => $currentMonth,
            'to_month' => $currentMonth,
            'from_year' => $currentYear,
            'to_year' => $currentYear,
        ];

        if ($groupBy === 'day') {
            $fromDay = $this->normalizeDate((string) ($_GET['from_day'] ?? $_GET['from'] ?? ''), $firstDayOfMonth);
            $toDay = $this->normalizeDate((string) ($_GET['to_day'] ?? $_GET['to'] ?? ''), $today);
            if ($fromDay > $toDay) {
                $tmp = $fromDay;
                $fromDay = $toDay;
                $toDay = $tmp;
            }

            $from = $fromDay;
            $to = $toDay;
            $filter['from_day'] = $fromDay;
            $filter['to_day'] = $toDay;
        } elseif ($groupBy === 'month') {
            $fromMonth = $this->normalizeMonth((string) ($_GET['from_month'] ?? ''), $currentMonth);
            $toMonth = $this->normalizeMonth((string) ($_GET['to_month'] ?? ''), $currentMonth);
            if ($fromMonth > $toMonth) {
                $tmp = $fromMonth;
                $fromMonth = $toMonth;
                $toMonth = $tmp;
            }

            $from = $fromMonth . '-01';
            $toMonthDate = \DateTime::createFromFormat('Y-m-d', $toMonth . '-01');
            $to = $toMonthDate ? $toMonthDate->format('Y-m-t') : $today;
            $filter['from_month'] = $fromMonth;
            $filter['to_month'] = $toMonth;
        } else {
            $fromYear = $this->normalizeYear((string) ($_GET['from_year'] ?? ''), $currentYear);
            $toYear = $this->normalizeYear((string) ($_GET['to_year'] ?? ''), $currentYear);
            if ((int) $fromYear > (int) $toYear) {
                $tmp = $fromYear;
                $fromYear = $toYear;
                $toYear = $tmp;
            }

            $from = $fromYear . '-01-01';
            $to = $toYear . '-12-31';
            $filter['from_year'] = $fromYear;
            $filter['to_year'] = $toYear;
        }

        $this->view('admin/dashboard', [
             ...$this->basePageData('dashboard', 'Dashboard - Almus Tech Admin'),
            'dashboardFilter' => $filter,
            'dashboard' => $this->getDashboardData($pdo, $from, $to, $groupBy),
        ]);
    }

    public function categories(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $this->view('admin/categories', [
             ...$this->basePageData('categories', 'Danh mục - Almus Tech Admin'),
            'categories' => $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll() ?: [],
        ]);
    }

    public function products(): void
    {
        $pdo      = \App\Core\Database::getInstance();
        $productQ = trim((string) ($_GET['product_q'] ?? ''));
        $createProductOldInput = $this->pullCreateProductOldInput();
        $products = $this->searchProducts($pdo, $productQ);
        $productImagesByProductId = $this->getProductImagesByProductId($pdo, $products);

        $this->view('admin/products', [
             ...$this->basePageData('products', 'Sản phẩm - Almus Tech Admin'),
            'productQ'             => $productQ,
            'categories'           => $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll() ?: [],
            'products'             => $products,
            'productImagesByProductId' => $productImagesByProductId,
            'createProductOldInput' => $createProductOldInput,
        ]);
    }

    public function orders(): void
    {
        $pdo    = \App\Core\Database::getInstance();
        $orderQ = trim((string) ($_GET['order_q'] ?? ''));

        $this->view('admin/orders', [
             ...$this->basePageData('orders', 'Đơn hàng - Almus Tech Admin'),
            'orderQ' => $orderQ,
            'orders' => $this->searchOrders($pdo, $orderQ),
        ]);
    }

    public function customers(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $selectedCustomer = null;
        $customerId       = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 0;
        if ($customerId > 0) {
            $stmt = $pdo->prepare('SELECT id, name, email, phone, address, role, is_active, created_at FROM users WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $customerId]);
            $selectedCustomer = $stmt->fetch() ?: null;
        }

        $this->view('admin/customers', [
             ...$this->basePageData('customers', 'Khách hàng - Almus Tech Admin'),
            'customers'        => $pdo->query('SELECT id, name, email, role, is_active, created_at FROM users ORDER BY id DESC')->fetchAll() ?: [],
            'selectedCustomer' => $selectedCustomer,
        ]);
    }

    public function coupons(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $this->ensureSchema($pdo);

        $this->view('admin/coupons', [
             ...$this->basePageData('coupons', 'Coupon - Almus Tech Admin'),
            'coupons' => $pdo->query('SELECT * FROM coupons ORDER BY id DESC')->fetchAll() ?: [],
        ]);
    }

    public function createCategory(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $name        = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $status      = isset($_POST['status']) ? 1 : 0;
        $iconPath    = trim((string) ($_POST['icon'] ?? ''));

        if ($name === '') {
            $this->flashAndRedirect('Tên danh mục không được để trống.', 'danger', '/admin/categories');
        }

        $stmt = $pdo->prepare('INSERT INTO categories (name, description, icon, status) VALUES (:name, :description, :icon, :status)');
        $stmt->execute([
            'name'        => $name,
            'description' => $description !== '' ? $description : null,
            'icon'        => $iconPath,
            'status'      => $status,
        ]);

        $this->flashAndRedirect('Đã thêm danh mục.', 'success', '/admin/categories');
    }

    public function updateCategory(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $id          = (int) ($_POST['id'] ?? 0);
        $name        = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $status      = isset($_POST['status']) ? 1 : 0;
        $icon        = trim((string) ($_POST['icon'] ?? ''));

        if ($id <= 0 || $name === '') {
            $this->flashAndRedirect('Dữ liệu danh mục không hợp lệ.', 'danger', '/admin/categories');
        }

        $currentStmt = $pdo->prepare('SELECT icon FROM categories WHERE id = :id LIMIT 1');
        $currentStmt->execute(['id' => $id]);
        $current = $currentStmt->fetch();
        if (! $current) {
            $this->flashAndRedirect('Không tìm thấy danh mục.', 'danger', '/admin/categories');
        }

        $stmt = $pdo->prepare('UPDATE categories SET name = :name, description = :description, icon = :icon, status = :status WHERE id = :id');
        $stmt->execute([
            'id'          => $id,
            'name'        => $name,
            'description' => $description !== '' ? $description : null,
            'icon'        => $icon !== '' ? $icon : ($current['icon'] ?? null),
            'status'      => $status,
        ]);

        $this->flashAndRedirect('Đã cập nhật danh mục.', 'success', '/admin/categories');
    }

    public function deleteCategory(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $id  = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->flashAndRedirect('Danh mục không hợp lệ.', 'danger', '/admin/categories');
        }

        $inUseStmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $inUseStmt->execute(['id' => $id]);
        $productsCount = (int) ($inUseStmt->fetchColumn() ?: 0);

        if ($productsCount > 0) {
            $this->flashAndRedirect('Không thể xóa danh mục vì đang có sản phẩm thuộc danh mục này.', 'warning', '/admin/categories');
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            $this->flashAndRedirect('Không thể xóa danh mục do ràng buộc dữ liệu.', 'danger', '/admin/categories');
        }

        $this->flashAndRedirect('Đã xóa danh mục.', 'success', '/admin/categories');
    }

    public function createProduct(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $name             = trim((string) ($_POST['name'] ?? ''));
        $categoryId       = (int) ($_POST['category_id'] ?? 0);
        $brand            = trim((string) ($_POST['brand'] ?? ''));
        $shortDescription = trim((string) ($_POST['short_description'] ?? ''));
        $description      = trim((string) ($_POST['description'] ?? ''));
        $price            = (int) ($_POST['price'] ?? 0);
        $salePrice        = (int) ($_POST['sale_price'] ?? 0);
        $stock            = (int) ($_POST['stock'] ?? 0);
        $status           = isset($_POST['status']) ? 1 : 0;

        $specificationJson = $this->buildSpecificationsFromPost();

        if ($name === '' || $categoryId <= 0 || $price <= 0) {
            $this->rememberCreateProductOldInput();
            $this->flashAndRedirectWithSearch('Thiếu dữ liệu sản phẩm bắt buộc.', 'danger', '/admin/products', 'product_q');
        }

        if ($salePrice > 0 && $salePrice >= $price) {
            $this->rememberCreateProductOldInput();
            $this->flashAndRedirectWithSearch('Giá khuyến mãi phải nhỏ hơn giá gốc.', 'danger', '/admin/products', 'product_q');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, name, brand, short_description, description, specifications, price, sale_price, stock, status)
				VALUES (:category_id, :name, :brand, :short_description, :description, :specifications, :price, :sale_price, :stock, :status)'
        );
        $stmt->execute([
            'category_id'       => $categoryId,
            'name'              => $name,
            'brand'             => $brand !== '' ? $brand : null,
            'short_description' => $shortDescription !== '' ? $shortDescription : null,
            'description'       => $description !== '' ? $description : null,
            'specifications'    => $specificationJson,
            'price'             => $price,
            'sale_price'        => $salePrice > 0 ? $salePrice : null,
            'stock'             => max(0, $stock),
            'status'            => $status,
        ]);

        $productId = (int) $pdo->lastInsertId();
        $this->handleMultipleProductImages($pdo, $productId);

        $this->flashAndRedirectWithSearch('Đã thêm sản phẩm.', 'success', '/admin/products', 'product_q');
    }

    public function updateProduct(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $id               = (int) ($_POST['id'] ?? 0);
        $name             = trim((string) ($_POST['name'] ?? ''));
        $categoryId       = (int) ($_POST['category_id'] ?? 0);
        $brand            = trim((string) ($_POST['brand'] ?? ''));
        $shortDescription = trim((string) ($_POST['short_description'] ?? ''));
        $description      = trim((string) ($_POST['description'] ?? ''));
        $price            = (int) ($_POST['price'] ?? 0);
        $salePrice        = (int) ($_POST['sale_price'] ?? 0);
        $stock            = (int) ($_POST['stock'] ?? 0);
        $status           = isset($_POST['status']) ? 1 : 0;

        $specificationJson = $this->buildSpecificationsFromPost();

        if ($id <= 0 || $name === '' || $categoryId <= 0 || $price <= 0) {
            $this->flashAndRedirectWithSearch('Dữ liệu sản phẩm không hợp lệ.', 'danger', '/admin/products', 'product_q');
        }

        if ($salePrice > 0 && $salePrice >= $price) {
            $this->flashAndRedirectWithSearch('Giá khuyến mãi phải nhỏ hơn giá gốc.', 'danger', '/admin/products', 'product_q');
        }

        $stmt = $pdo->prepare(
            'UPDATE products
				SET category_id = :category_id,
					name = :name,
					brand = :brand,
					short_description = :short_description,
					description = :description,
					specifications = :specifications,
					price = :price,
					sale_price = :sale_price,
					stock = :stock,
					status = :status
				WHERE id = :id'
        );
        $stmt->execute([
            'id'                => $id,
            'category_id'       => $categoryId,
            'name'              => $name,
            'brand'             => $brand !== '' ? $brand : null,
            'short_description' => $shortDescription !== '' ? $shortDescription : null,
            'description'       => $description !== '' ? $description : null,
            'specifications'    => $specificationJson,
            'price'             => $price,
            'sale_price'        => $salePrice > 0 ? $salePrice : null,
            'stock'             => max(0, $stock),
            'status'            => $status,
        ]);

        $this->deleteExistingProductImages($pdo, $id);
        $this->handleMultipleProductImages($pdo, $id);
        $this->flashAndRedirectWithSearch('Đã cập nhật sản phẩm.', 'success', '/admin/products', 'product_q');
    }

    public function deleteProduct(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $id  = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->flashAndRedirectWithSearch('Sản phẩm không hợp lệ.', 'danger', '/admin/products', 'product_q');
        }

        $orderDetailStmt = $pdo->prepare('SELECT COUNT(*) FROM order_details WHERE product_id = :id');
        $orderDetailStmt->execute(['id' => $id]);
        $usedInOrders = (int) ($orderDetailStmt->fetchColumn() ?: 0);

        if ($usedInOrders > 0) {
            $this->flashAndRedirectWithSearch('Không thể xóa sản phẩm vì đã phát sinh trong đơn hàng.', 'warning', '/admin/products', 'product_q');
        }

        $imageStmt = $pdo->prepare('SELECT image_path FROM product_images WHERE product_id = :id');
        $imageStmt->execute(['id' => $id]);
        $images = $imageStmt->fetchAll() ?: [];

        try {
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            $this->flashAndRedirectWithSearch('Không thể xóa sản phẩm do ràng buộc dữ liệu.', 'danger', '/admin/products', 'product_q');
        }

        foreach ($images as $image) {
            $this->deleteUploadedFile((string) ($image['image_path'] ?? ''));
        }

        $this->flashAndRedirectWithSearch('Đã xóa sản phẩm.', 'success', '/admin/products', 'product_q');
    }

    public function updateOrderStatus(): void
    {
        $pdo = \App\Core\Database::getInstance();

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status  = trim((string) ($_POST['status'] ?? 'pending'));
        $allowed = ['pending', 'confirmed', 'shipping', 'completed'];

        if ($orderId <= 0 || ! in_array($status, $allowed, true)) {
            $this->flashAndRedirectWithSearch('Thông tin cập nhật đơn hàng không hợp lệ.', 'danger', '/admin/orders', 'order_q');
        }

        $currentStatusStmt = $pdo->prepare('SELECT status FROM orders WHERE id = :id LIMIT 1');
        $currentStatusStmt->execute(['id' => $orderId]);
        $currentStatus = strtolower((string) ($currentStatusStmt->fetchColumn() ?: ''));

        if ($currentStatus === 'canceled') {
            $this->flashAndRedirectWithSearch('Đơn hàng đã hủy và không thể chỉnh sửa.', 'warning', '/admin/orders', 'order_q');
        }

        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $orderId]);

        $this->flashAndRedirectWithSearch('Đã cập nhật trạng thái đơn hàng.', 'success', '/admin/orders', 'order_q');
    }

    public function cancelOrder(): void
    {
        $pdo     = \App\Core\Database::getInstance();
        $orderId = (int) ($_POST['order_id'] ?? 0);

        if ($orderId <= 0) {
            $this->flashAndRedirectWithSearch('Mã đơn không hợp lệ.', 'danger', '/admin/orders', 'order_q');
        }

        $pdo->beginTransaction();

        try {
            $orderStmt = $pdo->prepare('SELECT status FROM orders WHERE id = :id FOR UPDATE');
            $orderStmt->execute(['id' => $orderId]);
            $order = $orderStmt->fetch();

            if (! $order) {
                throw new \RuntimeException('Không tìm thấy đơn hàng.');
            }

            if ((string) $order['status'] === 'canceled') {
                $pdo->rollBack();
                $this->flashAndRedirectWithSearch('Đơn hàng đã ở trạng thái hủy.', 'warning', '/admin/orders', 'order_q');
            }

            $detailStmt = $pdo->prepare('SELECT product_id, quantity FROM order_details WHERE order_id = :order_id');
            $detailStmt->execute(['order_id' => $orderId]);
            $details = $detailStmt->fetchAll() ?: [];

            $restockStmt = $pdo->prepare('UPDATE products SET stock = stock + :quantity WHERE id = :product_id');
            foreach ($details as $detail) {
                $restockStmt->execute([
                    'quantity'   => (int) ($detail['quantity'] ?? 0),
                    'product_id' => (int) ($detail['product_id'] ?? 0),
                ]);
            }

            $updateStmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
            $updateStmt->execute([
                'status' => 'canceled',
                'id'     => $orderId,
            ]);

            $pdo->commit();
            $this->flashAndRedirectWithSearch('Đã hủy đơn và hoàn tồn kho.', 'success', '/admin/orders', 'order_q');
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $this->flashAndRedirectWithSearch('Hủy đơn thất bại: ' . $e->getMessage(), 'danger', '/admin/orders', 'order_q');
        }
    }

    public function toggleCustomerStatus(): void
    {
        $pdo    = \App\Core\Database::getInstance();
        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            $this->flashAndRedirect('Khách hàng không hợp lệ.', 'danger', '/admin/customers');
        }

        $stmt = $pdo->prepare('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id');
        $stmt->execute(['id' => $userId]);

        $this->flashAndRedirect('Đã cập nhật trạng thái tài khoản.', 'success', '/admin/customers');
    }

    public function createCoupon(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $this->ensureSchema($pdo);

        $code          = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $discountType  = trim((string) ($_POST['discount_type'] ?? 'percent'));
        $discountValue = (float) ($_POST['discount_value'] ?? 0);
        $usageLimit    = (int) ($_POST['usage_limit'] ?? 0);
        $expiresAt     = trim((string) ($_POST['expires_at'] ?? ''));
        $expiresAt     = $expiresAt !== '' ? str_replace('T', ' ', $expiresAt) : '';
        $isActive      = isset($_POST['is_active']) ? 1 : 0;

        if ($code === '' || $discountValue <= 0 || ! in_array($discountType, ['percent', 'fixed'], true)) {
            $this->flashAndRedirect('Dữ liệu coupon không hợp lệ.', 'danger', '/admin/coupons');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO coupons (code, discount_type, discount_value, usage_limit, expires_at, is_active)
			 VALUES (:code, :discount_type, :discount_value, :usage_limit, :expires_at, :is_active)'
        );
        $stmt->execute([
            'code'           => $code,
            'discount_type'  => $discountType,
            'discount_value' => $discountValue,
            'usage_limit'    => max(0, $usageLimit),
            'expires_at'     => $expiresAt !== '' ? $expiresAt : null,
            'is_active'      => $isActive,
        ]);

        $this->flashAndRedirect('Đã tạo mã khuyến mãi.', 'success', '/admin/coupons');
    }

    public function updateCoupon(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $this->ensureSchema($pdo);

        $id            = (int) ($_POST['id'] ?? 0);
        $code          = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $discountType  = trim((string) ($_POST['discount_type'] ?? 'percent'));
        $discountValue = (float) ($_POST['discount_value'] ?? 0);
        $usageLimit    = (int) ($_POST['usage_limit'] ?? 0);
        $expiresAt     = trim((string) ($_POST['expires_at'] ?? ''));
        $expiresAt     = $expiresAt !== '' ? str_replace('T', ' ', $expiresAt) : '';
        $isActive      = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || $code === '' || $discountValue <= 0 || ! in_array($discountType, ['percent', 'fixed'], true)) {
            $this->flashAndRedirect('Dữ liệu coupon không hợp lệ.', 'danger', '/admin/coupons');
        }

        $stmt = $pdo->prepare(
            'UPDATE coupons
			 SET code = :code,
				 discount_type = :discount_type,
				 discount_value = :discount_value,
				 usage_limit = :usage_limit,
				 expires_at = :expires_at,
				 is_active = :is_active
			 WHERE id = :id'
        );
        $stmt->execute([
            'id'             => $id,
            'code'           => $code,
            'discount_type'  => $discountType,
            'discount_value' => $discountValue,
            'usage_limit'    => max(0, $usageLimit),
            'expires_at'     => $expiresAt !== '' ? $expiresAt : null,
            'is_active'      => $isActive,
        ]);

        $this->flashAndRedirect('Đã cập nhật coupon.', 'success', '/admin/coupons');
    }

    public function deleteCoupon(): void
    {
        $pdo = \App\Core\Database::getInstance();
        $this->ensureSchema($pdo);

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->flashAndRedirect('Coupon không hợp lệ.', 'danger', '/admin/coupons');
        }

        $stmt = $pdo->prepare('DELETE FROM coupons WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $this->flashAndRedirect('Đã xóa coupon.', 'success', '/admin/coupons');
    }

    /** @return array<string, mixed> */
    private function getDashboardData(\PDO $pdo, string $fromDate, string $toDate, string $groupBy): array
    {
        $start = $fromDate . ' 00:00:00';
        $end = $toDate . ' 23:59:59';

        $summaryStmt = $pdo->prepare(
            "SELECT
                COUNT(*) AS order_count,
                COALESCE(SUM(CASE WHEN status <> 'canceled' THEN total_amount ELSE 0 END), 0) AS total_revenue,
                COALESCE(MAX(CASE WHEN status <> 'canceled' THEN total_amount ELSE NULL END), 0) AS max_order_value,
                COALESCE(MIN(CASE WHEN status <> 'canceled' THEN total_amount ELSE NULL END), 0) AS min_order_value,
                COALESCE(AVG(CASE WHEN status <> 'canceled' THEN total_amount ELSE NULL END), 0) AS avg_order_value
            FROM orders
            WHERE created_at BETWEEN :start AND :end"
        );
        $summaryStmt->execute([
            'start' => $start,
            'end' => $end,
        ]);
        $summary = $summaryStmt->fetch() ?: [];

        $soldProductStatsStmt = $pdo->prepare(
            "SELECT
                COALESCE(MAX(t.sold_qty), 0) AS max_product_sold,
                COALESCE(MIN(t.sold_qty), 0) AS min_product_sold,
                COALESCE(AVG(t.sold_qty), 0) AS avg_product_sold
            FROM (
                SELECT od.product_id, SUM(od.quantity) AS sold_qty
                FROM order_details od
                JOIN orders o ON o.id = od.order_id
                WHERE o.created_at BETWEEN :start AND :end
                  AND o.status <> 'canceled'
                GROUP BY od.product_id
            ) t"
        );
        $soldProductStatsStmt->execute([
            'start' => $start,
            'end' => $end,
        ]);
        $soldProductStats = $soldProductStatsStmt->fetch() ?: [];

        $topProductsStmt = $pdo->prepare(
            "SELECT
                p.id,
                p.name,
                SUM(od.quantity) AS sold_quantity,
                COALESCE(SUM(od.sub_total), 0) AS sold_revenue
            FROM order_details od
            JOIN orders o ON o.id = od.order_id
            JOIN products p ON p.id = od.product_id
            WHERE o.created_at BETWEEN :start AND :end
              AND o.status <> 'canceled'
            GROUP BY p.id, p.name
            ORDER BY sold_quantity DESC, sold_revenue DESC
            LIMIT 10"
        );
        $topProductsStmt->execute([
            'start' => $start,
            'end' => $end,
        ]);
        $topProducts = $topProductsStmt->fetchAll() ?: [];

        $statusStmt = $pdo->prepare(
            "SELECT status, COUNT(*) AS total
            FROM orders
            WHERE created_at BETWEEN :start AND :end
            GROUP BY status"
        );
        $statusStmt->execute([
            'start' => $start,
            'end' => $end,
        ]);
        $statusRows = $statusStmt->fetchAll() ?: [];

        $statusSummary = [
            'processing' => 0,
            'completed' => 0,
            'canceled' => 0,
        ];
        foreach ($statusRows as $statusRow) {
            $status = strtolower((string) ($statusRow['status'] ?? ''));
            $count = (int) ($statusRow['total'] ?? 0);

            if (in_array($status, ['pending', 'confirmed', 'shipping'], true)) {
                $statusSummary['processing'] += $count;
            } elseif ($status === 'completed') {
                $statusSummary['completed'] += $count;
            } elseif ($status === 'canceled') {
                $statusSummary['canceled'] += $count;
            }
        }

        $bucketExpr = 'DATE(o.created_at)';
        if ($groupBy === 'month') {
            $bucketExpr = "DATE_FORMAT(o.created_at, '%Y-%m')";
        } elseif ($groupBy === 'year') {
            $bucketExpr = "DATE_FORMAT(o.created_at, '%Y')";
        }

        $chartSql =
            'SELECT ' . $bucketExpr . ' AS bucket,
                    COUNT(*) AS order_count,
                    COALESCE(SUM(CASE WHEN o.status <> "canceled" THEN o.total_amount ELSE 0 END), 0) AS revenue
             FROM orders o
             WHERE o.created_at BETWEEN :start AND :end
             GROUP BY ' . $bucketExpr . '
             ORDER BY bucket ASC';

        $chartStmt = $pdo->prepare($chartSql);
        $chartStmt->execute([
            'start' => $start,
            'end' => $end,
        ]);
        $chartRows = $chartStmt->fetchAll() ?: [];

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];
        foreach ($chartRows as $chartRow) {
            $bucket = (string) ($chartRow['bucket'] ?? '');
            if ($groupBy === 'day') {
                $dt = \DateTime::createFromFormat('Y-m-d', $bucket);
                $chartLabels[] = $dt ? $dt->format('d/m/Y') : $bucket;
            } elseif ($groupBy === 'month') {
                $dt = \DateTime::createFromFormat('Y-m', $bucket);
                $chartLabels[] = $dt ? $dt->format('m/Y') : $bucket;
            } else {
                $chartLabels[] = $bucket;
            }
            $chartRevenue[] = (int) ($chartRow['revenue'] ?? 0);
            $chartOrders[] = (int) ($chartRow['order_count'] ?? 0);
        }

        $lowStockProducts = $pdo->query('SELECT id, name, stock FROM products WHERE stock <= 10 AND status = 1 ORDER BY stock ASC, id DESC LIMIT 10')->fetchAll() ?: [];

        return [
            'orderCount' => (int) ($summary['order_count'] ?? 0),
            'totalRevenue' => (int) ($summary['total_revenue'] ?? 0),
            'maxOrderValue' => (int) ($summary['max_order_value'] ?? 0),
            'minOrderValue' => (int) ($summary['min_order_value'] ?? 0),
            'avgOrderValue' => (float) ($summary['avg_order_value'] ?? 0),
            'maxProductSold' => (int) ($soldProductStats['max_product_sold'] ?? 0),
            'minProductSold' => (int) ($soldProductStats['min_product_sold'] ?? 0),
            'avgProductSold' => (float) ($soldProductStats['avg_product_sold'] ?? 0),
            'topProducts' => $topProducts,
            'statusSummary' => $statusSummary,
            'chart' => [
                'labels' => $chartLabels,
                'revenue' => $chartRevenue,
                'orders' => $chartOrders,
            ],
            'lowStockProducts' => $lowStockProducts,
        ];
    }

    private function normalizeDate(string $value, string $fallback): string
    {
        $value = trim($value);
        if ($value === '') {
            return $fallback;
        }

        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if (! $dt || $dt->format('Y-m-d') !== $value) {
            return $fallback;
        }

        return $value;
    }

    private function normalizeMonth(string $value, string $fallback): string
    {
        $value = trim($value);
        if ($value === '') {
            return $fallback;
        }

        $dt = \DateTime::createFromFormat('Y-m', $value);
        if (! $dt || $dt->format('Y-m') !== $value) {
            return $fallback;
        }

        return $value;
    }

    private function normalizeYear(string $value, string $fallback): string
    {
        $value = trim($value);
        if (! preg_match('/^\d{4}$/', $value)) {
            return $fallback;
        }

        $year = (int) $value;
        if ($year < 2000 || $year > 2100) {
            return $fallback;
        }

        return (string) $year;
    }

    /** @return array<int, array<string, mixed>> */
    private function searchProducts(\PDO $pdo, string $keyword): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $pdo->query(
                'SELECT p.id, p.category_id, p.name, p.brand, p.short_description, p.description, p.specifications, p.price, p.sale_price, p.stock, p.status, c.name AS category_name
                FROM products p
                JOIN categories c ON c.id = p.category_id
                ORDER BY p.id DESC LIMIT 100')->fetchAll() ?: [];
        }

        $stmt = $pdo->prepare(
            'SELECT p.id, p.category_id, p.name, p.brand, p.short_description, p.description, p.specifications, p.price, p.sale_price, p.stock, p.status, c.name AS category_name
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE p.name LIKE :kw_name OR CAST(p.id AS CHAR) LIKE :kw_id OR c.name LIKE :kw_category OR p.brand LIKE :kw_brand ORDER BY p.id DESC LIMIT 100');

        $stmt->execute([
            'kw_name'     => '%' . $keyword . '%',
            'kw_id'       => '%' . $keyword . '%',
            'kw_category' => '%' . $keyword . '%',
            'kw_brand'    => '%' . $keyword . '%',
        ]);
        return $stmt->fetchAll() ?: [];
    }

    /** @return array<int, array<string, mixed>> */
    private function searchOrders(\PDO $pdo, string $keyword): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            $stmt = $pdo->query('SELECT o.id, o.name, o.email, o.phone, o.total_amount, o.status, o.created_at FROM orders o ORDER BY o.id DESC LIMIT 100');
            return $stmt->fetchAll() ?: [];
        }

        $stmt = $pdo->prepare('SELECT o.id, o.name, o.email, o.phone, o.total_amount, o.status, o.created_at FROM orders o WHERE CAST(o.id AS CHAR) LIKE :kw OR o.name LIKE :kw OR o.email LIKE :kw OR o.phone LIKE :kw ORDER BY o.id DESC LIMIT 100');
        $stmt->execute(['kw' => '%' . $keyword . '%']);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * @param array<int, array<string, mixed>> $products
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function getProductImagesByProductId(\PDO $pdo, array $products): array
    {
        $productIds = [];
        foreach ($products as $product) {
            $id = (int) ($product['id'] ?? 0);
            if ($id > 0) {
                $productIds[] = $id;
            }
        }

        if ($productIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $pdo->prepare(
            "SELECT id, product_id, image_path, is_primary, sort_order
            FROM product_images
            WHERE product_id IN ($placeholders)
            ORDER BY product_id ASC, is_primary DESC, sort_order ASC, id ASC"
        );
        $stmt->execute($productIds);

        $rows = $stmt->fetchAll() ?: [];
        $grouped = [];
        foreach ($rows as $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            if ($productId <= 0) {
                continue;
            }

            if (! isset($grouped[$productId])) {
                $grouped[$productId] = [];
            }

            $grouped[$productId][] = $row;
        }

        return $grouped;
    }

    private function buildSpecificationsFromPost(): ?string
    {
        $names = $_POST['spec_name'] ?? null;
        $values = $_POST['spec_value'] ?? null;

        $specs = [];
        $rowCount = max(count($names), count($values));

        for ($i = 0; $i < $rowCount; $i++) {
            $name = trim((string) ($names[$i] ?? ''));
            if ($name === '') {
                continue;
            }

            $value = trim((string) ($values[$i] ?? ''));
            $specs[$name] = $value;
        }

        if ($specs === []) {
            return null;
        }

        $encoded = json_encode($specs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            return null;
        }

        return $encoded;
    }

    private function handleMultipleProductImages(\PDO $pdo, int $productId): void
    {
        $slots = is_array($_POST['product_image_slot'] ?? null) ? $_POST['product_image_slot'] : [];
        $selectedPrimarySlot = trim((string) ($_POST['product_image_primary'] ?? ''));
        $selectedExistingImageId = 0;
        if (str_starts_with($selectedPrimarySlot, 'existing_')) {
            $selectedExistingImageId = (int) substr($selectedPrimarySlot, 9);
        }

        $clearPrimaryStmt = $pdo->prepare('UPDATE product_images SET is_primary = 0 WHERE product_id = :product_id');
        if ($selectedExistingImageId > 0) {
            $clearPrimaryStmt->execute(['product_id' => $productId]);

            $setExistingPrimaryStmt = $pdo->prepare(
                'UPDATE product_images SET is_primary = 1 WHERE id = :id AND product_id = :product_id LIMIT 1'
            );
            $setExistingPrimaryStmt->execute([
                'id' => $selectedExistingImageId,
                'product_id' => $productId,
            ]);
        }

        if (! isset($_FILES['product_images']) || ! is_array($_FILES['product_images']['name'] ?? null)) {
            return;
        }

        $names    = $_FILES['product_images']['name'];
        $tmpNames = $_FILES['product_images']['tmp_name'];
        $errors   = $_FILES['product_images']['error'];
        $selectedPrimaryIndex = null;

        if ($selectedExistingImageId <= 0) {
            foreach ($names as $index => $name) {
                $slot = (string) ($slots[$index] ?? '');
                if ($slot !== '' && $slot === $selectedPrimarySlot) {
                    $selectedPrimaryIndex = $index;
                    break;
                }
            }
        }

        $hasSelectedPrimary = $selectedPrimaryIndex !== null;
        $hasPrimary = false;
        if (! $hasSelectedPrimary) {
            $existingPrimaryStmt = $pdo->prepare('SELECT COUNT(*) FROM product_images WHERE product_id = :product_id AND is_primary = 1');
            $existingPrimaryStmt->execute(['product_id' => $productId]);
            $hasPrimary = (int) $existingPrimaryStmt->fetchColumn() > 0;
        }

        $insertStmt = $pdo->prepare('INSERT INTO product_images (product_id, image_path, is_primary, sort_order) VALUES (:product_id, :image_path, :is_primary, :sort_order)');
        $hasResetPrimary = false;

        foreach ($names as $index => $name) {
            $error = (int) ($errors[$index] ?? UPLOAD_ERR_NO_FILE);
            if ($error !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpName      = (string) ($tmpNames[$index] ?? '');
            $originalName = (string) $name;
            if ($tmpName === '' || $originalName === '') {
                continue;
            }

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                continue;
            }

            $publicDir = BASE_PATH . '/public/storage/products';
            if (! is_dir($publicDir)) {
                mkdir($publicDir, 0777, true);
            }

            $fileName = $this->generateUploadName($extension);
            $target   = $publicDir . '/' . $fileName;

            if (! move_uploaded_file($tmpName, $target)) {
                continue;
            }

            $isPrimary = 0;
            if ($hasSelectedPrimary) {
                if ($index === $selectedPrimaryIndex) {
                    if (! $hasResetPrimary) {
                        $clearPrimaryStmt->execute(['product_id' => $productId]);
                        $hasResetPrimary = true;
                    }
                    $isPrimary = 1;
                }
            } else {
                $isPrimary = $hasPrimary ? 0 : 1;
                if ($isPrimary === 1) {
                    $hasPrimary = true;
                }
            }

            $insertStmt->execute([
                'product_id' => $productId,
                'image_path' => $fileName,
                'is_primary' => $isPrimary,
                'sort_order' => $index,
            ]);
        }

        $primaryCountStmt = $pdo->prepare('SELECT COUNT(*) FROM product_images WHERE product_id = :product_id AND is_primary = 1');
        $primaryCountStmt->execute(['product_id' => $productId]);
        $hasAnyPrimary = (int) $primaryCountStmt->fetchColumn() > 0;

        if (! $hasAnyPrimary) {
            $pickPrimaryStmt = $pdo->prepare(
                'UPDATE product_images
                 SET is_primary = 1
                 WHERE product_id = :product_id
                 ORDER BY sort_order ASC, id ASC
                 LIMIT 1'
            );
            $pickPrimaryStmt->execute(['product_id' => $productId]);
        }
    }

    private function deleteExistingProductImages(\PDO $pdo, int $productId): void
    {
        $rawIds = $_POST['delete_existing_image_ids'] ?? null;
        if (! is_array($rawIds) || $rawIds === []) {
            return;
        }

        $imageIds = [];
        foreach ($rawIds as $rawId) {
            $id = (int) $rawId;
            if ($id > 0) {
                $imageIds[] = $id;
            }
        }
        $imageIds = array_values(array_unique($imageIds));

        if ($imageIds === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($imageIds), '?'));
        $params = array_merge([$productId], $imageIds);

        $selectStmt = $pdo->prepare(
            "SELECT id, image_path
            FROM product_images
            WHERE product_id = ? AND id IN ($placeholders)"
        );
        $selectStmt->execute($params);
        $rows = $selectStmt->fetchAll() ?: [];

        if ($rows === []) {
            return;
        }

        $deleteStmt = $pdo->prepare(
            "DELETE FROM product_images
            WHERE product_id = ? AND id IN ($placeholders)"
        );
        $deleteStmt->execute($params);

        foreach ($rows as $row) {
            $this->deleteUploadedFile((string) ($row['image_path'] ?? ''));
        }
    }

    private function rememberCreateProductOldInput(): void
    {
        $_SESSION['admin_product_old_input'] = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'brand' => trim((string) ($_POST['brand'] ?? '')),
            'price' => (int) ($_POST['price'] ?? 0),
            'sale_price' => (int) ($_POST['sale_price'] ?? 0),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'short_description' => trim((string) ($_POST['short_description'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status' => isset($_POST['status']) ? 1 : 0,
            'spec_name' => is_array($_POST['spec_name'] ?? null) ? $_POST['spec_name'] : [],
            'spec_value' => is_array($_POST['spec_value'] ?? null) ? $_POST['spec_value'] : [],
        ];
    }

    /** @return array<string, mixed>|null */
    private function pullCreateProductOldInput(): ?array
    {
        $oldInput = $_SESSION['admin_product_old_input'] ?? null;
        unset($_SESSION['admin_product_old_input']);

        return is_array($oldInput) ? $oldInput : null;
    }

    private function generateUploadName(string $extension): string
    {
        return date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    }

    private function deleteUploadedFile(string $path): void
    {
        $trimmed = trim($path);
        if ($trimmed === '') {
            return;
        }

        if (! str_starts_with($trimmed, '/storage/')) {
            $trimmed = '/storage/products/' . ltrim($trimmed, '/');
        }

        $fullPath = BASE_PATH . '/public' . $trimmed;
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private function ensureSchema(\PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS coupons (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				code VARCHAR(60) NOT NULL,
				discount_type ENUM("percent", "fixed") NOT NULL DEFAULT "percent",
				discount_value DECIMAL(15,2) NOT NULL,
				usage_limit INT UNSIGNED NOT NULL DEFAULT 0,
				used_count INT UNSIGNED NOT NULL DEFAULT 0,
				expires_at DATETIME NULL,
				is_active TINYINT NOT NULL DEFAULT 1,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY uq_coupons_code (code)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }

    private function flashAndRedirect(string $message, string $type, string $location): void
    {
        $_SESSION['admin_flash'] = [
            'type'    => $type,
            'message' => $message,
        ];

        header('Location: ' . $location);
        exit;
    }

    private function flashAndRedirectWithSearch(string $message, string $type, string $location, string $queryKey): void
    {
        $queryValue = trim((string) ($_POST[$queryKey] ?? ''));
        if ($queryValue === '') {
            $this->flashAndRedirect($message, $type, $location);
        }

        $separator = str_contains($location, '?') ? '&' : '?';
        $redirectLocation = $location . $separator . rawurlencode($queryKey) . '=' . rawurlencode($queryValue);
        $this->flashAndRedirect($message, $type, $redirectLocation);
    }

    /** @return array<string, mixed> */
    private function basePageData(string $activePage, string $title): array
    {
        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);

        return [
            'title'      => $title,
            'activePage' => $activePage,
            'flash'      => $flash,
        ];
    }
}
