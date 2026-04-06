<div class="container mt-4">
    <div class="row g-3 align-items-start">
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

            <h1 class="h4 mb-4">Đơn hàng của bạn</h1>
            <?php if (! empty($orders)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Mã đơn</th>
                            <th scope="col">Ngày đặt</th>
                            <th scope="col">Tình trạng</th>
                            <th scope="col">Khách hàng</th>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col" class="text-end">Tổng tiền</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <?php
                        $productRows = [];
                        $rawProducts = trim((string) ($order['products'] ?? ''));
                        if ($rawProducts !== '') {
                            $productRows = explode('|||', $rawProducts);
                        }

                        $rawStatus = strtolower((string) ($order['status'] ?? 'pending'));
                        $statusText = 'Chờ xử lý';
                        $statusClass = 'bg-warning text-dark';

                        if ($rawStatus === 'pending') {
                            $statusText = 'Chờ xử lý';
                            $statusClass = 'bg-warning text-dark';
                        } elseif ($rawStatus === 'confirmed') {
                            $statusText = 'Đã xác nhận';
                            $statusClass = 'bg-info text-dark';
                        } elseif ($rawStatus === 'shipping') {
                            $statusText = 'Đang giao';
                            $statusClass = 'bg-primary';
                        } elseif ($rawStatus === 'completed') {
                            $statusText = 'Hoàn thành';
                            $statusClass = 'bg-success';
                        } elseif ($rawStatus === 'canceled') {
                            $statusText = 'Đã hủy';
                            $statusClass = 'bg-danger';
                        }
                        ?>
                        <tr>
                            <td>#<?= (int) ($order['id'] ?? 0) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) ($order['created_at'] ?? 'now'))), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars((string) ($order['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars((string) ($order['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td>
                                <div class="small text-muted mb-1">Số dòng sản phẩm: <?= count($productRows) ?></div>
                                <?php if (! empty($productRows)): ?>
                                <ul class="mb-0 ps-3 small">
                                    <?php foreach ($productRows as $productRow): ?>
                                    <?php
                                    $parts = explode(':::', $productRow);
                                    $productName = (string) ($parts[0] ?? 'Sản phẩm');
                                    $quantity = (int) ($parts[1] ?? 0);
                                    $unitPrice = (int) ($parts[2] ?? 0);
                                    ?>
                                    <li><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?> - SL: <?= $quantity ?> - Đơn giá: <?= number_format($unitPrice, 0, ',', '.') ?>đ</li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold"><?= number_format((int) ($order['total_amount'] ?? 0), 0, ',', '.') ?>đ</td>
                            <td><a href="/orders/<?= (int) ($order['id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center mb-4">Bạn chưa có đơn hàng nào.</p>

            <a href="/products" class="btn btn-primary">Xem sản phẩm</a>
            <?php endif; ?>
        </div>
    </div>
</div>