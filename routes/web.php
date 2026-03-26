<?php

declare (strict_types = 1);

return [
    'GET'  => [
        '/'                        => ['HomeController', 'index'],
        '/login'                   => ['AuthController', 'getLoginForm'],
        '/logout'                  => ['AuthController', 'logout'],
        '/signup'                  => ['AuthController', 'getSignupForm'],

        '/admin/index'             => ['AdminController', 'index'],

    ],
    'POST' => [
        '/login'             => ['AuthController', 'postLoginForm'],
        '/signup'            => ['AuthController', 'postSignupForm'],
    ],
];
