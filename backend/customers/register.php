<?php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');
  $role_choice = $_POST['role'] ?? 'user';                // 'user' | 'admin'
  $store_pass  = trim($_POST['store_pass'] ?? '');        // mật khẩu cửa hàng khi chọn admin
  $ADMIN_SECRET = '9999';                                  // <<< yêu cầu: mã cửa hàng là 9999

  if ($u && $p) {
    // check trùng username
    $st = db()->prepare('SELECT id FROM customers WHERE username=?');
    $st->execute([$u]);
    if ($st->fetch()) {
      $err = 'Username đã tồn tại';
    } else {
      // Xác định role hợp lệ
      $final_role = 'user';
      if ($role_choice === 'admin' && $store_pass === $ADMIN_SECRET) {
        $final_role = 'admin';
      }
      // Nếu người dùng cố tình chọn admin nhưng mã sai -> tạo user và báo nhẹ
      $warn = ($role_choice === 'admin' && $final_role !== 'admin')
        ? 'Mã cửa hàng không đúng, tài khoản được tạo với vai trò KHÁCH (user).'
        : '';

      // tạo tài khoản
      $hash = password_hash($p, PASSWORD_BCRYPT);
      $ins = db()->prepare('INSERT INTO customers(username, password_hash, role) VALUES(?,?,?)');
      $ins->execute([$u, $hash, $final_role]);

      // chuyển về trang đăng nhập + đính kèm cảnh báo (nếu có)
      if ($warn) {
        $_SESSION['flash_warn'] = $warn;
      }
      header('Location: ' . url('/backend/customers/login.php'));
      exit;
    }
  } else {
    $err = 'Vui lòng nhập đầy đủ Username và Password';
  }
}
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Đăng ký</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-100 to-white flex items-center justify-center">
  <div class="w-full max-w-md bg-white/90 backdrop-blur shadow-xl rounded-2xl p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-4">Đăng ký</h1>

    <?php if(!empty($err)): ?>
      <p class="text-red-600 mb-3"><?= htmlspecialchars($err) ?></p>
    <?php endif; ?>

    <form method="post" class="space-y-4" action="<?= url('/backend/customers/register.php') ?>">
      <div>
        <label class="text-sm text-slate-600">Username</label>
        <input name="username" class="w-full border rounded-xl px-3 py-2" placeholder="Nhập username" required>
      </div>

      <div>
        <label class="text-sm text-slate-600">Password</label>
        <input name="password" type="password" class="w-full border rounded-xl px-3 py-2" placeholder="Mật khẩu" required>
      </div>

      <div>
        <label class="text-sm text-slate-600">Vai trò</label>
        <div class="mt-2 grid grid-cols-2 gap-2">
          <label class="flex items-center gap-2 border rounded-xl px-3 py-2">
            <input type="radio" name="role" value="user" checked>
            <span>Khách (user)</span>
          </label>
          <label class="flex items-center gap-2 border rounded-xl px-3 py-2">
            <input type="radio" name="role" value="admin" id="role-admin">
            <span>Admin</span>
          </label>
        </div>
      </div>

      <div id="store-pass-wrap" class="hidden">
        <label class="text-sm text-slate-600">Mã cửa hàng (chỉ khi chọn Admin)</label>
        <input name="store_pass" class="w-full border rounded-xl px-3 py-2" placeholder="Nhập mã cửa hàng">
      </div>

      <button class="w-full bg-emerald-600 text-white rounded-xl py-2 font-semibold hover:bg-emerald-700">Tạo tài khoản</button>
    </form>

    <div class="text-sm mt-4 text-slate-600">
      Đã có tài khoản? <a class="text-emerald-700" href="<?= url('/backend/customers/login.php') ?>">Đăng nhập</a>
    </div>
  </div>

  <script>
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const wrap = document.getElementById('store-pass-wrap');

    function toggleStorePass(){
      const val = document.querySelector('input[name="role"]:checked')?.value || 'user';
      if (val === 'admin') wrap.classList.remove('hidden');
      else wrap.classList.add('hidden');
    }
    roleRadios.forEach(r => r.addEventListener('change', toggleStorePass));
    toggleStorePass();
  </script>
</body>
</html>
