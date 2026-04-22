<?php

declare (strict_types = 1);

return [
    'GET'  => [
        '/'              => ['HomeController', 'index'],

        '/login'         => ['AuthController', 'getLoginForm'],
        '/logout'        => ['AuthController', 'logout'],
        '/signup'        => ['AuthController', 'getSignupForm'],

        '/products'      => ['ProductController', 'index'],
        '/products/{id}' => ['ProductController', 'detail'],

        '/cart'          => ['CartController', 'index'],
        '/checkout'      => ['CheckoutController', 'index'],
        '/orders'         => ['OrderController', 'index'],
        '/orders/{id}'      => ['OrderController', 'detail'],

        '/admin'         => ['AdminController', 'index'],
        '/admin/index'   => ['AdminController', 'index'],
        '/admin/dashboard' => ['AdminController', 'dashboard'],
        '/admin/categories' => ['AdminController', 'categories'],
        '/admin/products' => ['AdminController', 'products'],
        '/admin/orders' => ['AdminController', 'orders'],
        '/admin/customers' => ['AdminController', 'customers'],
        '/admin/coupons' => ['AdminController', 'coupons'],

    ],
    'POST' => [
        '/login'            => ['AuthController', 'postLoginForm'],
        '/signup'           => ['AuthController', 'postSignupForm'],

        '/cart/update'      => ['CartController', 'cartUpdate'],
        '/cart/summary'     => ['CartController', 'summary'],
        '/cart/add'    => ['CartController', 'addToCart'],
        '/cart/remove' => ['CartController', 'removeFromCart'],

        '/checkout'         => ['CheckoutController', 'process'],

        '/admin/categories/create' => ['AdminController', 'createCategory'],
        '/admin/categories/update' => ['AdminController', 'updateCategory'],
        '/admin/categories/delete' => ['AdminController', 'deleteCategory'],

        '/admin/products/create' => ['AdminController', 'createProduct'],
        '/admin/products/update' => ['AdminController', 'updateProduct'],
        '/admin/products/delete' => ['AdminController', 'deleteProduct'],

        '/admin/orders/status' => ['AdminController', 'updateOrderStatus'],
        '/admin/orders/cancel' => ['AdminController', 'cancelOrder'],

        '/admin/customers/toggle' => ['AdminController', 'toggleCustomerStatus'],

        '/admin/coupons/create' => ['AdminController', 'createCoupon'],
        '/admin/coupons/update' => ['AdminController', 'updateCoupon'],
        '/admin/coupons/delete' => ['AdminController', 'deleteCoupon'],
    ],
];
