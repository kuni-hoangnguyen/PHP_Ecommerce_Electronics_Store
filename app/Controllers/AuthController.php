<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class AuthController extends Controller
{
    public function getLoginForm(): void
    {
        $this->view('auth/login', ['title' => 'Login']);
    }

    public function postLoginForm(): void
    {
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * from users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ((int) $user['is_active'] === 0) {
                $this->view('auth/login', [
                    'title' => 'Login',
                    'error' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ]);
                return;
            }

            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            header('Location: /');
            exit;
        } else {
            $this->view('auth/login', [
                'title' => 'Login',
                'error' => 'Sai tài khoản email hoặc mật khẩu',
            ]);
        }
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function getSignupForm(): void
    {
        $this->view('auth/signup', ['title' => 'Signup']);
    }

    public function postSignupForm(): void
    {
        $name     = trim((string) ($_POST['name'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));
        $confirm_password = trim((string) ($_POST['confirm_password'] ?? ''));

        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $this->view('auth/signup', [
                'title' => 'Signup',
                'error' => 'Vui lòng điền đầy đủ thông tin.',
            ]);
            return;
        }

        if ($password !== $confirm_password) {
            $this->view('auth/signup', [
                'title' => 'Signup',
                'error' => 'Mật khẩu xác nhận không khớp.',
            ]);
            return;
        }

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * from users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $this->view('auth/signup', [
                'title' => 'Signup',
                'error' => 'Email đã tồn tại. Vui lòng sử dụng email khác.',
            ]);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt         = $pdo->prepare('INSERT INTO users (name, email, password_hash, is_active) VALUES (:name, :email, :password_hash, :is_active)');
        $stmt->execute([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => $passwordHash,
            'is_active'     => 1,
        ]);

        header('Location: /login');
        exit;
    }
}
