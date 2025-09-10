<?php
require_once __DIR__ . '/../../config.php';
require_login_page();

$me = current_user();

/* ====== POST: cập nhật ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Lấy dữ liệu & vệ sinh đơn giản
  $full_name = trim($_POST['full_name'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $phone     = trim($_POST['phone'] ?? '');
  $address   = trim($_POST['address'] ?? '');

  // (tuỳ chọn) kiểm tra email hợp lệ
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = 'Email không hợp lệ';
  }

  if (!isset($err)) {
    $sql = "UPDATE customers
            SET full_name=:full_name, email=:email, phone=:phone, address=:address
            WHERE id=:id";
    $st = db()->prepare($sql);
    $st->execute([
      ':full_name' => $full_name ?: null,
      ':email'     => $email ?: null,
      ':phone'     => $phone ?: null,
      ':address'   => $address ?: null,
      ':id'        => $me['id'],
    ]);

    // Cập nhật lại session để header hiển thị đúng tên (nếu bạn muốn)
    $_SESSION['user']['full_name'] = $full_name;

    // Quay về trang hồ sơ với flag updated
    header('Location: ' . url('/backend/customers/profile.php?updated=1'));
    exit;
  }
}

/* ====== GET: lấy dữ liệu hiện tại ====== */
$st = db()->prepare('SELECT username, role, full_name, email, phone, address FROM customers WHERE id=?');
$st->execute([$me['id']]);
$u = $st->fetch() ?: ['username'=>$me['username'],'role'=>$me['role']??'user','full_name'=>'','email'=>'','phone'=>'','address'=>''];
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Chỉnh sửa hồ sơ</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .btn-outline{border:1px solid rgba(0,0,0,.12);border-radius:9999px;padding:.45rem .9rem}
  .btn-primary{border-radius:9999px;padding:.55rem 1rem;background:#0071e3;color:#fff;font-weight:600}
  .input{border:1px solid rgba(0,0,0,.12);border-radius:12px;padding:.6rem .8rem;width:100%}
</style>
</head>
<body class="min-h-screen bg-slate-50">
<header class="sticky top-0 bg-white/80 backdrop-blur shadow-sm">
  <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <button onclick="history.back()" class="btn-outline" aria-label="Quay lại">← Quay lại</button>
      <a href="<?= url('/frontend/index.php') ?>" class="font-extrabold text-xl text-indigo-700">FechZone</a>
    </div>
    <div class="text-sm text-slate-700 flex items-center gap-2">
      <span>👤</span><strong><?= htmlspecialchars($u['username']) ?></strong>
      <span class="opacity-40">·</span>
      <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100"><?= htmlspecialchars($u['role']) ?></span>
    </div>
    <nav class="text-sm">
      <a class="text-slate-600 hover:text-indigo-700" href="<?= url('/backend/customers/profile.php') ?>">Hồ sơ</a>
      <span class="opacity-40 px-1">·</span>
      <a class="text-red-600 hover:text-red-700" href="<?= url('/backend/customers/logout.php') ?>">Đăng xuất</a>
    </nav>
  </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-6">
  <div class="bg-white rounded-2xl shadow">
    <div class="p-5 border-b">
      <h1 class="text-xl font-semibold">Chỉnh sửa hồ sơ</h1>
      <p class="text-sm text-slate-500 mt-1">Cập nhật tên và thông tin liên hệ của bạn.</p>
      <?php if (!empty($err)): ?>
        <div class="mt-3 text-sm text-red-600"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>
    </div>

    <form method="post" class="p-5 grid gap-4">
      <div>
        <label class="text-sm text-slate-600">Họ và tên</label>
        <input class="input mt-1" name="full_name" value="<?= htmlspecialchars($u['full_name'] ?? '') ?>">
      </div>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-slate-600">Email</label>
          <input class="input mt-1" name="email" type="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>">
        </div>
        <div>
          <label class="text-sm text-slate-600">Số điện thoại</label>
          <input class="input mt-1" name="phone" value="<?= htmlspecialchars($u['phone'] ?? '') ?>">
        </div>
      </div>
      <div>
        <label class="text-sm text-slate-600">Địa chỉ</label>
        <input class="input mt-1" name="address" value="<?= htmlspecialchars($u['address'] ?? '') ?>">
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="<?= url('/backend/customers/profile.php') ?>" class="btn-outline">Huỷ</a>
        <button class="btn-primary" type="submit">Lưu thay đổi</button>
      </div>
    </form>
  </div>
</main>
</body>
</html>
