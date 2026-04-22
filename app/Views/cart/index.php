<div class="container mt-4">
    <div class="row g-3 align-items-start">
        <div class="col-md-9">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100">
                <?php
                    $referer     = $_SERVER['HTTP_REFERER'] ?? '';
                    $currentHost = $_SERVER['HTTP_HOST'] ?? '';

                    $refererHost = parse_url($referer, PHP_URL_HOST) ?: '';
                    $refererPath = parse_url($referer, PHP_URL_PATH) ?: '';

                    $backUrl = '/products';

                    $isInternal = $refererPath !== '' && ($refererHost === '' || $refererHost === $currentHost);

                    if ($isInternal && $refererPath !== '/checkout' && $refererPath !== '/cart') {
                        $refererQuery = parse_url($referer, PHP_URL_QUERY);
                        $backUrl      = $refererPath . ($refererQuery ? '?' . $refererQuery : '');
                    }
                ?>
                <a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"
                    class="text-decoration-none text-secondary mb-3 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>

                <h1 class="h4 mb-4">Giỏ hàng của bạn</h1>
                <?php if (! empty($cartItems)): ?>
                <div class="table-responsive">
                    <table class="table cart-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Sản phẩm</th>
                                <th scope="col">Giá</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Tổng</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input cart-item-checkbox"
                                        value="<?php echo (int) $item['id'] ?>">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="/storage/products/<?php echo htmlspecialchars($item['image_path']) ?>"
                                            alt="<?php echo htmlspecialchars($item['name']) ?>" class="rounded"
                                            width="60">
                                        <div class="ms-3">
                                            <p class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($item['quantity']) ?>" min="1"
                                        max="<?php echo htmlspecialchars($item['stock']) ?>"
                                        data-product-id="<?php echo (int) $item['id'] ?>">
                                </td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <form action="/cart/remove" method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo (int) $item['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center mb-4">Giỏ hàng của bạn đang trống.</p>

                <a href="/products" class="btn btn-primary">Xem sản phẩm</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cart-total bg-white p-3 rounded shadow-sm h-100">
                <h2 class="h5 mb-4">Tổng quan đơn hàng</h2>
                <?php if (empty($cartItems)): ?>
                <p class="text-muted mb-0">Không có sản phẩm nào trong giỏ hàng.</p>
                <?php else: ?>
                <p class="mb-2">Tổng sản phẩm:</p>
                <ul class="list-group mb-3" id="selected-summary-list">
                    <li class="list-group-item text-muted" id="selected-summary-empty">Chưa chọn sản phẩm.</li>
                </ul>
                <p class="mb-2 total-price">Tổng tiền: <strong
                        class="text-primary" id="selected-summary-total">0đ</strong>
                </p>
                <button type="button" id="checkout-selected-btn" class="btn btn-primary w-100 mt-3">
                    Tiến hành thanh toán
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>