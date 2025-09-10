<?php
require_once __DIR__ . '/../../config.php';
require_admin_page();

/* Lấy danh sách đơn + tổng tiền + số dòng hàng */
$sql = "
  SELECT 
    o.id,
    c.username,
    COALESCE(o.total_amount, SUM(oi.quantity*oi.price)) AS total_amount,
    o.status,
    o.created_at,
    COUNT(oi.id) AS items_count
  FROM orders o
  JOIN customers c ON c.id = o.customer_id
  LEFT JOIN order_items oi ON oi.order_id = o.id
  GROUP BY o.id, c.username, o.total_amount, o.status, o.created_at
  ORDER BY o.id DESC
";
$rows = db()->query($sql)->fetchAll();

/* Lấy chi tiết item cho các đơn (để hiển thị trong <details>) */
$itemsMap = [];
if ($rows) {
  $ids = array_column($rows, 'id');
  $in  = implode(',', array_fill(0, count($ids), '?'));
  $st  = db()->prepare("
      SELECT oi.order_id, p.name, oi.quantity, oi.price
      FROM order_items oi 
      JOIN products p ON p.id = oi.product_id
      WHERE oi.order_id IN ($in)
      ORDER BY oi.order_id, oi.id
  ");
  $st->execute($ids);
  foreach ($st->fetchAll() as $it) {
    $itemsMap[$it['order_id']][] = $it;
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Đơn hàng</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .btn{padding:.5rem .9rem;border-radius:12px}
    .btn-o{border:1px solid rgba(0,0,0,.12)}
  </style>
</head>
<body class="min-h-screen bg-gray-50">
<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <button class="btn btn-o"
              onclick="if(document.referrer){history.back()}else{location.href='<?= url('/frontend/admin.php') ?>'}">
        ← Quay lại
      </button>
      <h1 class="text-2xl font-bold">Đơn hàng</h1>
    </div>
    <a class="text-indigo-700" href="<?= url('/frontend/admin.php') ?>">Trang quản trị</a>
  </div>

  <div class="bg-white rounded-2xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 text-left">ID</th>
        <th class="px-4 py-2 text-left">Khách</th>
        <th class="px-4 py-2 text-left">Số dòng</th>
        <th class="px-4 py-2 text-left">Tổng tiền</th>
        <th class="px-4 py-2 text-left">Trạng thái</th>
        <th class="px-4 py-2 text-left">Chi tiết</th>
        <th class="px-4 py-2">Cập nhật</th>
      </tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Chưa có đơn hàng.</td></tr>
      <?php endif; ?>

      <?php foreach($rows as $o): ?>
        <tr class="border-t align-top">
          <td class="px-4 py-2 font-medium">#<?= (int)$o['id'] ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($o['username']) ?></td>
          <td class="px-4 py-2"><?= (int)$o['items_count'] ?></td>
          <td class="px-4 py-2"><?= number_format((float)$o['total_amount'], 2) ?> đ</td>
          <td class="px-4 py-2">
            <span class="px-2 py-0.5 rounded-lg bg-slate-100"><?= htmlspecialchars($o['status']) ?></span>
          </td>

          <!-- Chi tiết items: dùng <details> gọn gàng -->
          <td class="px-4 py-2">
            <details>
              <summary class="cursor-pointer text-indigo-700">Xem</summary>
              <div class="mt-2 text-xs text-slate-700 space-y-1">
                <?php foreach (($itemsMap[$o['id']] ?? []) as $it): ?>
                  <div>
                    <span class="font-medium"><?= htmlspecialchars($it['name']) ?></span>
                    · SL <?= (int)$it['quantity'] ?>
                    · <?= number_format((float)$it['price'], 2) ?> đ
                  </div>
                <?php endforeach; ?>
                <?php if (empty($itemsMap[$o['id']])): ?>
                  <div class="text-slate-500">Không có dòng hàng.</div>
                <?php endif; ?>
              </div>
            </details>
          </td>

          <!-- Form cập nhật trạng thái (gọi API) -->
          <td class="px-4 py-2">
            <form action="<?= url('/api/orders.php') ?>" method="post" class="flex items-center gap-2">
              <input type="hidden" name="action" value="set_status">
              <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
              <select name="status" class="border rounded-xl px-2 py-1">
                <?php foreach(['pending','paid','shipped','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $s===$o['status']?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-o hover:bg-gray-50">Lưu</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
