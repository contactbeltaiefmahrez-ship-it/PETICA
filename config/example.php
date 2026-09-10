<?php
// Copy to local.php. Environment variables PETICA_<KEY> override these values.
return [
 'APP_URL'=>'http://localhost/petica','PUBLIC_URL'=>'https://petica.pet',
 'DB_HOST'=>'127.0.0.1','DB_PORT'=>'3306','DB_NAME'=>'petica','DB_USER'=>'petica','DB_PASS'=>'',
 'ENV'=>'local','APP_KEY'=>'', // Generate: php bin/console.php key
 'MAPS_KEY'=>'','MAPS_MAP_ID'=>'',
 'EMAIL_ENABLED'=>false,'EMAIL_FROM'=>'', // Configure and test PHP mail before enabling.
 'LOCATION_DAYS'=>30,'ALLOW_DEMO'=>false,
];
