<div class="container py-4">
    <?php require APP_PATH . '/Views/admin/partials/nav.php'; ?>

    <div class="bg-white p-3 rounded shadow-sm">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Danh sách khách hàng</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td>#<?= (int) ($customer['id'] ?? 0) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars((string) ($customer['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars((string) ($customer['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars((string) ($customer['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ((int) ($customer['is_active'] ?? 0) === 1): ?>
                                        <span class="badge bg-success">Đang hoạt động</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Đã khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-outline-primary btn-sm" href="/admin/customers?customer_id=<?= (int) ($customer['id'] ?? 0) ?>">Chi tiết</a>
                                        <form action="/admin/customers/toggle" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= (int) ($customer['id'] ?? 0) ?>">
                                            <button class="btn btn-outline-warning btn-sm" type="submit"><?= (int) ($customer['is_active'] ?? 0) === 1 ? 'Khóa' : 'Mở' ?></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="border rounded p-3 h-100">
                    <h2 class="h5 mb-3">Chi tiết khách hàng</h2>
                    <?php if ($selectedCustomer): ?>
                    <div class="small text-muted">ID</div>
                    <div class="fw-semibold mb-2">#<?= (int) ($selectedCustomer['id'] ?? 0) ?></div>
                    <div class="small text-muted">Họ tên</div>
                    <div class="fw-semibold mb-2"><?= htmlspecialchars((string) ($selectedCustomer['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-muted">Email</div>
                    <div class="fw-semibold mb-2"><?= htmlspecialchars((string) ($selectedCustomer['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-muted">Số điện thoại</div>
                    <div class="fw-semibold mb-2"><?= htmlspecialchars((string) ($selectedCustomer['phone'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-muted">Địa chỉ</div>
                    <div class="fw-semibold mb-2"><?= nl2br(htmlspecialchars((string) ($selectedCustomer['address'] ?? 'N/A'), ENT_QUOTES, 'UTF-8')) ?></div>
                    <div class="small text-muted">Ngày tạo</div>
                    <div class="fw-semibold"><?= htmlspecialchars((string) ($selectedCustomer['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    <?php else: ?>
                    <p class="text-muted mb-0">Chọn một khách hàng để xem chi tiết.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
