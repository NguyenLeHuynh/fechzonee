<?php
require_once __DIR__ . '/../../config.php';
require_login_page();

$sql = "
  SELECT 
    o.id,
    COALESCE(o.total_amount, SUM(oi.quantity*oi.price)) AS total_amount,
    o.status,
    o.created_at
  FROM orders o
  LEFT JOIN order_items oi ON oi.order_id = o.id
  WHERE o.customer_id = ?
  GROUP BY o.id, o.total_amount, o.status, o.created_at
  ORDER BY o.id DESC
";
$st = db()->prepare($sql);

$rows = $st->fetchAll();
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Đơn hàng của tôi</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>.btn-outline{border:1px solid rgba(0,0,0,.12);border-radius:9999px;padding:.45rem .9rem}</style>
</head><body class="min-h-screen bg-slate-50">
<header class="sticky top-0 bg-white/80 backdrop-blur shadow-sm">
  <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button onclick="history.back()" class="btn-outline">← Quay lại</button>
      <a href="<?= url('/frontend/index.php') ?>" class="font-extrabold text-xl text-indigo-700">FechZone</a>
    </div>
    <nav class="text-sm">
      <a class="text-slate-600 hover:text-indigo-700" href="<?= url('/backend/customers/profile.php') ?>">Hồ sơ</a>
      <?php if (is_admin()): ?> · <a class="text-slate-600 hover:text-indigo-700" href="<?= url('/frontend/admin.php') ?>">Quản trị</a><?php endif; ?>
      · <a class="text-red-600 hover:text-red-700" href="<?= url('/backend/customers/logout.php') ?>">Đăng xuất</a>
    </nav>
  </div>
</header>

<main class="max-w-4xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-4">Đơn hàng của tôi</h1>
  <div class="bg-white rounded-2xl shadow divide-y">
    <?php if (!$rows): ?>
      <div class="p-4 text-slate-600">Chưa có đơn hàng.</div>
    <?php else: foreach ($rows as $o): ?>
      <div class="p-4 flex items-center justify-between">
        <div>#<?= $o['id'] ?> • <?= htmlspecialchars($o['created_at']) ?></div>
        <div class="text-slate-600">
          <?= number_format((float)$o['total_amount'], 0, ',', '.') ?> đ
        </div>
        <div class="px-2 py-1 text-xs rounded-lg bg-slate-100"><?= htmlspecialchars($o['status']) ?></div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</main>
</body></html>
