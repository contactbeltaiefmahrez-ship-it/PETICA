<?php
require __DIR__.'/../app/core.php';
if(PHP_SAPI!=='cli'||DB_NAME!=='petica_legacy_test')exit(1);
$mode=$argv[1]??'';
if($mode==='seed'){
 $sql=preg_replace('/^--.*$/m','',file_get_contents(__DIR__.'/../database/schema.sql'));foreach(explode(';',$sql) as $s)if(trim($s)!=='')db()->exec($s);
 q('INSERT INTO users(email,password_hash,role) VALUES("legacy@petica-test.invalid",?,"customer")',[password_hash('Legacy-Test-Only-2026!',PASSWORD_DEFAULT)]);$u=(int)db()->lastInsertId();
 q('INSERT INTO customer_profiles(user_id,first_name,last_name,phone_primary,address,governorate) VALUES(?,"Legacy","Owner","20000003","Legacy test address","Tunis")',[$u]);
 q('INSERT INTO companions(serial_number,species,species_code,name,status) VALUES("DOG-000000042","dog","DOG","Legacy identity","lost")');$c=(int)db()->lastInsertId();
 q('INSERT INTO companion_owners(companion_id,user_id,relationship) VALUES(?,?,"primary")',[$c,$u]);
 q('INSERT INTO qr_identities(companion_id,kind,token) VALUES(?,"identity","11111111-1111-4111-8111-111111111111"),(?,"tag","22222222-2222-4222-8222-222222222222")',[$c,$c]);
 q('INSERT INTO crm_notes(customer_user_id,author_user_id,note) VALUES(?,?,"Original CRM history")',[$u,$u]);
 echo "Legacy fixture inserted.\n";
}elseif($mode==='verify'){
 $checks=[];$checks['serial_preserved']=one('SELECT * FROM companions WHERE serial_number="DOG-000000042"')['name']==='Legacy identity';
 $checks['two_tokens_preserved']=q('SELECT token FROM qr_identities ORDER BY id')->fetchAll(PDO::FETCH_COLUMN)===['11111111-1111-4111-8111-111111111111','22222222-2222-4222-8222-222222222222'];
 $checks['old_crm_kept']=(int)q('SELECT COUNT(*) FROM crm_notes')->fetchColumn()===1;
 $checks['crm_import_once']=(int)q('SELECT COUNT(*) FROM crm_activity WHERE body="Original CRM history"')->fetchColumn()===1;
 $checks['historic_not_recounted']=(int)q('SELECT COUNT(*) FROM registration_events WHERE counted=1')->fetchColumn()===0;
 $checks['counter_advanced']=(int)q('SELECT last_value FROM serial_counters WHERE species_code="DOG"')->fetchColumn()===42;
 $checks['state_preserved']=one('SELECT status FROM companions WHERE serial_number="DOG-000000042"')['status']==='lost';
 echo json_encode($checks,JSON_PRETTY_PRINT);exit(in_array(false,$checks,true)?1:0);
}
