<?php
require_once __DIR__ . '/../../config.php';
require_login_page();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $_POST['action'] = 'add';
  require __DIR__ . '/../../api/products.php'; // tái sử dụng endpoint
  exit;
}
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Thêm sản phẩm</title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="min-h-screen bg-gray-50 flex items-center">
<div class="max-w-xl mx-auto w-full bg-white rounded-2xl shadow p-6">
  <h1 class="text-2xl font-bold mb-4">Thêm sản phẩm</h1>
  <form method="post" class="space-y-3">
    <input class="w-full border rounded-xl px-3 py-2" name="name" placeholder="Tên sản phẩm" required>
    <div class="grid grid-cols-2 gap-3">
      <input class="border rounded-xl px-3 py-2" name="price" type="number" step="0.01" placeholder="Giá" required>
      <input class="border rounded-xl px-3 py-2" name="stock" type="number" placeholder="Tồn kho" required>
    </div>
    <input class="w-full border rounded-xl px-3 py-2" name="image_url" placeholder="Link ảnh (tuỳ chọn)">
    <button class="w-full bg-indigo-600 text-white rounded-xl py-2">Lưu</button>
  </form>
  <div class="mt-4"><a class="text-indigo-700" href="/backend/products/list.php">← Danh sách</a></div>
</div>
</body></html>
