<?php
$petica_api_route = '/admin/promo/'.(int)($_GET['id']??0).'/active';
require __DIR__ . '/../../api/index.php';
