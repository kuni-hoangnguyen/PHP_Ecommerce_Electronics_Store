<div class="container mt-4">
    <div class="row g-3 align-items-start">
        <div class="col-md-8">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100">
                <?php
                    $referer     = $_SERVER['HTTP_REFERER'] ?? '';
                    $currentHost = $_SERVER['HTTP_HOST'] ?? '';

                    $refererHost = parse_url($referer, PHP_URL_HOST) ?: '';
                    $refererPath = parse_url($referer, PHP_URL_PATH) ?: '';

                    $backUrl = '/products';

                    $isInternal = $refererPath !== '' && ($refererHost === '' || $refererHost === $currentHost);

                    if ($isInternal && $refererPath !== '/checkout') {
                        $refererQuery = parse_url($referer, PHP_URL_QUERY);
                        $backUrl      = $refererPath . ($refererQuery ? '?' . $refererQuery : '');
                    }
                ?>
                <a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none text-secondary mb-3 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <h2 class="mb-4">Thông tin giao hàng</h2>
                <form action="/checkout" method="POST">
                    <?php if (! empty($cartItems)): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <input type="hidden" name="product_id[]" value="<?php echo (int) $item['id'] ?>">
                        <input type="hidden" name="quantity[]" value="<?php echo (int) $item['quantity'] ?>">
                        <input type="hidden" name="unit_price[]" value="<?php echo (int) $item['price'] ?>">
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ giao hàng</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Tiến hành thanh toán</button>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white p-3 rounded shadow-sm h-100">
                <h2 class="mb-4">Đơn hàng của bạn</h2>
                <?php if (! empty($cartItems)): ?>
                <ul class="list-group mb-3">
                    <?php foreach ($cartItems as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="/storage/products/<?php echo htmlspecialchars(image_url($item['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?php echo htmlspecialchars($item['name']) ?>" class="me-3"
                                style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($item['name']) ?></h6>
                                <small class="text-muted">Số lượng: <?php echo (int) $item['quantity'] ?></small>
                                <p class="mb-0">
                                    <strong><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</strong>
                                </p>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>