<?php
require __DIR__.'/../app/identity.php';
if(PHP_SAPI!=='cli'||!str_ends_with(DB_NAME,'_test'))exit(1);
$u=one('SELECT * FROM users WHERE email="owner@petica-test.invalid"');
$r=create_companion(['name'=>'Concurrent test '.$argv[1],'species'=>'dog','idempotency_key'=>'parallel-'.str_pad($argv[1],20,'0',STR_PAD_LEFT)],$u);
echo $r['companion']['serial_number'];
