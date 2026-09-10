<?php
declare(strict_types=1);
$local = is_file(__DIR__.'/local.php') ? require __DIR__.'/local.php' : [];
function cfg(string $key, $fallback=null) { global $local; $env=getenv('PETICA_'.$key); $value=$env!==false ? $env : ($local[$key]??$fallback);return in_array($key,['EMAIL_ENABLED','ALLOW_DEMO'],true)?filter_var($value,FILTER_VALIDATE_BOOLEAN):$value; }
define('APP_URL',rtrim((string)cfg('APP_URL','http://localhost/petica'),'/'));
define('APP_BASE',rtrim((string)(parse_url(APP_URL,PHP_URL_PATH)?:''),'/'));
define('PUBLIC_URL',rtrim((string)cfg('PUBLIC_URL','https://petica.pet'),'/'));
define('DB_HOST',cfg('DB_HOST','127.0.0.1'));define('DB_PORT',cfg('DB_PORT','3306'));
define('DB_NAME',cfg('DB_NAME','petica'));define('DB_USER',cfg('DB_USER','root'));define('DB_PASS',cfg('DB_PASS',''));
define('DB_CHARSET','utf8mb4');define('SESSION_LIFETIME',1800);
define('UPLOAD_DIR',__DIR__.'/../storage/uploads');define('UPLOAD_URL_BASE',APP_BASE.'/storage/uploads');define('DELIVERY_FEE_TND',8.0);
date_default_timezone_set('UTC');error_reporting(E_ALL);ini_set('display_errors','0');ini_set('log_errors','1');
