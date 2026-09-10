<?php
require_once __DIR__.'/config/config.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
foreach(['api/','id/','q/','user/','storage/','fr/espace-client/','ar/espace-client/','fr/espace-pro/','ar/espace-pro/','fr/admin/','ar/admin/','fr/crm/','ar/crm/'] as $path)echo 'Disallow: '.APP_BASE.'/'.$path."\n";
echo 'Sitemap: '.APP_URL."/sitemap.xml\n";
