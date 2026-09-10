<?php
require_once __DIR__.'/api.php';
function draft_get(array $u): array {$r=one('SELECT * FROM registration_drafts WHERE user_id=? AND updated_at>DATE_SUB(UTC_TIMESTAMP(),INTERVAL 30 DAY)',[$u['id']]);return $r?json_decode($r['payload'],true):[];}
function draft_put(array $u,array $d,int $step): void {$d['step']=$step;q('INSERT INTO registration_drafts(user_id,step,payload) VALUES(?,?,?) ON DUPLICATE KEY UPDATE step=VALUES(step),payload=VALUES(payload)',[$u['id'],$step,json_encode($d,JSON_UNESCAPED_UNICODE)]);}
function wizard_action(string $action,array $b): array {
 if($action==='/wizard/account'){account_register($b);return ['_next'=>url(lang().'/inscription/client.html?step=2')];}
 $u=actor(true);$d=draft_get($u);$base=url(lang().'/inscription/client.html');
 if($action==='/wizard/owner'){profile_save($b,$u);draft_put($u,$d,3);return ['_next'=>$base.'?step=3'];}
 if($action==='/wizard/companion'){$v=validate_companion($b,$u);$d=array_merge($d,$v);$d['idempotency_key']=$d['idempotency_key']??bin2hex(random_bytes(24));if(isset($b['client_id'])&&(int)$b['client_id']>0)$d['client_id']=(int)$b['client_id'];draft_put($u,$d,4);return ['_next'=>$base.'?step=4'];}
 if($action==='/wizard/photo'){foreach(['photo','paw_photo'] as $key){if(isset($_FILES[$key])&&$_FILES[$key]['error']!==UPLOAD_ERR_NO_FILE){$_FILES['file']=$_FILES[$key];$r=upload_media($u);$d[$key==='photo'?'photo_path':'paw_photo_path']=$r['path'];}}if(empty($d['photo_path']))fail(422,'photo','Ajoutez un portrait de votre compagnon.','أضف صورة لرفيقك.');draft_put($u,$d,5);return ['_next'=>$base.'?step=5'];}
 if($action==='/wizard/confirm'){if(!empty($d['companion_id']))return ['_next'=>$base.'?step=6'];if(empty($d['photo_path'])||empty($d['name']))fail(422,'draft','Complétez les étapes précédentes.');$r=create_companion($d,$u,isset($d['client_id'])?(int)$d['client_id']:null);$d['companion_id']=$r['companion']['id'];draft_put($u,$d,6);return ['_next'=>$base.'?step=6'];}
 if($action==='/wizard/order'){if(empty($d['companion_id']))fail(422,'identity','Identité manquante.');$quote=quote_create(['items'=>[['product_code'=>$b['product_code']??'id_card','companion_id'=>$d['companion_id']]],'promo_code'=>$b['promo_code']??''],$u);$_SESSION['quote']=$quote;return ['_next'=>$base.'?step=6&review=1'];}
 if($action==='/wizard/place-order'){$r=order_create($b,$u);$d['order_id']=$r['order_id'];draft_put($u,$d,7);return ['_next'=>$base.'?step=7'];}
 if($action==='/wizard/new'){q('DELETE FROM registration_drafts WHERE user_id=?',[$u['id']]);unset($_SESSION['quote']);return ['_next'=>$base.'?step=2'];}
 fail(404,'wizard','Étape introuvable.');
}
