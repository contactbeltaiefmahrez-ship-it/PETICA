<?php
$petica_api_route = '/companions/'.(int)($_GET['id']??0).'/vet';
require __DIR__ . '/../../api/index.php';
