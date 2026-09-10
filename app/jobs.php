<?php
require_once __DIR__.'/core.php';
interface NotificationTransport {public function available(): bool;public function send(array $message): bool;}
final class PhpMailTransport implements NotificationTransport {
 public function available(): bool {return (bool)cfg('EMAIL_ENABLED',false)&&filter_var(cfg('EMAIL_FROM',''),FILTER_VALIDATE_EMAIL)!==false;}
 public function send(array $m): bool {return mail($m['email'],'PETICA — notification',tr('Une nouvelle activité est disponible dans votre espace PETICA.','يتوفر نشاط جديد في مساحتك على PETICA.')."\n".APP_URL.'/'.$m['language'].'/espace-client/notifications.html',['From'=>cfg('EMAIL_FROM')]);}
}
final class UnconfiguredTransport implements NotificationTransport {public function available(): bool{return false;}public function send(array $message): bool{return false;}}
function run_jobs(): array {
 $purged=q('UPDATE qr_scan_events SET location_cipher=NULL,location_status="expired" WHERE location_expires<UTC_TIMESTAMP() AND location_cipher IS NOT NULL')->rowCount();
 q('DELETE FROM rate_limits WHERE created_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 DAY)');q('DELETE FROM registration_drafts WHERE updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 30 DAY)');q('DELETE FROM quotes WHERE expires_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 DAY)');
 $sent=0;$failed=0;$transport=new PhpMailTransport();if($transport->available())for($i=0;$i<50;$i++){$row=tx(function(){ $r=one('SELECT o.*,u.email,u.language FROM notification_outbox o JOIN notifications n ON n.id=o.notification_id JOIN users u ON u.id=n.user_id WHERE o.status IN ("queued","retry") AND o.next_attempt_at<=UTC_TIMESTAMP() AND (o.locked_until IS NULL OR o.locked_until<UTC_TIMESTAMP()) AND o.attempts<5 ORDER BY o.id LIMIT 1 FOR UPDATE');if($r)q('UPDATE notification_outbox SET locked_until=DATE_ADD(UTC_TIMESTAMP(),INTERVAL 5 MINUTE),attempts=attempts+1 WHERE id=?',[$r['id']]);return $r;});if(!$row)break;try{$ok=$transport->send($row);}catch(Throwable $e){$ok=false;}if($ok){q('UPDATE notification_outbox SET status="accepted",accepted_at=UTC_TIMESTAMP(),locked_until=NULL WHERE id=?',[$row['id']]);$sent++;}else{q('UPDATE notification_outbox SET status=IF(attempts>=5,"failed","retry"),next_attempt_at=DATE_ADD(UTC_TIMESTAMP(),INTERVAL 10 MINUTE),last_error="transport_failed",locked_until=NULL WHERE id=?',[$row['id']]);$failed++;}}
 return ['positions_expired'=>$purged,'email_accepted'=>$sent,'email_failed'=>$failed,'email_available'=>$transport->available(),'push_available'=>false,'sms_available'=>false];
}
