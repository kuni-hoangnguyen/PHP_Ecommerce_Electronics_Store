<div class="container mt-4">
	<div class="row g-3 align-items-start">
		<div class="col-lg-8">
			<div class="bg-white p-3 rounded shadow-sm overflow-hidden h-100">
				<?php
					$referer     = $_SERVER['HTTP_REFERER'] ?? '';
					$currentHost = $_SERVER['HTTP_HOST'] ?? '';

					$refererHost = parse_url($referer, PHP_URL_HOST) ?: '';
					$refererPath = parse_url($referer, PHP_URL_PATH) ?: '';

					$backUrl = '/orders';

					$isInternal = $refererPath !== '' && ($refererHost === '' || $refererHost === $currentHost);

					if ($isInternal && $refererPath !== '/checkout') {
						$refererQuery = parse_url($referer, PHP_URL_QUERY);
						$backUrl      = $refererPath . ($refererQuery ? '?' . $refererQuery : '');
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

					$productRows = [];
					$rawProducts = trim((string) ($order['products'] ?? ''));
					if ($rawProducts !== '') {
						$productRows = explode('|||', $rawProducts);
					}
				?>

				<a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"
					class="text-decoration-none text-secondary mb-3 d-inline-block">
					<i class="bi bi-arrow-left me-1"></i> Quay lại
				</a>

				<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
					<h1 class="h4 mb-0">Chi tiết đơn hàng #<?= (int) ($order['id'] ?? 0) ?></h1>
					<span class="badge <?= $statusClass ?>"><?= htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') ?></span>
				</div>

				<p class="text-muted mb-4">
					Ngày đặt: <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) ($order['created_at'] ?? 'now'))), ENT_QUOTES, 'UTF-8') ?>
				</p>

				<h2 class="h5 mb-3">Danh sách sản phẩm</h2>
				<?php if (! empty($productRows)): ?>
				<div class="table-responsive mb-4">
					<table class="table align-middle mb-0">
						<thead>
							<tr>
								<th scope="col">Sản phẩm</th>
								<th scope="col" class="text-center">Số lượng</th>
								<th scope="col" class="text-end">Đơn giá</th>
								<th scope="col" class="text-end">Thành tiền</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($productRows as $productRow): ?>
							<?php
								$parts = explode(':::', $productRow);
								$productName = (string) ($parts[0] ?? 'Sản phẩm');
								$quantity = (int) ($parts[1] ?? 0);
								$unitPrice = (int) ($parts[2] ?? 0);
								$lineTotal = $quantity * $unitPrice;
							?>
							<tr>
								<td class="fw-semibold"><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?></td>
								<td class="text-center"><?= $quantity ?></td>
								<td class="text-end"><?= number_format($unitPrice, 0, ',', '.') ?>đ</td>
								<td class="text-end fw-semibold"><?= number_format($lineTotal, 0, ',', '.') ?>đ</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php else: ?>
				<p class="text-muted mb-4">Đơn hàng chưa có dữ liệu sản phẩm.</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="bg-white p-3 rounded shadow-sm h-100">
				<h2 class="h5 mb-3">Thông tin nhận hàng</h2>

				<div class="small text-muted mb-1">Họ và tên</div>
				<div class="fw-semibold mb-3"><?= htmlspecialchars((string) ($order['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>

				<div class="small text-muted mb-1">Email</div>
				<div class="fw-semibold mb-3"><?= htmlspecialchars((string) ($order['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>

				<div class="small text-muted mb-1">Số điện thoại</div>
				<div class="fw-semibold mb-3"><?= htmlspecialchars((string) ($order['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>

				<div class="small text-muted mb-1">Địa chỉ</div>
				<div class="fw-semibold mb-4"><?= nl2br(htmlspecialchars((string) ($order['address'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>

				<div class="d-flex justify-content-between align-items-center border-top pt-3">
					<span class="text-muted">Tổng thanh toán</span>
					<span class="h5 mb-0 text-primary"><?= number_format((int) ($order['total_amount'] ?? 0), 0, ',', '.') ?>đ</span>
				</div>
			</div>
		</div>
	</div>
</div>
