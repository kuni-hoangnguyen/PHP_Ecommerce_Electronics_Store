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

        '/admin/index'   => ['AdminController', 'index'],

    ],
    'POST' => [
        '/login'  => ['AuthController', 'postLoginForm'],
        '/signup' => ['AuthController', 'postSignupForm'],
    ],
];
