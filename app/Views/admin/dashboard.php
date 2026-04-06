<div class="container py-4">
    <?php require APP_PATH . '/Views/admin/partials/nav.php'; ?>

    <?php
    $filter = is_array($dashboardFilter ?? null) ? $dashboardFilter : [];
    $groupBy = (string) ($filter['group_by'] ?? 'day');
    $fromDay = (string) ($filter['from_day'] ?? date('Y-m-01'));
    $toDay = (string) ($filter['to_day'] ?? date('Y-m-d'));
    $fromMonth = (string) ($filter['from_month'] ?? date('Y-m'));
    $toMonth = (string) ($filter['to_month'] ?? date('Y-m'));
    $fromYear = (string) ($filter['from_year'] ?? date('Y'));
    $toYear = (string) ($filter['to_year'] ?? date('Y'));
    $statusSummary = is_array($dashboard['statusSummary'] ?? null) ? $dashboard['statusSummary'] : ['processing' => 0, 'completed' => 0, 'canceled' => 0];
    $chart = is_array($dashboard['chart'] ?? null) ? $dashboard['chart'] : ['labels' => [], 'revenue' => [], 'orders' => []];
    $chartLabels = htmlspecialchars((string) json_encode($chart['labels'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
    $chartRevenue = htmlspecialchars((string) json_encode($chart['revenue'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
    $chartOrders = htmlspecialchars((string) json_encode($chart['orders'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
    ?>

    <div class="bg-white p-3 rounded shadow-sm">
        <form method="GET" action="/admin/dashboard" class="row g-2 align-items-end mb-3" id="dashboard-filter-form">
            <div class="col-md-4">
                <?php if ($groupBy === 'day'): ?>
                <label class="form-label">Từ ngày</label>
                <input type="date" class="form-control" name="from_day" value="<?= htmlspecialchars($fromDay, ENT_QUOTES, 'UTF-8') ?>">
                <?php elseif ($groupBy === 'month'): ?>
                <label class="form-label">Từ tháng</label>
                <input type="month" class="form-control" name="from_month" value="<?= htmlspecialchars($fromMonth, ENT_QUOTES, 'UTF-8') ?>">
                <?php else: ?>
                <label class="form-label">Từ năm</label>
                <input type="number" class="form-control" name="from_year" min="2000" max="2100" step="1" value="<?= htmlspecialchars($fromYear, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <?php if ($groupBy === 'day'): ?>
                <label class="form-label">Đến ngày</label>
                <input type="date" class="form-control" name="to_day" value="<?= htmlspecialchars($toDay, ENT_QUOTES, 'UTF-8') ?>">
                <?php elseif ($groupBy === 'month'): ?>
                <label class="form-label">Đến tháng</label>
                <input type="month" class="form-control" name="to_month" value="<?= htmlspecialchars($toMonth, ENT_QUOTES, 'UTF-8') ?>">
                <?php else: ?>
                <label class="form-label">Đến năm</label>
                <input type="number" class="form-control" name="to_year" min="2000" max="2100" step="1" value="<?= htmlspecialchars($toYear, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Thống kê theo</label>
                <select class="form-select" name="group_by" id="dashboard-group-by">
                    <option value="day" <?= $groupBy === 'day' ? 'selected' : '' ?>>Ngày</option>
                    <option value="month" <?= $groupBy === 'month' ? 'selected' : '' ?>>Tháng</option>
                    <option value="year" <?= $groupBy === 'year' ? 'selected' : '' ?>>Năm</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary" type="submit">Áp dụng</button>
            </div>
        </form>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Tổng số đơn hàng</div>
                    <div class="h3 mb-0"><?= (int) ($dashboard['orderCount'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Tổng doanh thu</div>
                    <div class="h3 mb-0"><?= number_format((int) ($dashboard['totalRevenue'] ?? 0), 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Sản phẩm sắp hết hàng</div>
                    <div class="h3 mb-0"><?= count($dashboard['lowStockProducts'] ?? []) ?></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Giá trị đơn hàng lớn nhất</div>
                    <div class="h5 mb-0"><?= number_format((int) ($dashboard['maxOrderValue'] ?? 0), 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Giá trị đơn hàng nhỏ nhất</div>
                    <div class="h5 mb-0"><?= number_format((int) ($dashboard['minOrderValue'] ?? 0), 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">Giá trị đơn hàng trung bình</div>
                    <div class="h5 mb-0"><?= number_format((int) round((float) ($dashboard['avgOrderValue'] ?? 0)), 0, ',', '.') ?>đ</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">SL bán lớn nhất / sản phẩm</div>
                    <div class="h5 mb-0"><?= (int) ($dashboard['maxProductSold'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">SL bán nhỏ nhất / sản phẩm</div>
                    <div class="h5 mb-0"><?= (int) ($dashboard['minProductSold'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted small">SL bán trung bình / sản phẩm</div>
                    <div class="h5 mb-0"><?= number_format((float) ($dashboard['avgProductSold'] ?? 0), 2, ',', '.') ?></div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="border rounded p-3 h-100">
                    <h2 class="h5 mb-3">Biểu đồ doanh thu + số đơn</h2>
                    <canvas
                        id="dashboardRevenueOrderChart"
                        height="110"
                        data-chart-labels="<?= $chartLabels ?>"
                        data-chart-revenue="<?= $chartRevenue ?>"
                        data-chart-orders="<?= $chartOrders ?>"
                    ></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="border rounded p-3 h-100">
                    <h2 class="h5 mb-3">Đơn hàng theo trạng thái</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Đang xử lý</span>
                        <span class="fw-semibold"><?= (int) ($statusSummary['processing'] ?? 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Đã giao</span>
                        <span class="fw-semibold"><?= (int) ($statusSummary['completed'] ?? 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Đã hủy</span>
                        <span class="fw-semibold"><?= (int) ($statusSummary['canceled'] ?? 0) ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Top sản phẩm bán chạy</h2>
                    <?php if (! empty($dashboard['topProducts'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">Đã bán</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($dashboard['topProducts'] ?? []) as $idx => $topProduct): ?>
                                <tr>
                                    <td><?= (int) $idx + 1 ?></td>
                                    <td><?= htmlspecialchars((string) ($topProduct['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end"><?= (int) ($topProduct['sold_quantity'] ?? 0) ?></td>
                                    <td class="text-end"><?= number_format((int) ($topProduct['sold_revenue'] ?? 0), 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-0">Chưa có dữ liệu bán hàng trong khoảng thời gian này.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12">
                <div class="border rounded p-3">
                    <h2 class="h5 mb-3">Danh sách sắp hết hàng</h2>
                    <?php if (! empty($dashboard['lowStockProducts'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th class="text-end">Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard['lowStockProducts'] as $lowStockItem): ?>
                                <tr>
                                    <td>#<?= (int) ($lowStockItem['id'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars((string) ($lowStockItem['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end"><?= (int) ($lowStockItem['stock'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-0">Chưa có sản phẩm tồn kho thấp.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
