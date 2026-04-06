<div class="container py-4">
    <?php require APP_PATH . '/Views/admin/partials/nav.php'; ?>

    <div class="bg-white p-3 rounded shadow-sm">
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Thêm danh mục</h2>
                    <form action="/admin/categories/create" method="POST" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Icon danh mục</label>
                            <input type="text" name="icon" class="form-control" placeholder="VD: bi bi-tag. Tìm ở bootstrap icons">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="category_status" name="status" checked>
                            <label class="form-check-label" for="category_status">Hiển thị</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Thêm danh mục</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Danh sách danh mục</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Icon</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>#<?= (int) ($category['id'] ?? 0) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars((string) ($category['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td>
                                        <?php $icon = (string) ($category['icon'] ?? ''); ?>
                                        <span class="text-muted"><?= htmlspecialchars($icon !== '' ? $icon : 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td>
                                        <?php if ((int) ($category['status'] ?? 0) === 1): ?>
                                        <span class="badge bg-success">Hiển thị</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#cat-edit-<?= (int) ($category['id'] ?? 0) ?>">Sửa</button>
                                        <form action="/admin/categories/delete" method="POST" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                                            <input type="hidden" name="id" value="<?= (int) ($category['id'] ?? 0) ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse" id="cat-edit-<?= (int) ($category['id'] ?? 0) ?>">
                                    <td colspan="5">
                                        <form action="/admin/categories/update" method="POST" enctype="multipart/form-data" class="border rounded p-3 mt-1">
                                            <input type="hidden" name="id" value="<?= (int) ($category['id'] ?? 0) ?>">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Tên danh mục</label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Icon</label>
                                                    <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars((string) ($category['icon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: bi bi-tag. Tìm ở bootstrap icons">
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="status" id="status_cat_<?= (int) ($category['id'] ?? 0) ?>" <?= (int) ($category['status'] ?? 0) === 1 ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="status_cat_<?= (int) ($category['id'] ?? 0) ?>">Hiển thị</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Mô tả</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars((string) ($category['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">Lưu</button>
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
