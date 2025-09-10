<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'];

function rows_to_json(array $rows): void {
  json_response(['ok' => true, 'data' => $rows]);
}

try {
  /* =========================
   *  ADMIN: danh sách tất cả đơn
   *  GET /api/orders.php?action=list
   * ========================= */
  if ($method === 'GET' && $action === 'list') {
    require_admin_page();

    $sql = "
      SELECT
        o.id,
        c.username,
        COALESCE(o.total_amount, SUM(oi.quantity*oi.price)) AS total_amount,
        o.status,
        o.created_at
      FROM orders o
      JOIN customers c ON c.id = o.customer_id
      LEFT JOIN order_items oi ON oi.order_id = o.id
      GROUP BY o.id, c.username, o.total_amount, o.status, o.created_at
      ORDER BY o.id DESC
    ";
    $rows = db()->query($sql)->fetchAll();
    rows_to_json($rows);
  }

  /* =========================
   *  USER: danh sách đơn của tôi
   *  GET /api/orders.php?action=my
   * ========================= */
  if ($method === 'GET' && $action === 'my') {
    require_login_page();
    $uid = (int) (current_user()['id'] ?? 0);

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
    $st->execute([$uid]);
    rows_to_json($st->fetchAll());
  }

  /* =========================
   *  USER: tạo đơn từ giỏ hàng
   *  POST /api/orders.php  (form-data: action=create, items_json=[{product_id,qty},...])
   * ========================= */
  if ($method === 'POST' && $action === 'create') {
    require_login_page();

    $uid = (int) ($_SESSION['user']['id'] ?? 0);
    if ($uid <= 0) {
      json_response(['ok'=>false,'msg'=>'Bạn chưa đăng nhập.'], 401);
    }

    // QUAN TRỌNG: kiểm tra user còn tồn tại (tránh ID mồ côi sau khi bạn reset DB)
    $chk = db()->prepare('SELECT id FROM customers WHERE id=?');
    $chk->execute([$uid]);
    if (!$chk->fetchColumn()) {
      json_response(['ok'=>false,'msg'=>'Phiên đăng nhập đã cũ. Vui lòng Đăng xuất rồi Đăng nhập lại.'], 400);
    }

    $items = json_decode($_POST['items_json'] ?? '[]', true);
    if (!is_array($items) || count($items) === 0) {
      json_response(['ok'=>false,'msg'=>'Giỏ hàng rỗng'], 400);
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
      // 1) Tạo đơn (total_amount có/không cũng được; sau sẽ cập nhật)
      $insOrder = $pdo->prepare('INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, ?)');
      $insOrder->execute([$uid, 0, 'pending']);
      $order_id = (int) $pdo->lastInsertId();

      // 2) Thêm từng item (lấy giá hiện tại từ bảng products)
      $getPrice = $pdo->prepare('SELECT price FROM products WHERE id=?');
      $insItem  = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)'
      );

      foreach ($items as $it) {
        $pid = (int)($it['product_id'] ?? 0);
        $qty = max(1, (int)($it['qty'] ?? 1));
        if ($pid <= 0) throw new Exception('Thiếu product_id');

        $getPrice->execute([$pid]);
        $row = $getPrice->fetch();
        if (!$row) throw new Exception("Sản phẩm ID {$pid} không tồn tại");

        $price = (float)$row['price'];
        $insItem->execute([$order_id, $pid, $qty, $price]);
      }

      // 3) Cập nhật tổng tiền (nếu có cột total_amount)
      try {
        $sum = $pdo->query("SELECT SUM(quantity*price) FROM order_items WHERE order_id={$order_id}")
                   ->fetchColumn();
        $pdo->prepare('UPDATE orders SET total_amount=? WHERE id=?')->execute([(float)$sum, $order_id]);
      } catch (Throwable $ignore) {
        // bảng không có cột total_amount thì không sao
      }

      $pdo->commit();
      json_response(['ok'=>true,'order_id'=>$order_id]);
    } catch (Throwable $ex) {
      $pdo->rollBack();
      // Trả lỗi rõ ràng để dễ debug trên màn hình
      json_response(['ok'=>false,'msg'=>$ex->getMessage()], 500);
    }
  }

  // Không khớp action
  json_response(['ok'=>false,'msg'=>'No action'], 400);

} catch (Throwable $e) {
  json_response(['ok'=>false,'msg'=>$e->getMessage()], 500);
}
