<?php
$petica_api_route = '/crm/customers/'.(int)($_GET['id']??0).'/notes';
require __DIR__ . '/../../api/index.php';
