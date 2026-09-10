<?php
require_once __DIR__.'/app/core.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$pages=['index.html','tarifs.html','comment-ca-marche.html','a-propos.html','faq.html','contact.html','mouvement.html','world.html','produits/carte-identite.html','produits/qr-tag.html','animal-perdu.html'];
foreach(['fr','ar'] as $l)foreach($pages as $p)echo '<url><loc>'.esc(APP_URL.'/'.$l.'/'.$p).'</loc></url>';
echo '</urlset>';
