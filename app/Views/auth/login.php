    <div class="container py-4">
        <!-- Breadcrumb placeholder if needed inside views -->
        <div class="bg-white p-0 rounded shadow-sm overflow-hidden" style="min-height: 400px;">
            <div class="row justify-content-center my-5">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h3 mb-3 text-center">Đăng nhập</h1>
                            <p class="text-muted text-center mb-4">Nhập tài khoản để truy cập hệ thống.</p>

                            <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>

                            <form action="/login" method="POST">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input class="form-control" type="email" id="email" name="email" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password">Mật khẩu</label>
                                    <input class="form-control" type="password" id="password" name="password" required>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>