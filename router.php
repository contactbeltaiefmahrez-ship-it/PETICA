<?php
// Development router for php -S. Apache uses .htaccess in XAMPP.
require_once __DIR__.'/config/config.php';
$p=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
if(APP_BASE!==''&&str_starts_with($p,APP_BASE.'/'))$p=substr($p,strlen(APP_BASE));
if(preg_match('~(?:^|/)(?:\.|config|app|includes|database|bin|docs|tests|reference|data)(?:/|$)|storage/(?:private|logs)|\x00~',$p)||str_contains($p,'..')){http_response_code(403);exit('Forbidden');}
if(str_starts_with($p,'/api/')&&str_ends_with($p,'.php')&&is_file(__DIR__.$p)){require __DIR__.$p;return;}
if(str_starts_with($p,'/api/')){require __DIR__.'/api/index.php';return;}
if(preg_match('~^/id/([A-Za-z0-9-]+)$~',$p,$m)){$_GET['value']=$m[1];require __DIR__.'/id/index.php';return;}
if(preg_match('~^/q/([A-Za-z0-9-]+)$~',$p,$m)){$_GET['token']=$m[1];require __DIR__.'/q/index.php';return;}
if($p==='/user/PETICAQR/index.php'){require __DIR__.'/user/PETICAQR/index.php';return;}
if($p==='/sitemap.xml'){require __DIR__.'/sitemap.php';return;}if($p==='/robots.txt'){require __DIR__.'/robots.php';return;}
if(preg_match('~^/(assets|css|js|fonts|storage/uploads)/[^\x00]+$~',$p)&&is_file(__DIR__.$p)&&!preg_match('/\.(php|phtml|phar|html|svg)$/i',$p) || (str_starts_with($p,'/assets/icons/')&&str_ends_with($p,'.svg')&&is_file(__DIR__.$p))){$ext=strtolower(pathinfo($p,PATHINFO_EXTENSION));$types=['css'=>'text/css','js'=>'text/javascript','svg'=>'image/svg+xml','webp'=>'image/webp','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','woff'=>'font/woff','woff2'=>'font/woff2','ico'=>'image/x-icon'];header('Content-Type: '.($types[$ext]??'application/octet-stream'));readfile(__DIR__.$p);return;}
require __DIR__.'/page.php';
