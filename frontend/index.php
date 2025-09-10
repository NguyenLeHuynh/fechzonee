<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>FechZone</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{ --ink:#0a0a0a; --muted:#6b7280; --hairline:rgba(0,0,0,.08); }
    html,body{ font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji"; color:var(--ink);}
    .glass{ background:rgba(255,255,255,.7); -webkit-backdrop-filter:saturate(180%) blur(16px); backdrop-filter:saturate(180%) blur(16px); border-bottom:1px solid var(--hairline);}
    .card{ border:1px solid var(--hairline); border-radius:20px; transition:transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s, border-color .35s; box-shadow:0 1px 0 rgba(0,0,0,.02); background:#fff;}
    .card:hover{ transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,.08); border-color:rgba(0,0,0,.12);}
    .btn-primary{ border-radius:9999px; padding:.65rem 1.1rem; background:#0071e3; color:#fff; font-weight:600; transition:background .25s, transform .25s;}
    .btn-primary:hover{ background:#0570cc; transform:translateY(-1px);}
    .btn-outline{ border-radius:9999px; padding:.55rem 1rem; border:1px solid var(--hairline); transition:background .2s, border-color .2s;}
    .btn-outline:hover{ background:#f6f7f8; border-color:rgba(0,0,0,.15);}
    .drawer{ position:fixed; inset:0 0 0 auto; width:420px; max-width:100vw; background:#fff; transform:translateX(100%); transition:transform .35s ease; border-left:1px solid var(--hairline); z-index:60;}
    .drawer.show{ transform:translateX(0);}
    .hairline{ border-top:1px solid var(--hairline); }
    .ratio{ aspect-ratio:16/10; background:linear-gradient(180deg,#f8fafc,#eef2f7);}
  </style>
</head>
<body class="bg-gradient-to-b from-[#f6f7fb] to-white min-h-screen">
  <!-- NAV -->
  <header class="glass sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-5 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="<?= url('/frontend/index.php') ?>" class="flex items-center gap-2">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 2a5 5 0 0 1-3 4.58A4.8 4.8 0 0 0 8 6a5 5 0 0 1 3-4 4.9 4.9 0 0 1 5 0zM20 15.5c0-3.2 2.6-4.4 2.6-4.4-.2-.6-1.7-1.7-3.5-1.7-1.6 0-2.3.8-3.5.8s-1.9-.8-3.5-.8C9.5 9.4 8 11 8 13.4c0 2.9 2.7 6.6 4.1 6.6 1.3 0 1.6-.9 3.2-.9s1.8.9 3.1.9c1.4 0 3.6-3.4 3.6-6.5z"/></svg>
          <span class="text-[18px] font-semibold tracking-tight">FechZone</span>
        </a>
      </div>
        <a class="hover:underline" href="<?= url('/frontend/index.php') ?>">Trang chủ</a>
        <?php if (current_user()): ?>
          <a class="hover:underline" href="<?= url('/backend/orders/my.php') ?>">Đơn hàng của tôi</a>
          <a class="hover:underline" href="<?= url('/backend/customers/profile.php') ?>">Hồ sơ</a>
          <?php if (is_admin()): ?>
            <a class="hover:underline" href="<?= url('/frontend/admin.php') ?>">Quản trị</a>
          <?php endif; ?>
        <?php else: ?>
          <a class="hover:underline" href="<?= url('/backend/customers/login.php') ?>">Đăng nhập</a>
        <?php endif; ?>
      </nav>

      <div class="flex items-center gap-4">
        <span class="hidden sm:block text-[13px] text-[#4b5563]">
          👤 <?= htmlspecialchars(current_user()['username'] ?? 'Khách') ?>
          <?php if (current_user() && isset(current_user()['role'])): ?>
            (<?= htmlspecialchars(current_user()['role']) ?>)
          <?php endif; ?>
        </span>
        <?php if (current_user()): ?>
          <a class="btn-outline" href="<?= url('/backend/customers/logout.php') ?>">Đăng xuất</a>
        <?php endif; ?>
        <button id="btn-cart" class="btn-outline">🛍️ Giỏ hàng</button>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="max-w-7xl mx-auto px-5 pt-10 pb-6">
    <div class="relative rounded-[28px] overflow-hidden border border-[color:var(--hairline)] bg-white">
      <div class="absolute inset-0 pointer-events-none"
           style="background:
             radial-gradient(1200px 400px at 80% -30%, #cde5ff 0, transparent 60%),
             radial-gradient(1200px 400px at 20% 130%, #eae7ff 0, transparent 60%);">
      </div>

      <div class="relative grid md:grid-cols-2 gap-8 items-center px-8 md:px-14 py-12 md:py-16">
        <div>
          <p class="text-[#6b7280] text-sm mb-2">Mới</p>
          <h1 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight">
            Thiết kế tối giản.<br/>Trải nghiệm vượt trội.
          </h1>
          <p class="text-[#4b5563] mt-4 text-[15px] max-w-xl">
            Khám phá các sản phẩm được chế tác tinh gọn, hiệu năng mạnh mẽ và liền mạch trong từng thao tác.
          </p>

          <div class="mt-6 flex flex-wrap items-center gap-3">
            <a href="#products" class="btn-primary">Mua ngay</a>
            <?php if (!current_user()): ?>
              <a href="<?= url('/backend/customers/login.php') ?>" class="btn-outline">Đăng nhập để đặt hàng</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="ratio rounded-[20px] border border-[color:var(--hairline)] overflow-hidden">
          <?php
            $local = __DIR__ . '/../public/assets/pc-rgb.jpg';
            $src   = file_exists($local)
                     ? url('/public/assets/pc-rgb.jpg')
                     : 'https://images.unsplash.com/photo-1516116216624-53e697fedbea?q=80&w=1600&auto=format&fit=crop';
          ?>
          <img
            src="<?= $src ?>"
            srcset="<?= $src ?> 1600w, <?= $src ?> 1200w, <?= $src ?> 800w"
            sizes="(min-width: 768px) 50vw, 100vw"
            alt="PC gaming RGB"
            loading="lazy"
            class="w-full h-full object-cover"
            onerror="this.onerror=null;this.src='<?= url('/public/assets/pc-rgb.jpg') ?>';">
        </div>
      </div>
    </div>
  </section>

  <!-- PRODUCTS -->
  <main id="products" class="max-w-7xl mx-auto px-5 pb-16">
    <h2 class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Sản phẩm nổi bật</h2>
    <p class="text-[15px] text-[#6b7280] mb-6">Thiết kế tinh xảo, hiệu năng tối ưu – chọn sản phẩm phù hợp với bạn.</p>

    <div id="grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <div id="blank" class="hidden bg-white border border-[color:var(--hairline)] rounded-[20px] p-8 text-center">
      <p class="text-[15px] text-[#6b7280]">Chưa có sản phẩm.</p>
    </div>
  </main>

  <!-- CART DRAWER -->
  <aside id="drawer" class="drawer" aria-label="Giỏ hàng">
    <div class="flex items-center justify-between px-5 py-4">
      <div class="text-[18px] font-semibold">Giỏ hàng</div>
      <button id="btn-close" class="btn-outline">Đóng</button>
    </div>
    <div id="cart" class="px-5 pb-4"></div>
    <div class="hairline"></div>
    <div class="p-5">
      <button id="checkout" class="btn-primary w-full">Đặt hàng</button>
      <p class="text-[12px] text-[#6b7280] mt-2">* Cần đăng nhập để đặt hàng.</p>
    </div>
  </aside>

  <!-- FOOTER -->
  <footer class="border-t border-[color:var(--hairline)]">
    <div class="max-w-7xl mx-auto px-5 py-10 text-sm text-[#6b7280]">
      © <?= date('Y') ?> FechZone. Thiết kế tối giản & tinh tế.
    </div>
  </footer>

  <!-- BASE_URL để dùng trong JS -->
  <script>const BASE = '<?= BASE_URL ?>';</script>

  <script>
    const cart = [];
    const grid  = document.getElementById('grid');
    const blank = document.getElementById('blank');
    const drawer= document.getElementById('drawer');
    document.getElementById('btn-cart').onclick  = () => drawer.classList.add('show');
    document.getElementById('btn-close').onclick = () => drawer.classList.remove('show');

    const currency = (n) => Number(n || 0).toLocaleString('vi-VN') + ' đ';

    /* ẢNH GỢI Ý THEO TÊN SẢN PHẨM (nếu DB không có image_url) */
    function imageFromName(name = '') {
      const t = String(name).toLowerCase();
      const U = (id) => `https://images.unsplash.com/${id}?q=80&w=1600&auto=format&fit=crop`;

      if (/(laptop|notebook|fechbook|macbook)/.test(t))                return U('photo-1517336714731-489689fd1ca8');
      if (/(pc|desktop|tower|case|gaming|rgb|rtx|4070|4080|4090)/.test(t)) return U('photo-1516116216624-53e697fedbea');
      if (/(monitor|màn hình|display|ultrawide|ips|144hz|240hz)/.test(t))  return U('photo-1517336714731-489689fd1ca8');
      if (/(keyboard|bàn phím|keycap)/.test(t))                           return U('photo-1518779578993-ec3579fee39f');
      if (/(mouse|chuột)/.test(t))                                        return U('photo-1587825140400-5fc7f8d757d6');
      if (/(headset|tai nghe|headphone)/.test(t))                         return U('photo-1518441902119-0f3c44f0c075');
      if (/(ssd|nvme|m2|gen4|gen5|storage|ổ cứng)/.test(t))               return U('photo-1518770660439-4636190af475');
      if (/(ram|memory|ddr4|ddr5)/.test(t))                                return U('photo-1518770660439-4636190af475');

      return U('photo-1517336714731-489689fd1ca8');
    }

    function renderCart(){
      const box = document.getElementById('cart');
      if (cart.length===0){
        box.innerHTML = '<div class="text-[14px] text-[#6b7280]">Chưa có sản phẩm</div>';
        return;
      }
      let sum=0;
      box.innerHTML = cart.map((x,i)=>{
        const line = x.qty * x.price; sum+=line;
        return `<div class="py-3 border-t border-[color:var(--hairline)] first:border-0 flex items-center justify-between gap-3">
          <div class="min-w-0">
            <div class="font-medium truncate">${x.name}</div>
            <div class="text-[13px] text-[#6b7280]">SL ${x.qty} · ${currency(x.price)}</div>
          </div>
          <div class="flex items-center gap-2">
            <div class="text-[14px] font-semibold">${currency(line)}</div>
            <button class="btn-outline" onclick="removeItem(${i})">Xoá</button>
          </div>
        </div>`;
      }).join('') + `<div class="pt-3 mt-2 text-right text-[15px] font-semibold">Tổng: ${currency(sum)}</div>`;
    }
    function removeItem(i){ cart.splice(i,1); renderCart(); }

    function productCard(p){
      const price = Number(p.price);
      const img = p.image_url || imageFromName(p.name);   // <-- chọn ảnh theo tên nếu thiếu
      const safeName = (p.name || '').replaceAll('"','&quot;');
      return `
      <div class="card p-5 flex flex-col">
        <div class="ratio rounded-[14px] overflow-hidden mb-4">
          <img src="${img}" alt="${safeName}" class="w-full h-full object-cover">
        </div>
        <div class="flex-1">
          <div class="text-[18px] font-semibold leading-tight mb-1">${p.name}</div>
          <div class="text-[14px] text-[#6b7280] mb-3">Tồn ${p.stock}</div>
          <div class="text-[15px] font-semibold mb-4">${currency(price)}</div>
        </div>
        <div class="flex items-center gap-2">
          <button class="btn-primary flex-1" onclick='addToCart(${p.id},"${safeName}",${price})'>Thêm vào giỏ</button>
          <button class="btn-outline" onclick='quickBuy(${p.id},"${safeName}",${price})'>Mua nhanh</button>
        </div>
      </div>`;
    }

    async function loadProducts(){
      try{
        const res = await fetch(`${BASE}/api/products.php?action=list`, { credentials: 'same-origin' });
        const j = await res.json();
        const rows = (j && j.data) ? j.data : [];
        if (rows.length === 0){ blank.classList.remove('hidden'); grid.innerHTML=''; return; }
        blank.classList.add('hidden');
        grid.innerHTML = rows.map(productCard).join('');
      }catch(e){
        blank.classList.remove('hidden');
        grid.innerHTML='';
      }
    }

    function addToCart(id,name,price){
      const f = cart.find(x=>x.product_id===id);
      if (f) f.qty++; else cart.push({product_id:id, name, price, qty:1});
      drawer.classList.add('show');
      renderCart();
    }
    function quickBuy(id,name,price){
      const f = cart.find(x=>x.product_id===id);
      if (f) f.qty++; else cart.push({product_id:id, name, price, qty:1});
      renderCart();
      drawer.classList.add('show');
    }

    document.getElementById('checkout').onclick = async ()=>{
      if (cart.length===0) return alert('Giỏ hàng trống');
      const fd = new FormData();
      fd.append('action','create');
      fd.append('items_json', JSON.stringify(cart.map(({product_id,qty})=>({product_id,qty}))));
      try{
        const res = await fetch(`${BASE}/api/orders.php`, { method:'POST', body:fd, credentials:'same-origin' });
        const j = await res.json();
        if (j.ok) { alert('Đặt hàng thành công!'); cart.length = 0; renderCart(); drawer.classList.remove('show'); }
        else alert(j.msg||'Lỗi');
      }catch(e){
        alert('Không thể đặt hàng. Vui lòng thử lại.');
      }
    };

    loadProducts(); renderCart();
  </script>
</body>
</html>
