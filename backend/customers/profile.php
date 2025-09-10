<?php
require_once __DIR__ . '/../../config.php';
require_login_page();

$user = current_user();

// Lấy thông tin chi tiết (nếu bạn muốn cập nhật form sau này)
$st = db()->prepare('SELECT username, role, full_name, email, phone, address, created_at FROM customers WHERE id=?');
$st->execute([$user['id']]);
$u = $st->fetch() ?: [
  'username'=>$user['username'],
  'role'=>$user['role'] ?? 'user',
  'full_name'=>null, 'email'=>null, 'phone'=>null, 'address'=>null,
  'created_at'=>date('Y-m-d H:i:s')
];
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Hồ sơ</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .btn-outline{border:1px solid rgba(0,0,0,.12);border-radius:9999px;padding:.45rem .9rem}
  .hairline{border-top:1px solid rgba(0,0,0,.08)}
</style>
</head>
<body class="min-h-screen bg-slate-50">

<!-- HEADER gộp tiêu đề + tên user, có nút quay lại -->
<header class="sticky top-0 bg-white/80 backdrop-blur shadow-sm">
  <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button onclick="history.back()" class="btn-outline" aria-label="Quay lại">← Quay lại</button>
      <a href="<?= url('/frontend/index.php') ?>" class="font-extrabold text-xl text-indigo-700">FechZone</a>
    </div>

    <!-- CỤM gộp: Hồ sơ + icon + tên -->
    <div class="flex items-center gap-2 text-sm text-slate-700">
      <span class="hidden sm:inline">Hồ sơ</span>
      <span class="opacity-40">·</span>
      <span class="inline-flex items-center gap-1">
        <span>👤</span>
        <strong><?= htmlspecialchars($u['username']) ?></strong>
      </span>
      <?php if (!empty($u['role'])): ?>
        <span class="opacity-40">·</span>
        <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100"><?= htmlspecialchars($u['role']) ?></span>
      <?php endif; ?>
    </div>

    <nav class="text-sm">
      <?php if (is_admin()): ?>
        <a class="text-slate-600 hover:text-indigo-700" href="<?= url('/frontend/admin.php') ?>">Quản trị</a>
        <span class="opacity-40 px-1">·</span>
      <?php endif; ?>
      <a class="text-red-600 hover:text-red-700" href="<?= url('/backend/customers/logout.php') ?>">Đăng xuất</a>
    </nav>
  </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-6">
  <div class="bg-white rounded-2xl shadow">
    <div class="p-5 border-b">
      <h1 class="text-xl font-semibold">Thông tin cá nhân</h1>
      <p class="text-sm text-slate-500 mt-1">Bạn có thể cập nhật tên và thông tin liên hệ tại đây.</p>
    </div>

    <!-- Hiển thị thông tin (form có thể thêm sau) -->
    <div class="p-5 grid md:grid-cols-2 gap-4">
      <div>
        <div class="text-xs text-slate-500 mb-1">Họ và tên</div>
        <div class="font-medium"><?= htmlspecialchars($u['full_name'] ?: '—') ?></div>
      </div>
      <div>
        <div class="text-xs text-slate-500 mb-1">Email</div>
        <div class="font-medium"><?= htmlspecialchars($u['email'] ?: '—') ?></div>
      </div>
      <div>
        <div class="text-xs text-slate-500 mb-1">Số điện thoại</div>
        <div class="font-medium"><?= htmlspecialchars($u['phone'] ?: '—') ?></div>
      </div>
      <div>
        <div class="text-xs text-slate-500 mb-1">Địa chỉ</div>
        <div class="font-medium"><?= htmlspecialchars($u['address'] ?: '—') ?></div>
      </div>
    </div>

    <div class="hairline"></div>

    <div class="p-5 flex items-center justify-between">
      <div class="text-sm text-slate-500">Tham gia: <?= htmlspecialchars($u['created_at']) ?></div>
      <div class="flex gap-2">
        <a href="<?= url('/backend/orders/my.php') ?>" class="btn-outline">Đơn hàng của tôi</a>
        <!-- Nút chỉnh sửa có thể trỏ sang trang edit riêng -->
        <a href="<?= url('/backend/customers/edit_profile.php') ?>" class="btn-outline">Chỉnh sửa</a>
      </div>
    </div>
  </div>
</main>
</body>
</html>
