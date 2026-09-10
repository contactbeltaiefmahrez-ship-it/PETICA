<?php
$petica_api_route = '/crm/customers/'.(int)($_GET['id']??0).'/followups';
require __DIR__ . '/../../api/index.php';
