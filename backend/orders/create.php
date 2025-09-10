<?php
require_once __DIR__ . '/../../config.php';
require_login_page();
$products = db()->query('SELECT id,name,price,stock FROM products ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Tạo đơn hàng</title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="min-h-screen bg-gray-50">
<div class="max-w-3xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Tạo đơn (demo)</h1>
    <a class="text-indigo-700" href="/backend/orders/list.php">← Danh sách</a>
  </div>
  <div class="bg-white rounded-2xl shadow p-4">
    <form id="f" class="space-y-3">
      <div class="grid md:grid-cols-3 gap-3 items-end">
        <div>
          <label class="text-sm text-slate-600">Sản phẩm</label>
          <select id="pid" class="w-full border rounded-xl px-3 py-2">
            <?php foreach($products as $p): ?>
              <option value="<?=$p['id']?>" data-name="<?=htmlspecialchars($p['name'])?>" data-price="<?=$p['price']?>"><?=$p['name']?> (<?=number_format((float)$p['price'],2)?> đ | tồn <?=$p['stock']?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="text-sm text-slate-600">Số lượng</label>
          <input id="qty" type="number" value="1" min="1" class="w-full border rounded-xl px-3 py-2">
        </div>
        <button type="button" id="add" class="bg-indigo-600 text-white rounded-xl py-2">Thêm vào giỏ</button>
      </div>
      <div id="cart" class="border rounded-2xl p-3"></div>
      <button class="w-full bg-emerald-600 text-white rounded-xl py-2">Tạo đơn</button>
    </form>
  </div>
</div>
<script>
const cart = [];
function render() {
  const wrap = document.getElementById('cart');
  if (cart.length===0) { wrap.innerHTML='<div class="text-slate-500">Giỏ hàng trống</div>'; return; }
  let html = '<table class="w-full text-sm"><tr class="text-slate-600"><th class="text-left p-2">SP</th><th>SL</th><th>Giá</th><th>Tổng</th></tr>';
  let sum=0;
  cart.forEach(x=>{
    const line = x.qty * x.price;
    sum+=line;
    html+=`<tr class="border-t"><td class="p-2">${x.name}</td><td class="text-center">${x.qty}</td><td class="text-center">${x.price.toFixed(2)}</td><td class="text-right p-2">${line.toFixed(2)}</td></tr>`;
  });
  html+=`<tr class="border-t font-semibold"><td colspan="3" class="p-2 text-right">Tổng</td><td class="text-right p-2">${sum.toFixed(2)}</td></tr></table>`;
  wrap.innerHTML = html;
}
document.getElementById('add').onclick = () => {
  const sel = document.getElementById('pid');
  const opt = sel.options[sel.selectedIndex];
  const id = +opt.value; const name = opt.dataset.name; const price = +opt.dataset.price;
  const qty = Math.max(1, +document.getElementById('qty').value);
  const found = cart.find(x=>x.product_id===id);
  if (found) found.qty+=qty; else cart.push({product_id:id, name, price, qty});
  render();
};
document.getElementById('f').onsubmit = async (e)=>{
  e.preventDefault();
  if (cart.length===0) return alert('Giỏ hàng trống');
  const fd = new FormData();
  fd.append('action','create');
  fd.append('items_json', JSON.stringify(cart.map(({product_id,qty})=>({product_id,qty}))));
  const res = await fetch('/api/orders.php', {method:'POST', body:fd});
  const j = await res.json();
  if (j.ok) { alert('Tạo đơn thành công!'); location.href='/backend/orders/list.php'; }
  else alert(j.msg||'Lỗi');
};
render();
</script>
</body></html>
