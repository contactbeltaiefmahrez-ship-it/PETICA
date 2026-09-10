<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../app/core.php';
$command=$argv[1]??'help';
try{
 if($command==='key'){echo base64_encode(random_bytes(32)).PHP_EOL;exit;}
 if($command==='migrate'){
  $pdo=db();if(!(int)$pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name='users'")->fetchColumn()){$sql=file_get_contents(__DIR__.'/../database/schema.sql');$sql=preg_replace('/^--.*$/m','',$sql);foreach(explode(';',$sql) as $s)if(trim($s)!=='')$pdo->exec($s);}
  if(!(int)$pdo->query("SELECT GET_LOCK('petica_migration',30)")->fetchColumn())throw new RuntimeException('Another migration is running.');
  $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations(version VARCHAR(100) PRIMARY KEY,applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB');
  foreach(glob(__DIR__.'/../database/migrations/*.php') as $file){$v=basename($file);if(one('SELECT version FROM schema_migrations WHERE version=?',[$v]))continue;$migration=require $file;$migration($pdo);q('INSERT INTO schema_migrations(version) VALUES(?)',[$v]);echo "Applied $v\n";}
  $pdo->query("SELECT RELEASE_LOCK('petica_migration')");echo "Migration complete. No demo records installed.\n";exit;
 }
 if($command==='create-admin'){$email=$argv[2]??'';if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Usage: php bin/console.php create-admin EMAIL (password read from standard input)');echo 'Password (minimum 12 characters): ';$pw=trim(fgets(STDIN));if(strlen($pw)<12)throw new RuntimeException('Password too short.');q('INSERT INTO users(email,password_hash,role,language) VALUES(?,?,"admin","fr")',[$email,password_hash($pw,PASSWORD_DEFAULT)]);echo "Admin created.\n";exit;}
 if($command==='health'){$checks=['php'=>PHP_VERSION,'php_supported'=>version_compare(PHP_VERSION,'8.1','>='),'pdo_mysql'=>extension_loaded('pdo_mysql'),'mbstring'=>extension_loaded('mbstring'),'gd'=>extension_loaded('gd'),'openssl'=>extension_loaded('openssl'),'fileinfo'=>extension_loaded('fileinfo'),'storage_writable'=>is_writable(__DIR__.'/../storage/uploads'),'db'=>db()->getAttribute(PDO::ATTR_SERVER_VERSION),'migrations'=>q('SELECT * FROM schema_migrations')->fetchAll(),'encryption_key'=>strlen((string)base64_decode((string)cfg('APP_KEY',''),true))===32,'maps_configured'=>(bool)cfg('MAPS_KEY',''),'email_enabled'=>(bool)cfg('EMAIL_ENABLED',false)];echo json_encode($checks,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;exit;}
 if($command==='jobs'){require __DIR__.'/../app/jobs.php';echo json_encode(run_jobs(),JSON_PRETTY_PRINT).PHP_EOL;exit;}
 echo "Commands: key | migrate | create-admin EMAIL | health | jobs\n";
}catch(Throwable $e){fwrite(STDERR,get_class($e).': '.$e->getMessage().PHP_EOL);exit(1);}
