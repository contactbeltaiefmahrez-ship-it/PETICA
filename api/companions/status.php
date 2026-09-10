<?php
$petica_api_route = '/companions/'.(int)($_GET['id']??0).'/status';
require __DIR__ . '/../../api/index.php';
