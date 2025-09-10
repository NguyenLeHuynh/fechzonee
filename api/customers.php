<?php
// FECHZONE/api/customers.php
require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
  if ($method === 'GET' && $action === 'list') {
    $q = db()->query('SELECT id, username, created_at FROM customers ORDER BY id DESC');
    json_response(['ok'=>true, 'data'=>$q->fetchAll()]);
  }

  if ($method === 'POST' && $action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') json_response(['ok'=>false,'msg'=>'Thiếu username/password'], 400);

    $st = db()->prepare('SELECT id FROM customers WHERE username = ?');
    $st->execute([$username]);
    if ($st->fetch()) json_response(['ok'=>false,'msg'=>'Username đã tồn tại'], 409);

    $st = db()->prepare('INSERT INTO customers(username, password_hash) VALUES(?, ?)');
    $st->execute([$username, hash_password($password)]);
    json_response(['ok'=>true, 'msg'=>'Đăng ký thành công']);
  }

  if ($method === 'POST' && $action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $st = db()->prepare('SELECT id, username, password_hash FROM customers WHERE username = ?');
    $st->execute([$username]);
    $u = $st->fetch();
    if (!$u || !verify_password($password, $u['password_hash'])) {
      json_response(['ok'=>false,'msg'=>'Sai tài khoản hoặc mật khẩu'], 401);
    }
    $_SESSION['user'] = ['id'=>$u['id'], 'username'=>$u['username']];
    json_response(['ok'=>true, 'user'=>$_SESSION['user']]);
  }

  if ($method === 'POST' && $action === 'logout') {
    unset($_SESSION['user']);
    json_response(['ok'=>true]);
  }

  // default
  json_response(['ok'=>false,'msg'=>'Endpoint không hợp lệ'], 404);

} catch (Throwable $e) {
  json_response(['ok'=>false,'msg'=>$e->getMessage()], 500);
}
