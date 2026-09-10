<?php
$petica_api_route = '/professionals/companions/'.(int)($_GET['companion_id']??0).'/session-notes';
require __DIR__ . '/../../api/index.php';
