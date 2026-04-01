<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') : 'PHP Native App' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/css/lightslider.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    $isActivePath = static function (string $path, bool $prefix = false) use ($currentPath): bool {
        if ($prefix) {
            $normalizedPath = rtrim($path, '/');

            return $currentPath === $path || str_starts_with($currentPath, $normalizedPath . '/');
        }

        return $currentPath === $path;
    };
    ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="/">ALMUS TECH
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="<?= $isActivePath('/') ? 'nav-link active' : 'nav-link' ?>" href="/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="<?= ($isActivePath('/products', true) || $isActivePath('/category', true)) ? 'nav-link active' : 'nav-link' ?>"
                            href="/products">Danh sách sản phẩm</a>
                    </li>
                </ul>

                <form class="d-flex me-3" role="search" action="/products" method="get">
                    <div class="input-group">
                        <?php if (isset($currentCategoryId) && (int) $currentCategoryId > 0): ?>
                        <input type="hidden" name="category" value="<?= (int) $currentCategoryId ?>">
                        <?php endif; ?>
                        <?php if (isset($currentSort) && (string) $currentSort !== 'newest'): ?>
                        <input type="hidden" name="sort"
                            value="<?= htmlspecialchars((string) $currentSort, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <?php if (isset($currentMinPrice) && isset($priceFloor) && (int) $currentMinPrice > (int) $priceFloor): ?>
                        <input type="hidden" name="min_price" value="<?= (int) $currentMinPrice ?>">
                        <?php endif; ?>
                        <?php if (isset($currentMaxPrice) && isset($priceCeil) && (int) $currentMaxPrice < (int) $priceCeil): ?>
                        <input type="hidden" name="max_price" value="<?= (int) $currentMaxPrice ?>">
                        <?php endif; ?>
                        <input class="form-control border-secondary border-end-0" type="search" name="q"
                            placeholder="Tìm sản phẩm..." aria-label="Search" <?php if (isset($query)): ?>
                            value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>" <?php endif; ?>>
                        <button class="btn btn-outline-secondary border-start-0" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <div class="d-flex align-items-center mt-3 mt-md-1">
                    <a href="/cart" class="btn btn-light position-relative me-3 text-dark">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php
                            $cartItemCount = 0;
                            if (isset($_SESSION['user_id'])) {
                                $pdo = \App\Core\Database::getInstance();
                                $stmt = $pdo->prepare('SELECT COUNT(product_id) FROM cart_items WHERE user_id = :user_id');
                                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                                $cartItemCount = (int) $stmt->fetchColumn();
                            }
                            echo $cartItemCount > 99 ? '99+' : $cartItemCount;
                            ?>
                        </span>
                    </a>

                    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <?= strtoupper(substr($authUser['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <span class="d-none d-lg-block"><?= htmlspecialchars($authUser['name'] ?? 'User') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="background-color: white;"
                            aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="/orders"><i class="bi bi-bag me-2"></i>Đơn hàng</a></li>
                            <?php if (($authUser['role'] ?? '') === 'admin'): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Admin
                                    Dashboard</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="/logout" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <a href="/login" class="btn btn-outline-primary me-2">Đăng nhập</a>
                    <a href="/signup" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <?= $content ?>
    </main>

    <footer class="bg-dark text-white pt-5 pb-4 mt-5 rounded-bottom">
        <div class="container text-center text-md-start">
            <div class="row text-center text-md-start">
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold text-primary">Almus Tech</h5>
                    <p>Cửa hàng điện tử hàng đầu. Chất lượng đã được chứng nhận.</p>
                </div>

                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Sản phẩm</h5>
                    <p><a href="#" class="text-white text-decoration-none">Điện thoại</a></p>
                    <p><a href="#" class="text-white text-decoration-none">Tai nghe</a></p>
                    <p><a href="#" class="text-white text-decoration-none">Đồng hồ thông minh</a></p>
                </div>

                <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Liên kết</h5>
                    <p><a href="/profile" class="text-white text-decoration-none">Tài khoản của tôi</a></p>
                    <p><a href="/cart" class="text-white text-decoration-none">Giỏ hàng</a></p>
                    <p><a href="#" class="text-white text-decoration-none">Trợ giúp</a></p>
                </div>

                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Liên hệ</h5>
                    <p><i class="bi bi-house-door me-2"></i>Việt Nam</p>
                    <p><i class="bi bi-envelope me-2"></i> info@almustech.com</p>
                    <p><i class="bi bi-phone me-2"></i> +84 123 456 789</p>
                </div>
            </div>

            <hr class="mb-4">

            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8">
                    <p class="mb-0">© 2026 Almus Tech. All rights reserved.</p>
                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="text-center text-md-end">
                        <ul class="list-unstyled list-inline">
                            <li class="list-inline-item"><a href="#" class="btn-floating btn-sm text-white fs-5"><i
                                        class="bi bi-facebook"></i></a></li>
                            <li class="list-inline-item"><a href="#" class="btn-floating btn-sm text-white fs-5"><i
                                        class="bi bi-twitter"></i></a></li>
                            <li class="list-inline-item"><a href="#" class="btn-floating btn-sm text-white fs-5"><i
                                        class="bi bi-google"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/js/lightslider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script src="/assets/js/main.js"></script>
</body>

</html>