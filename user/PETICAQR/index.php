<?php
require __DIR__.'/../../app/public_qr.php';
qr_page('tag',is_string($_GET['petica']??null)?$_GET['petica']:'',is_scalar($_GET['id']??null)?(int)$_GET['id']:0);
