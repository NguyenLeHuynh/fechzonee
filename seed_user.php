<?php
// FECHZONE/seed_user.php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

// username và password bạn muốn đặt
$username = 'admin1';            // đổi nếu muốn
$newPass  = '123456';

$hash = password_hash($newPass, PASSWORD_BCRYPT);
$pdo  = db();

// Nếu có user thì UPDATE, chưa có thì INSERT
$st = $pdo->prepare('SELECT id FROM customers WHERE username = ?');
$st->execute([$username]);
if ($st->fetch()) {
  $u = $pdo->prepare('UPDATE customers SET password_hash=? WHERE username=?');
  $u->execute([$hash, $username]);
  echo "Đã đặt lại mật khẩu cho {$username} = {$newPass}";
} else {
  $i = $pdo->prepare('INSERT INTO customers(username,password_hash) VALUES(?,?)');
  $i->execute([$username, $hash]);
  echo "Đã tạo user {$username} / {$newPass}";
}
