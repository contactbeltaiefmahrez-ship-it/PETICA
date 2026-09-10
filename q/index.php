<?php
require __DIR__.'/../app/public_qr.php';
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);$value=$_GET['token']??'';if(preg_match('~/q/([A-Za-z0-9-]+)$~',$path,$m))$value=$m[1];qr_page('tag',is_string($value)?$value:'');
