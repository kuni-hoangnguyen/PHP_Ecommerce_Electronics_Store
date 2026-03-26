<?php
declare (strict_types = 1);

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function currentRole(): ?string
{
    $role = $_SESSION['role'] ?? null;
    return is_string($role) ? $role : null;
}

function requireGuest(): void
{
    if (isLoggedIn()) {
        redirect('/');
    }
}

function requireAuth(): void
{
    if (! isLoggedIn()) {
        redirect('/login');
    }
}

function authorize(array $allowedRoles): void
{
    requireAuth();

    $role = currentRole();
    if ($role === null || ! in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        echo 'Bạn không có quyền truy cập trang này.';
        exit;
    }
}

function enforceAccess(string $uri): void
{
    // Public routes
        if ($uri === '/login' || $uri === '/signup') {
            requireGuest();
    }

    // Must login 
    // if ($uri === '/profile' || $uri === '/cart') {
    //     requireAuth();
    //     return;
    // }

    $role = currentRole();
    if ($role === 'admin') {
        return; // admin bypass
    }

    if (str_starts_with($uri, '/customer/')) {
        authorize(['customer']);
        return;
    }

    if (str_starts_with($uri, '/admin/')) {
        authorize(['admin']);
        return;
    }
}
