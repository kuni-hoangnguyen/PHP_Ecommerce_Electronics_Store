<div class="container mt-4">
    <?php
    $flashType = (string) ($flash['type'] ?? '');
    $flashMessage = (string) ($flash['message'] ?? '');
    ?>

    <?php if ($flashMessage !== ''): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flashType !== '' ? $flashType : 'info', ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

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
                <a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none text-secondary mb-3 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <h1 class="h4 mb-4"><?php echo htmlspecialchars($product['name'] ?? 'Chi tiết sản phẩm') ?></h1>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="vrmedia-gallery">
                            <ul class="ecommerce-gallery">
                                <?php foreach ($img as $image): ?>
                                <li data-thumb="/storage/products/<?php echo htmlspecialchars(image_url($image['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                                    data-src="/storage/products/<?php echo htmlspecialchars(image_url($image['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>">
                                    <img class="w-100"
                                        src="/storage/products/<?php echo htmlspecialchars(image_url($image['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?php echo htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($product['sale_price'])): ?>
                        <h2 class="text-primary"><?php echo number_format($product['sale_price'] ?? 0, 0, ',', '.') ?>đ
                        </h2>
                        <h5 class="text-muted mb-3">
                            <del><?php echo number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</del>
                        </h5>
                        <?php else: ?>
                        <h2 class="text-primary mb-3"><?php echo number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</h2>
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả nào.')) ?></p>
                        <form action="/cart/add" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']) ?>">
                            <button type="submit" class="btn btn-success mt-3">Thêm vào giỏ hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100 mb-3">
                <h5 class="mb-3">Thông tin sản phẩm</h5>
                <ul class="list-unstyled">
                    <li><strong>Mô tả ngắn:</strong>
                        <?php echo htmlspecialchars($product['short_description'] ?? 'Không có thông tin') ?></li>
                    <li><strong>Thương hiệu:</strong> <?php echo htmlspecialchars($product['brand'] ?? 'Không có thông tin') ?>
                    </li>
                    <li><strong>Sẵn hàng:</strong> <?php echo htmlspecialchars($product['stock'] ?? 0) ?></li>
                </ul>
            </div>
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100 mb-3">
                <h5>Thông tin chi tiết</h5>
                <ul class="list-unstyled">
                    <?php if (isset($product['specifications']) && is_array(json_decode($product['specifications'], true))): ?>
                        <?php foreach (json_decode($product['specifications'], true) as $key => $value): ?>
                            <li><strong><?php echo htmlspecialchars(ucfirst($key)) ?>:</strong> <?php echo htmlspecialchars($value) ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>