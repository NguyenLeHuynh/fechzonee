<?php
require_once __DIR__ . '/../../config.php';
require_admin_page();

$rows = db()->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Sản phẩm</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .btn     { padding:.55rem 1rem; border-radius:12px; }
    .btn-p   { background:#4f46e5; color:#fff; }
    .btn-o   { border:1px solid rgba(0,0,0,.12); }
  </style>
</head>
<body class="min-h-screen bg-gray-50">
  <div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-3">
        <button type="button" class="btn btn-o"
                onclick="if (document.referrer) { history.back(); } else { location.href='<?= url('/frontend/admin.php') ?>'; }">
          ← Quay lại
        </button>
        <h1 class="text-2xl font-bold">Sản phẩm</h1>
      </div>
      <div class="space-x-3">
        <a class="btn btn-p" href="<?= url('/backend/products/add.php') ?>">+ Thêm</a>
        <a class="text-indigo-700 hidden md:inline" href="<?= url('/frontend/admin.php') ?>">Trang quản trị</a>
      </div>
    </div>

    <?php if (empty($rows)): ?>
      <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-600">
        Chưa có sản phẩm.
      </div>
    <?php else: ?>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($rows as $p): ?>
          <div class="bg-white rounded-2xl shadow p-4 flex flex-col">
            <img
              src="<?= htmlspecialchars($p['image_url'] ?: 'https://placehold.co/600x400') ?>"
              alt="<?= htmlspecialchars($p['name']) ?>"
              class="rounded-xl mb-3 w-full h-40 object-cover">
            <div class="font-semibold line-clamp-2"><?= htmlspecialchars($p['name']) ?></div>
            <div class="text-slate-600 my-1"><?= number_format((float)$p['price'], 2) ?> đ</div>
            <div class="text-sm text-slate-500">Tồn: <?= (int)$p['stock'] ?></div>

            <div class="mt-3 flex items-center gap-2">
              <a class="btn btn-o hover:bg-slate-50"
                 href="<?= url('/backend/products/edit.php?id=' . (int)$p['id']) ?>">Sửa</a>
              <form action="<?= url('/api/products.php') ?>" method="post"
                    onsubmit="return confirm('Xóa sản phẩm này?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn-o text-red-600 hover:bg-red-50">Xóa</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
