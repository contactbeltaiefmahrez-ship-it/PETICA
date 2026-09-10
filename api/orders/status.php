<?php
$petica_api_route = '/orders/'.(int)($_GET['id']??0).'/status';
require __DIR__ . '/../../api/index.php';
