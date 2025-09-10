<?php
require_once __DIR__ . '/../../config.php';
unset($_SESSION['user']);
header('Location: ' . url('/backend/customers/login.php'));  // dùng BASE_URL
exit;
