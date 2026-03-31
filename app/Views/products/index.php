<div class="container mt-4">
    <div class="row g-3 align-items-start">
        <div class="col-md-3">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden">
                <?php
                $currentCategory = isset($currentCategoryId) ? (int) $currentCategoryId : 0;
                $currentQuery = isset($query) ? trim((string) $query) : '';
                $sort = isset($currentSort) ? (string) $currentSort : 'newest';
                $priceMin = isset($currentMinPrice) ? (int) $currentMinPrice : 0;
                $priceMax = isset($currentMaxPrice) ? (int) $currentMaxPrice : 0;
                $priceFloorValue = isset($priceFloor) ? (int) $priceFloor : 0;
                $priceCeilValue = isset($priceCeil) ? (int) $priceCeil : 0;

                $buildProductsUrl = static function (array $params = []) use ($currentCategory, $currentQuery, $sort, $priceMin, $priceMax, $priceFloorValue, $priceCeilValue): string {
                    $base = [];
                    if ($currentCategory > 0) {
                        $base['category'] = $currentCategory;
                    }
                    if ($currentQuery !== '') {
                        $base['q'] = $currentQuery;
                    }
                    if ($sort !== 'newest') {
                        $base['sort'] = $sort;
                    }
                    if ($priceMin > $priceFloorValue) {
                        $base['min_price'] = $priceMin;
                    }
                    if ($priceMax < $priceCeilValue) {
                        $base['max_price'] = $priceMax;
                    }

                    $merged = array_merge($base, $params);
                    $filtered = [];
                    foreach ($merged as $key => $value) {
                        if ($value === null || $value === '' || $value === false) {
                            continue;
                        }
                        if ($key === 'page' && (int) $value <= 1) {
                            continue;
                        }
                        $filtered[$key] = $value;
                    }

                    return '/products' . ($filtered !== [] ? '?' . http_build_query($filtered) : '');
                };
                ?>
                <h1 class="h4 text-center mb-2">Danh mục</h1>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                    <?php $isActiveCategory = (int) ($category['id'] ?? 0) === $currentCategory; ?>
                    <li class="list-group-item rounded <?= $isActiveCategory ? 'active' : '' ?>">
                        <a href="<?= htmlspecialchars($isActiveCategory ? $buildProductsUrl(['category' => null, 'page' => null]) : $buildProductsUrl(['category' => (int) $category['id'], 'page' => null]), ENT_QUOTES, 'UTF-8') ?>"
                            class="text-decoration-none d-flex align-items-center text-dark"
                            <?= $isActiveCategory ? 'aria-current="page"' : '' ?>>
                            <i
                                class="bi <?= htmlspecialchars($category['icon'] ?? 'bi-tag') ?> fs-4 text-primary me-2"></i>
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <h1 class="h4 text-center mt-2">Bộ lọc</h1>
                <form action="/products" method="get" class="mb-3" data-price-filter>
                    <?php if ($currentQuery !== ''): ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <?php if ($currentCategory > 0): ?>
                    <input type="hidden" name="category" value="<?= $currentCategory ?>">
                    <?php endif; ?>

                    <div class="g-3 align-items-end">
                        <div class="mb-3">
                            <label for="sort" class="form-label mb-1">Sắp xếp</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá: thấp đến
                                    cao</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá: cao đến
                                    thấp</option>
                                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Tên: A-Z</option>
                                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Tên: Z-A
                                </option>
                            </select>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1 small text-muted">
                                <span>Khoảng giá</span>
                                <span>
                                    <strong
                                        data-min-price-display><?= number_format($priceMin, 0, ',', '.') ?></strong>đ -
                                    <strong
                                        data-max-price-display><?= number_format($priceMax, 0, ',', '.') ?></strong>đ
                                </span>
                            </div>
                            <div class="price-slider-wrap">
                                <input type="range" class="form-range" name="min_price" min="<?= $priceFloorValue ?>"
                                    max="<?= $priceCeilValue ?>" step="100000" value="<?= $priceMin ?>"
                                    data-min-price-input>
                                <input type="range" class="form-range" name="max_price" min="<?= $priceFloorValue ?>"
                                    max="<?= $priceCeilValue ?>" step="100000" value="<?= $priceMax ?>"
                                    data-max-price-input>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a class="btn btn-outline-secondary btn-sm" href="/products">Đặt lại</a>
                        <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-9">
            <div class="bg-white p-3 rounded shadow-sm overflow-hidden">
                <h1 class="h4 text-center mb-2">Tất cả sản phẩm</h1>
                <?php if (isset($query) && $query !== ''): ?>
                <p class="text-center">Đã tìm thấy <?= (int) $totalProducts ?> sản phẩm cho từ khóa
                    "<strong><?= htmlspecialchars($query) ?></strong>"</p>
                <?php endif; ?>
                <div class="row">
                    <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-3 col-6 mb-4">
                        <div class="card h-100">
                            <a href="/products/<?= htmlspecialchars($product['id']) ?>">
                                <img class="card-img-top" style="height: 216px; object-fit:cover;"
                                    src="/storage/products/<?= htmlspecialchars(image_url($product['image_url'] ?? null), ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text fw-bold mb-2">
                                    <?php if (isset($product['sale_price'])): ?>
                                    <?= number_format($product['sale_price'], 0, ',', '.') ?>đ
                                    <?php else: ?>
                                    <?= number_format($product['price'], 0, ',', '.') ?>đ
                                    <?php endif; ?>
                                </p>
                                <a href="/products/<?= htmlspecialchars($product['id']) ?>"
                                    class="btn btn-primary btn-sm mt-auto">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col">
                        <p class="text-center">Hiện chưa có sản phẩm nào.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (($totalPages ?? 1) > 1): ?>
                <nav aria-label="Product pagination" class="mt-3">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?= ($currentPage ?? 1) <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= htmlspecialchars($buildProductsUrl(['page' => max(1, ($currentPage ?? 1) - 1)]), ENT_QUOTES, 'UTF-8') ?>"
                                aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php for ($page = 1; $page <= ($totalPages ?? 1); $page++): ?>
                        <?php if ($page === 1 || $page === ($totalPages ?? 1) || ($currentPage !== null && abs($page - $currentPage) <= 2)): ?>
                        <li class="page-item <?= $page === ($currentPage ?? 1) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="<?= htmlspecialchars($buildProductsUrl(['page' => $page]), ENT_QUOTES, 'UTF-8') ?>"><?= $page ?></a>
                        </li>
                        <?php elseif ($page === 2 && ($currentPage ?? 1) > 4): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php elseif ($page === ($totalPages ?? 1) - 1 && ($currentPage ?? 1) < (($totalPages ?? 1) - 3)): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <?php endfor; ?>

                        <li class="page-item <?= ($currentPage ?? 1) >= ($totalPages ?? 1) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= htmlspecialchars($buildProductsUrl(['page' => min(($totalPages ?? 1), ($currentPage ?? 1) + 1)]), ENT_QUOTES, 'UTF-8') ?>"
                                aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>