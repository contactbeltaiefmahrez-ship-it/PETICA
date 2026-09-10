<?php
$petica_api_route = isset($_GET['id']) ? '/admin/products/'.(int)($_GET['id']??0).'' : '/admin/products';
require __DIR__ . '/../../api/index.php';
