<?php
require_once __DIR__ . '/../../config.php';

function redirect_after_login(string $role): void {
  if ($role === 'admin') {
    header('Location: ' . url('/frontend/admin.php'));
  } else {
    header('Location: ' . url('/frontend/index.php'));
  }
  exit;
}

// Nếu đã đăng nhập, tự động điều hướng đúng nơi
if (current_user()) {
  redirect_after_login(current_user()['role'] ?? 'user');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');

  $st = db()->prepare('SELECT id, username, password_hash, role FROM customers WHERE username = ?');
  $st->execute([$u]);
  $row = $st->fetch();

  if ($row && verify_password($p, $row['password_hash'])) {
    $_SESSION['user'] = [
      'id'       => $row['id'],
      'username' => $row['username'],
      'role'     => $row['role'], // 'admin' | 'user'
    ];
    redirect_after_login($row['role']);
  }
  $err = 'Sai tài khoản hoặc mật khẩu';
}

// nhận cảnh báo nhẹ khi đăng ký chọn admin nhưng sai mã cửa hàng
$flash_warn = $_SESSION['flash_warn'] ?? '';
unset($_SESSION['flash_warn']);
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Đăng nhập</title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="min-h-screen bg-gradient-to-br from-indigo-100 to-white flex items-center justify-center">
<div class="w-full max-w-md bg-white/90 backdrop-blur shadow-xl rounded-2xl p-6">
  <h1 class="text-2xl font-bold text-slate-800 mb-4">Đăng nhập</h1>

  <?php if(!empty($flash_warn)): ?>
    <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 mb-3">
      <?= htmlspecialchars($flash_warn) ?>
    </p>
  <?php endif; ?>

  <?php if(!empty($err)): ?>
    <p class="text-red-600 mb-3"><?= htmlspecialchars($err) ?></p>
  <?php endif; ?>

  <form method="post" class="space-y-3" action="<?= url('/backend/customers/login.php') ?>">
    <input name="username" class="w-full border rounded-xl px-3 py-2" placeholder="Username" required>
    <input name="password" type="password" class="w-full border rounded-xl px-3 py-2" placeholder="Password" required>
    <button class="w-full bg-indigo-600 text-white rounded-xl py-2 font-semibold hover:bg-indigo-700">Đăng nhập</button>
  </form>

  <div class="text-sm mt-4 text-slate-600">
    Chưa có tài khoản? <a class="text-indigo-700" href="<?= url('/backend/customers/register.php') ?>">Đăng ký</a>
  </div>
</div>
</body></html>
