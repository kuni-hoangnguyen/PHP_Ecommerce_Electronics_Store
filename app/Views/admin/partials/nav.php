<?php
$active = isset($activePage) ? (string) $activePage : '';

if ($active === '') {
    $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $active = isset($segments[1]) ? (string) $segments[1] : 'dashboard';

    if ($active === '' || $active === 'index') {
        $active = 'dashboard';
    }
}

$flashType = (string) ($flash['type'] ?? '');
$flashMessage = (string) ($flash['message'] ?? '');

$linkClass = function ($target, $active) {
    return $target === $active ? 'btn btn-primary btn-sm' : 'btn btn-outline-primary btn-sm';
};
?>

<div class="bg-white p-3 rounded shadow-sm mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h3 mb-1">Admin Dashboard</h1>
            <p class="text-muted mb-0">Quản trị dữ liệu.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/admin/dashboard" class="<?= $linkClass('dashboard', $active) ?>">Tổng quan</a>
            <a href="/admin/categories" class="<?= $linkClass('categories', $active) ?>">Danh mục</a>
            <a href="/admin/products" class="<?= $linkClass('products', $active) ?>">Sản phẩm</a>
            <a href="/admin/orders" class="<?= $linkClass('orders', $active) ?>">Đơn hàng</a>
            <a href="/admin/customers" class="<?= $linkClass('customers', $active) ?>">Khách hàng</a>
            <!-- <a href="/admin/coupons" class="<?= $linkClass('coupons', $active) ?>">Coupon</a> -->
        </div>
    </div>

    <?php if ($flashMessage !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars($flashType !== '' ? $flashType : 'info', ENT_QUOTES, 'UTF-8') ?> mb-0 mt-3">
        <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
</div>
