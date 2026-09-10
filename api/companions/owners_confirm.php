<?php
$petica_api_route = '/companions/'.(int)($_GET['id']??0).'/owners/'.(int)($_GET['invite_id']??0).'/confirm';
require __DIR__ . '/../../api/index.php';
