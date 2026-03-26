<!-- Banner Section -->
<div class="container-fluid p-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="https://placehold.co/1920x600/343a40/ffffff" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 900px;">
                        <h1 class="display-1 text-white mb-md-4">Sản phẩm công nghệ mới nhất</h1>
                        <a href="/products" class="btn btn-primary py-md-3 px-md-5 me-3">Mua ngay</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100" src="https://placehold.co/1920x600/495057/ffffff" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 900px;">
                        <h1 class="display-1 text-white mb-md-4">Giá cả hợp lý</h1>
                        <a href="/products" class="btn btn-primary py-md-3 px-md-5 me-3">Xem sản phẩm</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<div class="container mt-5">

    <!-- Categories Section (Horizontal Slider) -->
    <section class="mb-5">
        <h2 class="text-center mb-4">Danh mục sản phẩm</h2>
        <?php if (!empty($categories)): ?>
            <div class="slider-wrapper">
                <div class="slider" id="categories-slider">
                    <?php foreach ($categories as $category): ?>
                        <div class="slider-item slider-item-category">
                            <a href="/categories/<?= htmlspecialchars($category['id']) ?>" class="card text-decoration-none text-dark text-center h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <i class="bi <?= htmlspecialchars($category['icon'] ?? 'bi-tag') ?> fs-1 text-primary mb-2"></i>
                                    <h5 class="card-title mt-1 mb-0 text-truncate w-100"><?= htmlspecialchars($category['name']) ?></h5>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col">
                    <p class="text-center">Hiện chưa có danh mục nào.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Featured Products Section (Horizontal Slider) -->
    <section class="mb-5">
        <h2 class="text-center mb-4">Sản phẩm nổi bật</h2>
        <?php if (!empty($products)): ?>
            <div class="slider-wrapper">
                <div class="slider" id="products-slider">
                    <?php foreach ($products as $product): ?>
                        <div class="slider-item slider-item-product">
                            <div class="card h-100">
                                <a href="/products/<?= htmlspecialchars($product['id']) ?>">
                                    <img class="card-img-top" src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text fw-bold mb-2"><?= number_format($product['price'], 0, ',', '.') ?>đ</p>
                                    <a href="/products/<?= htmlspecialchars($product['id']) ?>" class="btn btn-primary btn-sm mt-auto">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col">
                    <p class="text-center">Hiện chưa có sản phẩm nổi bật nào.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>



    <!-- Promotions Section -->
    <!-- <section>
        <h2 class="text-center mb-4">Khuyến mãi</h2>
        <div class="row">
            <?php if (!empty($promotions)): ?>
                <?php foreach ($promotions as $promotion): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card bg-light p-4">
                            <h5 class="fw-bold"><?= htmlspecialchars($promotion['title']) ?></h5>
                            <p><?= htmlspecialchars($promotion['description']) ?></p>
                            <a href="#" class="btn btn-outline-primary">Xem ngay</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col">
                    <p class="text-center">Hiện chưa có chương trình khuyến mãi nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </section> -->
</div>