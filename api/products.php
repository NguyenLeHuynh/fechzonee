<?php
require_once __DIR__ . '/../config.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
  if ($action === 'list') {
    $rows = db()->query('SELECT id, name, price, stock, image_url FROM products ORDER BY id DESC')->fetchAll();
    json_response(['ok'=>true, 'data'=>$rows]);
  }

  // (tuỳ chọn) thêm/sửa/xoá – chỉ admin
  if ($action === 'add') {
    require_admin_page();
    $name = trim($_POST['name']??''); $price=(float)($_POST['price']??0); $stock=(int)($_POST['stock']??0);
    $img = trim($_POST['image_url']??'');
    $st = db()->prepare('INSERT INTO products(name,price,stock,image_url) VALUES (?,?,?,?)');
    $st->execute([$name,$price,$stock,$img?:null]);
    json_response(['ok'=>true, 'id'=>db()->lastInsertId()]);
  }

  if ($action === 'delete') {
    require_admin_page();
    $id = (int)($_POST['id']??0);
    db()->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    json_response(['ok'=>true]);
  }

  json_response(['ok'=>false,'msg'=>'No action'], 400);
} catch (Throwable $e) {
  json_response(['ok'=>false,'msg'=>$e->getMessage()], 500);
}
