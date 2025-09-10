<?php
declare(strict_types=1);
session_start();

/* ===== DB ===== */
const DB_HOST    = '127.0.0.1';
const DB_NAME    = 'fechzone';
const DB_USER    = 'root';
const DB_PASS    = '';
const DB_CHARSET = 'utf8mb4';

/* ===== APP BASE (đường dẫn gốc của app dưới htdocs) ===== */
const BASE_URL = '/fechzone';   // đổi nếu bạn di chuyển thư mục dự án

/* ===== URL helper ===== */
function url(string $path): string {
  if ($path === '' || $path[0] !== '/') $path = '/'.$path;
  return BASE_URL . $path;
}

/* ===== PDO ===== */
function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

/* ===== JSON response ===== */
function json_response($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

/* ===== AUTH & ROLES ===== */
function current_user(): ?array {
  // kỳ vọng lưu trong session dạng: ['id'=>..,'username'=>..,'role'=>'user|admin']
  return $_SESSION['user'] ?? null;
}
function is_admin(): bool {
  return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function require_login_page(): void {
  if (!current_user()) {
    header('Location: ' . url('/backend/customers/login.php'));
    exit;
  }
}
function require_admin_page(): void {
  if (!current_user()) {
    header('Location: ' . url('/backend/customers/login.php'));
    exit;
  }
  if (!is_admin()) {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1><p>Bạn không có quyền truy cập trang này.</p>';
    exit;
  }
}

/* ===== Password utils ===== */
function hash_password(string $plain): string {
  return password_hash($plain, PASSWORD_BCRYPT);
}
function verify_password(string $plain, string $hash): bool {
  return password_verify($plain, $hash);
}
