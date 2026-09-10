<?php
require __DIR__.'/../app/core.php';
if(PHP_SAPI!=='cli'||!str_ends_with(DB_NAME,'_test'))exit(1);
$users=[];
foreach(['admin'=>'admin','owner'=>'customer','stranger'=>'customer','vet'=>'professional','groomer'=>'professional'] as $name=>$role){
 q('INSERT INTO users(email,password_hash,role,language) VALUES(?,?,?,"fr")',[$name.'@petica-test.invalid',password_hash('Petica-Test-2026!',PASSWORD_DEFAULT),$role]);$id=(int)db()->lastInsertId();$users[$name]=$id;
 if($role==='professional')q('INSERT INTO professional_profiles(user_id,business_name,profession,phone,city) VALUES(?,?,?,?,?)',[$id,'TEST '.$name,$name==='vet'?'veterinarian':'groomer','20000000','Test']);
}
echo json_encode($users);
