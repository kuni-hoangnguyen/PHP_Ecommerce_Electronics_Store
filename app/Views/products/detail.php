<div class="container mt-4">
    <div class="row g-3 align-items-start">
        <div class="col-md-9">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100">
                <a href="/products" class="text-decoration-none text-secondary mb-3 d-inline-block"><i
                        class="bi bi-arrow-left me-1"></i> Quay lại</a>
                <h1 class="h4 mb-4"><?= htmlspecialchars($product['name'] ?? 'Chi tiết sản phẩm') ?></h1>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="vrmedia-gallery">
                            <ul class="ecommerce-gallery">
                                <?php foreach ($img as $image): ?>
                                <li data-thumb="/storage/products/<?= htmlspecialchars(image_url($image['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                                    data-src="/storage/products/<?= htmlspecialchars(image_url($image['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>">
                                    <img class="w-100"
                                        src="/storage/products/<?= htmlspecialchars(image_url($image['image_path'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($product['sale_price'])): ?>
                        <h2 class="text-primary"><?= number_format($product['sale_price'] ?? 0, 0, ',', '.') ?>đ
                        </h2>
                        <h5 class="text-muted mb-3">
                            <del><?= number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</del>
                        </h5>
                        <?php else: ?>
                        <h2 class="text-primary mb-3"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</h2>
                        <?php endif; ?>
                        <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả nào.')) ?></p>
                        <a href="/cart/add/<?= htmlspecialchars($product['id']) ?>" class="btn btn-success mt-3">Thêm
                            vào giỏ hàng</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100 mb-3">
                <h5 class="mb-3">Thông tin sản phẩm</h5>
                <ul class="list-unstyled">
                    <li><strong>Mô tả ngắn:</strong>
                        <?= htmlspecialchars($product['short_description'] ?? 'Không có thông tin') ?></li>
                    <li><strong>Thương hiệu:</strong> <?= htmlspecialchars($product['brand'] ?? 'Không có thông tin') ?>
                    </li>
                    <li><strong>Sẵn hàng:</strong> <?= htmlspecialchars($product['stock'] ?? 0) ?></li>
                </ul>
            </div>
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100 mb-3">
                <h5>Thông tin chi tiết</h5>
                <ul class="list-unstyled">
                    <?php if (isset($product['specifications']) && is_array(json_decode($product['specifications'], true))): ?>
                        <?php foreach (json_decode($product['specifications'], true) as $key => $value): ?>
                            <li><strong><?= htmlspecialchars(ucfirst($key)) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>