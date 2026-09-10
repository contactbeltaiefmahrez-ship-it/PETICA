<?php
require __DIR__.'/../app/public_qr.php';
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);$value=$_GET['value']??'';if(preg_match('~/id/([A-Za-z0-9-]+)$~',$path,$m))$value=$m[1];qr_page('identity',is_string($value)?$value:'');
