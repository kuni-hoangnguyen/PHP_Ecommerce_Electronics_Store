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

        '/admin/index'   => ['AdminController', 'index'],

    ],
    'POST' => [
        '/login'            => ['AuthController', 'postLoginForm'],
        '/signup'           => ['AuthController', 'postSignupForm'],

        '/cart/update'      => ['CartController', 'cartUpdate'],
        '/cart/add'    => ['CartController', 'addToCart'],
        '/cart/remove' => ['CartController', 'removeFromCart'],

        '/checkout'         => ['CheckoutController', 'process'],
    ],
];
