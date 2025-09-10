<?php
require_once __DIR__ . '/../../config.php';
require_login_page();
$rows = db()->query('SELECT id, username, created_at FROM customers ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Danh sách khách hàng</title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="min-h-screen bg-gray-50">
<div class="max-w-5xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Khách hàng</h1>
    <a href="/frontend/admin.php" class="text-indigo-700">← Quản trị</a>
  </div>
  <div class="bg-white rounded-2xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100">
        <tr><th class="px-4 py-2 text-left">ID</th><th class="px-4 py-2 text-left">Username</th><th class="px-4 py-2 text-left">Created</th></tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
        <tr class="border-t">
          <td class="px-4 py-2"><?= $r['id'] ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($r['username']) ?></td>
          <td class="px-4 py-2"><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>
