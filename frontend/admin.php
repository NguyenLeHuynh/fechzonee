<?php require_once __DIR__ . '/../config.php'; require_admin_page(); ?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Admin | FechZone</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .btn-outline{border:1px solid rgba(0,0,0,.12);border-radius:9999px;padding:.45rem .9rem}
</style>
</head>
<body class="min-h-screen bg-slate-50">
<header class="sticky top-0 bg-white/80 backdrop-blur shadow-sm">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button onclick="history.back()" class="btn-outline">← Quay lại</button>
      <a href="<?= url('/frontend/index.php') ?>" class="font-extrabold text-xl text-indigo-700">FechZone</a>
      <a class="text-sm text-slate-600 hover:text-indigo-700" href="<?= url('/backend/products/list.php') ?>">Sản phẩm</a>
      <a class="text-sm text-slate-600 hover:text-indigo-700" href="<?= url('/backend/orders/list.php') ?>">Đơn hàng</a>
      <a class="text-sm text-slate-600 hover:text-indigo-700" href="<?= url('/backend/customers/list.php') ?>">Khách hàng</a>
    </div>
    <div class="flex items-center gap-3 text-sm">
      <span>
        👤 <?= htmlspecialchars(current_user()['username'] ?? '') ?>
        <?php if (current_user() && isset(current_user()['role'])): ?>
          (<?= htmlspecialchars(current_user()['role']) ?>)
        <?php endif; ?>
      </span>
      <a class="text-red-600 hover:text-red-700" href="<?= url('/backend/customers/logout.php') ?>">Đăng xuất</a>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-4">Tổng quan</h1>

  <div id="cards" class="grid md:grid-cols-3 gap-4 mb-6">
    <!-- skeleton -->
    <div class="bg-white rounded-2xl shadow p-4 h-[96px] animate-pulse"></div>
    <div class="bg-white rounded-2xl shadow p-4 h-[96px] animate-pulse"></div>
    <div class="bg-white rounded-2xl shadow p-4 h-[96px] animate-pulse"></div>
  </div>

  <div class="bg-white rounded-2xl shadow p-4">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Đơn hàng gần đây</h2>
      <a class="text-indigo-700" href="<?= url('/backend/orders/list.php') ?>">Xem tất cả →</a>
    </div>
    <div id="orders">
      <div class="h-24 animate-pulse bg-slate-100 rounded-xl"></div>
    </div>
  </div>
</main>

<script>
  const BASE = '<?= BASE_URL ?>';

  async function safeJson(url) {
    try {
      const r = await fetch(url, { credentials: 'same-origin' });
      const j = await r.json();
      return j && j.ok ? j : { ok:false, data: [] };
    } catch(e) {
      return { ok:false, data: [] };
    }
  }

  async function loadDash(){
    const [products, orders, customers] = await Promise.all([
      safeJson(`${BASE}/api/products.php?action=list`),
      safeJson(`${BASE}/api/orders.php?action=list`),     // yêu cầu require_admin_page() trong API
      safeJson(`${BASE}/api/customers.php?action=list`)   // nếu chưa có API này sẽ trả về rỗng
    ]);

    const pCount = (products.data || []).length;
    const oCount = (orders.data || []).length;
    const cCount = (customers.data || []).length;

    document.getElementById('cards').innerHTML = `
      <div class="bg-white rounded-2xl shadow p-4">
        <div class="text-slate-500 text-sm">Sản phẩm</div>
        <div class="text-3xl font-bold">${pCount}</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-4">
        <div class="text-slate-500 text-sm">Đơn hàng</div>
        <div class="text-3xl font-bold">${oCount}</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-4">
        <div class="text-slate-500 text-sm">Khách hàng</div>
        <div class="text-3xl font-bold">${cCount}</div>
      </div>
    `;

    const rows = orders.data || [];
    document.getElementById('orders').innerHTML =
      rows.slice(0,5).map(o => `
        <div class="flex items-center justify-between border-t py-2 first:border-0">
          <div>#${o.id} · ${o.username ?? ''}</div>
          <div class="text-slate-600">${Number(o.total_amount ?? 0).toFixed(2)} đ</div>
          <div class="px-2 py-1 text-xs rounded-lg bg-slate-100">${o.status ?? ''}</div>
        </div>
      `).join('') || '<div class="text-slate-500">Chưa có đơn</div>';
  }

  loadDash();
</script>
</body></html>
