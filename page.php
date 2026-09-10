<?php
require_once __DIR__.'/app/api.php';require_once __DIR__.'/views/helpers.php';
$request_url=$_SERVER['REQUEST_URI']??url('fr/index.html');$request_path=parse_url($_SERVER['REQUEST_URI']??url('fr/index.html'),PHP_URL_PATH);$relative=substr($request_path,strlen(APP_BASE));$relative=preg_replace('~/+~','/',$relative);
if($relative==='/'||$relative===''||$relative==='/index.php'){header('Location: '.url('fr/index.html'),true,302);exit;}
preg_match('~^/(fr|ar)/(.+\.html)$~',$relative,$route);$page=$route[2]??'404.html';$_GET['lang']=$route[1]??'fr';start();$_SESSION['lang']=lang();$routes=require __DIR__.'/app/routes.php';$known=isset($routes[$page]);if(!$known){$page='404.html';http_response_code(404);}
$private=preg_match('~^(espace-client|espace-pro|admin|crm)/~',$page)===1;headers_safe($private||preg_match('~auth/|connexion|inscription|compte-type|404|500|demo~',$page));$nonce=base64_encode(random_bytes(18));
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce' https://maps.googleapis.com https://maps.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: blob: https://maps.googleapis.com https://maps.gstatic.com https://*.googleapis.com https://*.gstatic.com; font-src 'self' https://fonts.gstatic.com; connect-src 'self' https://maps.googleapis.com https://maps.gstatic.com; frame-src https://www.google.com; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
$user=null;$error=null;$result=null;$flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
try{
 $user=actor();if($private&&!$user){header('Location: '.link_page('connexion.html',['return'=>$page]),true,302);exit;}
 if(str_starts_with($page,'admin/')||str_starts_with($page,'crm/'))admin();if(str_starts_with($page,'espace-pro/'))roles(['professional','veterinarian']);
 if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
  $b=$_POST;csrf_check($b);$action=(string)($b['_action']??'');
  if(str_starts_with($action,'/wizard/')){require_once __DIR__.'/app/wizard.php';$result=wizard_action($action,$b);}else{$result=dispatch($action,'POST',$b);}
  $next=(string)($b['_next']??$request_url);if(isset($result['_next']))$next=$result['_next'];if($action==='/auth/login'){$who=$result['user']['role'];$target=$who==='admin'?'admin/index.html':(in_array($who,['professional','veterinarian'],true)?'espace-pro/index.html':'espace-client/index.html');$next=link_page($target);}
  if($action==='/auth/register-professional')$next=link_page('espace-pro/profil.html');
  if($action==='/orders'&&isset($result['order_id']))$next=link_page('espace-client/commande.html',['id'=>$result['order_id']]);
  if($action==='/uploads'&&isset($result['path']))$_SESSION['last_upload']=$result;
  if($action==='/orders/preview')$_SESSION['quote']=$result;
  if(!str_starts_with($next,url(lang().'/'))||str_contains($next,"\r")||str_contains($next,"\n"))$next=link_page('index.html');
  $_SESSION['flash']=$result['message']??tr('Enregistrement effectué.','تم الحفظ.');header('Location: '.$next,true,303);exit;
 }
}catch(ApiError $e){http_response_code($e->status);$error=$e->getMessage();}catch(Throwable $e){http_response_code(503);error_log('PETICA page '.get_class($e));$error=tr('Le service est momentanément indisponible. Vos modifications ne sont pas confirmées.','الخدمة غير متاحة مؤقتًا. لم يتم تأكيد التغييرات.');}
$titles=['index.html'=>['Une identité permanente. Un lien qui reste.','هوية دائمة. ورابط يبقى.'],'tarifs.html'=>['Une identité, un choix clair.','هوية واحدة، اختيار واضح.'],'world.html'=>['Ce qui se prépare.','ما نعدّه للمستقبل.'],'futur.html'=>['PETICA World','عالم PETICA'],'mouvement.html'=>['The Billion Movement','حركة المليار'],'billion-movement.html'=>['The Billion Movement','حركة المليار'],'connexion.html'=>['Retrouver votre espace','العودة إلى مساحتك'],'a-propos.html'=>['Le lien, au fil du temps.','الرابط عبر الزمن.'],'comment-ca-marche.html'=>['Une identité qui accompagne.','هوية ترافق رفيقك.'],'animal-perdu.html'=>['Un chemin pour se retrouver.','طريق للعودة.'],'contact.html'=>['Parlons de votre compagnon.','لنتحدث عن رفيقك.'],'faq.html'=>['Des réponses, simplement.','إجابات ببساطة.'],'produits/carte-identite.html'=>['Son identité, entre vos mains.','هويته بين يديك.'],'produits/qr-tag.html'=>['Un lien, à portée de scan.','رابط بمجرد مسح الرمز.']];
$title=isset($titles[$page])?tr(...$titles[$page]):tr('Votre espace PETICA','مساحتك في PETICA');
$content='';ob_start();try{if($error&&$private&&(!$user||http_response_code()===403)){}elseif(str_contains($page,'inscription')||str_ends_with($page,'ajouter-compagnon.html')){require __DIR__.'/views/registration.php';}elseif($private){require __DIR__.'/views/workspace.php';}else{require __DIR__.'/views/public.php';}}catch(ApiError $e){http_response_code($e->status);$error=$e->getMessage();}catch(Throwable $e){http_response_code(503);error_log('PETICA view '.get_class($e));$error=tr('Ces informations sont momentanément indisponibles. Réessayez.','المعلومات غير متاحة مؤقتًا. حاول مجددًا.');}$content=ob_get_clean();
require __DIR__.'/views/layout.php';
