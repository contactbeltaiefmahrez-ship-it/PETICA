<?php
$petica_api_route = '/professionals/search-serial/'.rawurlencode((string)($_GET['serial']??'')).'';
require __DIR__ . '/../../api/index.php';
