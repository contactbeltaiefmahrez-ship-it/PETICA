<?php
$petica_api_route = '/professionals/clients/'.(int)($_GET['id']??0).'';
require __DIR__ . '/../../api/index.php';
