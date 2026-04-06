<div class="container py-4">
    <?php require APP_PATH . '/Views/admin/partials/nav.php'; ?>
    <?php
        $createOld        = is_array($createProductOldInput ?? null) ? $createProductOldInput : [];
        $productImagesMap = is_array($productImagesByProductId ?? null) ? $productImagesByProductId : [];
        $createSpecNames  = is_array($createOld['spec_name'] ?? null) ? $createOld['spec_name'] : [];
        $createSpecValues = is_array($createOld['spec_value'] ?? null) ? $createOld['spec_value'] : [];
        $createSpecRows   = [];
        $createSpecCount  = max(count($createSpecNames), count($createSpecValues));

        for ($i = 0; $i < $createSpecCount; $i++) {
            $createSpecRows[] = [
                'name'  => trim((string) ($createSpecNames[$i] ?? '')),
                'value' => trim((string) ($createSpecValues[$i] ?? '')),
            ];
        }

        if ($createSpecRows === []) {
            $createSpecRows[] = ['name' => '', 'value' => ''];
        }
    ?>

    <div class="bg-white p-3 rounded shadow-sm">
        <div class="row g-3">
            <div class="col-12">
                <form method="GET" action="/admin/products" class="row g-2 mb-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="product_q"
                            value="<?php echo htmlspecialchars((string) ($productQ ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Tìm theo tên/ thương hiệu...">
                    </div>
                    <div class="col-md-4 d-grid">
                        <button class="btn btn-outline-primary" type="submit">Tìm sản phẩm</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Thêm sản phẩm</h2>
                    <form action="/admin/products/create" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_q"
                            value="<?php echo htmlspecialchars((string) ($productQ ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="mb-2">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" name="name" class="form-control"
                                value="<?php echo htmlspecialchars((string) ($createOld['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int) ($category['id'] ?? 0) ?>"
                                    <?php echo (int) ($createOld['category_id'] ?? 0) === (int) ($category['id'] ?? 0) ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Thương hiệu</label>
                            <input type="text" name="brand" class="form-control"
                                value="<?php echo htmlspecialchars((string) ($createOld['brand'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Giá gốc</label>
                            <input type="number" name="price" class="form-control" min="0" step="1000"
                                value="<?php echo (int) ($createOld['price'] ?? 0) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Giá khuyến mãi</label>
                            <input type="number" name="sale_price" class="form-control" min="0" step="1000"
                                value="<?php echo (int) ($createOld['sale_price'] ?? 0) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tồn kho</label>
                            <input type="number" name="stock" class="form-control" min="0"
                                value="<?php echo array_key_exists('stock', $createOld) ? (int) $createOld['stock'] : 0 ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mô tả ngắn</label>
                            <textarea name="short_description" class="form-control"
                                rows="2"><?php echo htmlspecialchars((string) ($createOld['short_description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control"
                                rows="3"><?php echo htmlspecialchars((string) ($createOld['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="mb-2" data-spec-builder>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Thông số</label>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-spec-add>+ Thêm
                                    thông số</button>
                            </div>
                            <div class="d-grid gap-2" data-spec-rows>
                                <?php foreach ($createSpecRows as $createSpecRow): ?>
                                <div class="row g-2" data-spec-row>
                                    <div class="col-5">
                                        <input type="text" class="form-control" name="spec_name[]" data-spec-name
                                            placeholder="name (vd: ram)"
                                            value="<?php echo htmlspecialchars((string) ($createSpecRow['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control" name="spec_value[]" data-spec-value
                                            placeholder="value (vd: 8GB)"
                                            value="<?php echo htmlspecialchars((string) ($createSpecRow['value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="col-2 d-grid">
                                        <button type="button" class="btn btn-outline-danger" data-spec-remove>X</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-2" data-image-builder>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Ảnh sản phẩm</label>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-image-add>+ Thêm
                                    ảnh</button>
                            </div>
                            <div class="d-grid gap-2" data-image-rows>
                                <div class="row g-2 align-items-center" data-image-row>
                                    <div class="col-5">
                                        <input type="file" class="form-control" name="product_images[]" data-image-file
                                            accept=".jpg,.jpeg,.png,.webp,.gif">
                                        <input type="hidden" name="product_image_slot[]" data-image-slot
                                            value="slot_create_0">
                                    </div>
                                    <div class="col-3">
                                        <img src="" alt="preview" class="img-thumbnail d-none" data-image-preview
                                            style="max-width: 72px; max-height: 72px; object-fit: cover;">
                                    </div>
                                    <div class="col-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="product_image_primary"
                                                value="slot_create_0" data-image-primary checked>
                                            <label class="form-check-label">Ảnh chính</label>
                                        </div>
                                    </div>
                                    <div class="col-1 d-grid">
                                        <button type="button" class="btn btn-outline-danger"
                                            data-image-remove>X</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="product_status" name="status"
                                <?php echo (int) ($createOld['status'] ?? 1) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="product_status">Hiển thị</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Thêm sản phẩm</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Danh sách sản phẩm</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Danh mục</th>
                                    <th>Giá</th>
                                    <th>Tồn kho</th>
                                    <th>TT</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            <?php echo htmlspecialchars((string) ($product['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?php echo htmlspecialchars((string) ($product['brand'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars((string) ($product['category_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <div><?php echo number_format((int) ($product['price'] ?? 0), 0, ',', '.') ?>đ</div>
                                        <?php if (! empty($product['sale_price'])): ?>
                                        <div class="small text-danger">KM:
                                            <?php echo number_format((int) ($product['sale_price'] ?? 0), 0, ',', '.') ?>đ
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo (int) ($product['stock'] ?? 0) ?></td>
                                    <td>
                                        <?php if ((int) ($product['status'] ?? 0) === 1): ?>
                                        <span class="badge bg-success">Hiển thị</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse"
                                            data-bs-target="#product-edit-<?php echo (int) ($product['id'] ?? 0) ?>">Sửa</button>
                                        <form action="/admin/products/delete" method="POST" class="d-inline"
                                            onsubmit="return confirm('Xóa sản phẩm này?')">
                                            <input type="hidden" name="id" value="<?php echo (int) ($product['id'] ?? 0) ?>">
                                            <input type="hidden" name="product_q"
                                                value="<?php echo htmlspecialchars((string) ($productQ ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                            <button class="btn btn-outline-danger btn-sm" type="submit">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse" id="product-edit-<?php echo (int) ($product['id'] ?? 0) ?>">
                                    <td colspan='7'>
                                        <?php
                                            $currentProductId = (int) ($product['id'] ?? 0);
                                            $existingImages   = is_array($productImagesMap[$currentProductId] ?? null) ? $productImagesMap[$currentProductId] : [];
                                        ?>
                                        <form action="/admin/products/update" method="POST"
                                            enctype="multipart/form-data" class="border rounded p-3 mt-1">
                                            <input type="hidden" name="id" value="<?php echo (int) ($product['id'] ?? 0) ?>">
                                            <input type="hidden" name="product_q"
                                                value="<?php echo htmlspecialchars((string) ($productQ ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Tên sản phẩm</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="<?php echo htmlspecialchars((string) ($product['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Danh mục</label>
                                                    <select name="category_id" class="form-select" required>
                                                        <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo (int) ($category['id'] ?? 0) ?>"
                                                            <?php echo (int) ($product['category_id'] ?? 0) === (int) ($category['id'] ?? 0) ? 'selected' : '' ?>>
                                                            <?php echo htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Thương hiệu</label>
                                                    <input type="text" class="form-control" name="brand"
                                                        value="<?php echo htmlspecialchars((string) ($product['brand'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giá</label>
                                                    <input type="number" class="form-control" name="price" min="0"
                                                        step="1000" value="<?php echo (int) ($product['price'] ?? 0) ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giá KM</label>
                                                    <input type="number" class="form-control" name="sale_price" min="0"
                                                        step="1000" value="<?php echo (int) ($product['sale_price'] ?? 0) ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Tồn kho</label>
                                                    <input type="number" class="form-control" name="stock" min="0"
                                                        value="<?php echo (int) ($product['stock'] ?? 0) ?>">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="status"
                                                            id="status_product_<?php echo (int) ($product['id'] ?? 0) ?>"
                                                            <?php echo (int) ($product['status'] ?? 0) === 1 ? 'checked' : '' ?>>
                                                        <label class="form-check-label"
                                                            for="status_product_<?php echo (int) ($product['id'] ?? 0) ?>">Hiển
                                                            thị</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Mô tả ngắn</label>
                                                    <textarea class="form-control" name="short_description"
                                                        rows="2"><?php echo htmlspecialchars((string) ($product['short_description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Mô tả chi tiết</label>
                                                    <textarea class="form-control" name="description"
                                                        rows="2"><?php echo htmlspecialchars((string) ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                                </div>
                                                <div class="col-md-6" data-spec-builder>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label mb-0">Thông số</label>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                            data-spec-add>+ Thêm thông số</button>
                                                    </div>
                                                    <div class="d-grid gap-2" data-spec-rows>
                                                        <?php
                                                            $specRows          = [];
                                                            $rawSpecifications = (string) ($product['specifications'] ?? '');
                                                            if ($rawSpecifications !== '') {
                                                                $decoded = json_decode($rawSpecifications, true);
                                                                if (is_array($decoded)) {
                                                                    foreach ($decoded as $specName => $specValue) {
                                                                        $specRows[] = [
                                                                            'name'  => (string) $specName,
                                                                            'value' => is_scalar($specValue) ? (string) $specValue : json_encode($specValue),
                                                                        ];
                                                                    }
                                                                }
                                                            }
                                                            if ($specRows === []) {
                                                                $specRows[] = ['name' => '', 'value' => ''];
                                                            }
                                                        ?>
                                                        <?php foreach ($specRows as $specRow): ?>
                                                        <div class="row g-2" data-spec-row>
                                                            <div class="col-5">
                                                                <input type="text" class="form-control"
                                                                    name="spec_name[]" data-spec-name
                                                                    placeholder="name (vd: ram)"
                                                                    value="<?php echo htmlspecialchars((string) ($specRow['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                            </div>
                                                            <div class="col-5">
                                                                <input type="text" class="form-control"
                                                                    name="spec_value[]" data-spec-value
                                                                    placeholder="value (vd: 8GB)"
                                                                    value="<?php echo htmlspecialchars((string) ($specRow['value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                            </div>
                                                            <div class="col-2 d-grid">
                                                                <button type="button" class="btn btn-outline-danger"
                                                                    data-spec-remove>X</button>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <textarea class="d-none"
                                                        name="specifications"><?php echo htmlspecialchars((string) ($product['specifications'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                                </div>
                                                <div class="col-md-6" data-image-builder>
                                                    <div class="border rounded p-2 mb-2">
                                                        <label class="form-label mb-2">Ảnh hiện có</label>
                                                        <?php if ($existingImages !== []): ?>
                                                        <div class="d-grid gap-2">
                                                            <?php foreach ($existingImages as $existingImage): ?>
                                                            <div class="border rounded p-2">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <img src="/storage/products/<?php echo htmlspecialchars((string) ($existingImage['image_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        alt="product image"
                                                                        class="img-fluid rounded border"
                                                                        style="height: 56px; width: 56px; object-fit: cover;">
                                                                    <div class="flex-grow-1">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="product_image_primary"
                                                                                value="existing_<?php echo (int) ($existingImage['id'] ?? 0) ?>"
                                                                                id="primary_existing_image_<?php echo (int) ($existingImage['id'] ?? 0) ?>"
                                                                                <?php echo (int) ($existingImage['is_primary'] ?? 0) === 1 ? 'checked' : '' ?>>
                                                                            <label class="form-check-label"
                                                                                for="primary_existing_image_<?php echo (int) ($existingImage['id'] ?? 0) ?>">Chọn
                                                                                làm ảnh chính</label>
                                                                        </div>
                                                                        <div class="form-check mt-1">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                name="delete_existing_image_ids[]"
                                                                                value="<?php echo (int) ($existingImage['id'] ?? 0) ?>"
                                                                                id="delete_image_<?php echo (int) ($existingImage['id'] ?? 0) ?>">
                                                                            <label class="form-check-label text-danger"
                                                                                for="delete_image_<?php echo (int) ($existingImage['id'] ?? 0) ?>">Xóa
                                                                                ảnh này</label>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ((int) ($existingImage['is_primary'] ?? 0) === 1): ?>
                                                                    <span class="badge bg-primary">Chính</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="small text-muted">Chưa có ảnh nào.</div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label mb-0">Thêm ảnh mới</label>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                            data-image-add>+ Thêm ảnh</button>
                                                    </div>
                                                    <div class="d-grid gap-2" data-image-rows>
                                                        <div class="row g-2 align-items-center" data-image-row>
                                                            <div class="col-5">
                                                                <input type="file" class="form-control"
                                                                    name="product_images[]" data-image-file
                                                                    accept=".jpg,.jpeg,.png,.webp,.gif">
                                                                <input type="hidden" name="product_image_slot[]"
                                                                    data-image-slot
                                                                    value="slot_update_<?php echo (int) ($product['id'] ?? 0) ?>_0">
                                                            </div>
                                                            <div class="col-3">
                                                                <img src="" alt="preview" class="img-thumbnail d-none"
                                                                    data-image-preview
                                                                    style="max-width: 72px; max-height: 72px; object-fit: cover;">
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="product_image_primary"
                                                                        value="slot_update_<?php echo (int) ($product['id'] ?? 0) ?>_0"
                                                                        data-image-primary
                                                                        <?php echo $existingImages === [] ? 'checked' : '' ?>>
                                                                    <label class="form-check-label">Ảnh chính
                                                                        (mới)</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-1 d-grid">
                                                                <button type="button" class="btn btn-outline-danger"
                                                                    data-image-remove>X</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary btn-sm mt-2" type="submit">Lưu thay
                                                đổi</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>