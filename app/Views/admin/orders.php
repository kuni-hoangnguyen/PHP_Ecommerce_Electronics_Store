<div class="container py-4">
    <?php require APP_PATH . '/Views/admin/partials/nav.php'; ?>

    <?php
    $statusMap = [
        'pending' => ['Chờ xử lý', 'bg-warning text-dark'],
        'confirmed' => ['Đã xác nhận', 'bg-info text-dark'],
        'shipping' => ['Đang giao', 'bg-primary'],
        'completed' => ['Hoàn thành', 'bg-success'],
        'canceled' => ['Đã hủy', 'bg-danger'],
    ];
    ?>

    <div class="bg-white p-3 rounded shadow-sm">
        <form method="GET" action="/admin/orders" class="row g-2 mb-3">
            <div class="col-md-8">
                <input type="text" class="form-control" name="order_q" value="<?= htmlspecialchars((string) ($orderQ ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm theo mã đơn / khách hàng / email / SĐT...">
            </div>
            <div class="col-md-4 d-grid">
                <button class="btn btn-outline-primary" type="submit">Tìm đơn hàng</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày tạo</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <?php
                    $rawOrderStatus = strtolower((string) ($order['status'] ?? 'pending'));
                    $mappedOrderStatus = $statusMap[$rawOrderStatus] ?? ['Không xác định', 'bg-secondary'];
                    $isCanceled = $rawOrderStatus === 'canceled';
                    ?>
                    <tr>
                        <td>#<?= (int) ($order['id'] ?? 0) ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars((string) ($order['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="small text-muted"><?= htmlspecialchars((string) ($order['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) ($order['created_at'] ?? 'now'))), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format((int) ($order['total_amount'] ?? 0), 0, ',', '.') ?>đ</td>
                        <td><span class="badge <?= $mappedOrderStatus[1] ?>"><?= htmlspecialchars($mappedOrderStatus[0], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <form action="/admin/orders/status" method="POST" class="d-flex gap-1 mb-1">
                                <input type="hidden" name="order_id" value="<?= (int) ($order['id'] ?? 0) ?>">
                                <input type="hidden" name="order_q" value="<?= htmlspecialchars((string) ($orderQ ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <select name="status" class="form-select form-select-sm" <?= $isCanceled ? 'disabled' : '' ?>>
                                    <?php foreach (['pending' => 'Chờ xử lý', 'confirmed' => 'Đã xác nhận', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành'] as $statusKey => $statusLabel): ?>
                                    <option value="<?= $statusKey ?>" <?= $rawOrderStatus === $statusKey ? 'selected' : '' ?>><?= $statusLabel ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-primary btn-sm" type="submit" <?= $isCanceled ? 'disabled' : '' ?>>Lưu</button>
                            </form>
                            <form action="/admin/orders/cancel" method="POST" onsubmit="return confirm('Hủy đơn này và hoàn tồn kho?')">
                                <input type="hidden" name="order_id" value="<?= (int) ($order['id'] ?? 0) ?>">
                                <input type="hidden" name="order_q" value="<?= htmlspecialchars((string) ($orderQ ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <button class="btn btn-outline-danger btn-sm w-100" type="submit" <?= $isCanceled ? 'disabled' : '' ?>>Hủy + hoàn tồn</button>
                            </form>
                            <?php if ($isCanceled): ?>
                            <div class="small text-muted mt-1">Đơn đã hủy, không thể chỉnh sửa.</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
