<?php
$petica_api_route = isset($_GET['id']) ? '/professionals/'.(int)($_GET['id']??0).'/verification' : '/professionals';
require __DIR__ . '/../../api/index.php';
