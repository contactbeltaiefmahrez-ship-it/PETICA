<?php
require_once __DIR__.'/../app/api.php';headers_safe();
try {
 $requested=preg_replace('~^'.preg_quote(APP_BASE,'~').'/api~','',parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));$path=str_ends_with($requested,'.php')?($petica_api_route??$requested):$requested;
 $data=dispatch($path,$_SERVER['REQUEST_METHOD'],request_body());send_json(array_merge(['success'=>true,'data'=>$data],$data));
}catch(ApiError $e){send_json(['success'=>false,'error'=>['code'=>$e->apiCode,'message'=>$e->getMessage(),'validation_errors'=>$e->details]],$e->status);}
catch(Throwable $e){error_log('PETICA API failure '.get_class($e));send_json(['success'=>false,'error'=>['code'=>'service_unavailable','message'=>tr('Service momentanément indisponible.','الخدمة غير متاحة مؤقتًا.')]],503);}
