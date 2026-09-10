<?php
$petica_api_route = '/professionals/clients/'.(int)($_GET['client_id']??0).'/companions';
require __DIR__ . '/../../api/index.php';
