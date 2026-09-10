<?php
$petica_api_route = '/admin/users/'.(int)($_GET['id']??0).'/status';
require __DIR__ . '/../../api/index.php';
