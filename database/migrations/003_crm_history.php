<?php
return function(PDO $pdo): void {
 $s=$pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='crm_activity' AND column_name='legacy_source'");
 if(!(int)$s->fetchColumn())$pdo->exec('ALTER TABLE crm_activity ADD legacy_source VARCHAR(50) NULL UNIQUE');
 $pdo->exec("INSERT IGNORE INTO crm_activity(contact_id,actor_id,type,channel,body,created_at,legacy_source) SELECT c.id,n.author_user_id,'note','other',n.note,n.created_at,CONCAT('note:',n.id) FROM crm_notes n JOIN crm_contacts c ON c.user_id=n.customer_user_id");
 $pdo->exec("INSERT IGNORE INTO crm_activity(contact_id,actor_id,type,channel,body,created_at,legacy_source) SELECT c.id,n.author_user_id,'interaction',n.channel,n.summary,n.created_at,CONCAT('interaction:',n.id) FROM crm_interactions n JOIN crm_contacts c ON c.user_id=n.customer_user_id");
 $pdo->exec("INSERT IGNORE INTO crm_activity(contact_id,actor_id,type,channel,body,due_at,assigned_to,status,created_at,completed_at,legacy_source) SELECT c.id,n.author_user_id,'followup','other',CONCAT(n.title,' — ',COALESCE(n.notes,'')),n.due_date,n.author_user_id,IF(n.status='completed','completed','open'),n.created_at,n.completed_at,CONCAT('followup:',n.id) FROM crm_followups n JOIN crm_contacts c ON c.user_id=n.customer_user_id");
};
