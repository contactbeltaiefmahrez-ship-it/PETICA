<?php
// Reentrant migration: MySQL/MariaDB DDL commits implicitly. Version is recorded only after success.
return function(PDO $pdo): void {
 $column=function($table,$name,$definition)use($pdo){$s=$pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=? AND column_name=?');$s->execute([$table,$name]);if(!(int)$s->fetchColumn())$pdo->exec("ALTER TABLE `$table` ADD COLUMN `$name` $definition");};
 $column('users','auth_version','INT NOT NULL DEFAULT 0');
 $column('companions','version','INT NOT NULL DEFAULT 1');
 $column('companions','public_contact_consent','TINYINT NOT NULL DEFAULT 0');
 $pdo->exec('ALTER TABLE companions MODIFY vaccinated TINYINT(1) NULL DEFAULT NULL');
 $column('professional_clients','consent_status',"VARCHAR(20) NOT NULL DEFAULT 'pending'");
 $column('professional_clients','scopes',"VARCHAR(150) NOT NULL DEFAULT 'identity'");
 $column('professional_clients','consented_at','DATETIME NULL');
 $column('orders','quote_snapshot','LONGTEXT NULL');$column('orders','production_snapshot','LONGTEXT NULL');
 $column('orders','version','INT NOT NULL DEFAULT 1');$column('orders','ordered_by','INT NULL');
 $column('orders','production_stage',"VARCHAR(20) NOT NULL DEFAULT 'draft'");
 $column('order_items','companion_id','INT NULL');$column('order_items','owner_invite_id','INT NULL');
 $column('order_items','net_millimes','BIGINT NULL');$column('order_items','discount_millimes','BIGINT NULL');
 $column('promo_codes','starts_at','DATETIME NULL');$column('promo_codes','stackable','TINYINT NOT NULL DEFAULT 0');
 $column('products','commercial_approved','TINYINT NOT NULL DEFAULT 0');$column('products','price_version','INT NOT NULL DEFAULT 1');
 $ddl=file_get_contents(__DIR__.'/001_tables.sql');foreach(explode(';',$ddl) as $sql)if(trim($sql)!=='')$pdo->exec($sql);
 // Preserve all original numbers and tokens; counters are advanced, never rewound.
 $rows=$pdo->query("SELECT species_code,MAX(CAST(SUBSTRING(serial_number,5) AS UNSIGNED)) n FROM companions WHERE serial_number REGEXP '^[A-Z]{3}-[0-9]{9}$' GROUP BY species_code")->fetchAll();
 foreach($rows as $r)$pdo->prepare('INSERT INTO serial_counters(species_code,last_value) VALUES(?,?) ON DUPLICATE KEY UPDATE last_value=GREATEST(last_value,VALUES(last_value))')->execute([$r['species_code'],$r['n']]);
 // Existing identities belong to the historical boundary, not new registrations.
 $pdo->exec("INSERT IGNORE INTO registration_events(companion_id,source,counted) SELECT c.id,IF(EXISTS(SELECT 1 FROM companion_owners co JOIN users u ON u.id=co.user_id WHERE co.companion_id=c.id AND u.is_demo=1),'demo','historical'),0 FROM companions c");
 $pdo->prepare('INSERT IGNORE INTO system_settings(`key`,value) VALUES(?,?)')->execute(['movement_baseline','100']);
 $pdo->prepare('INSERT IGNORE INTO system_settings(`key`,value) VALUES(?,?)')->execute(['commercial',json_encode(['version'=>1,'delivery_millimes'=>8000,'multi_step'=>2,'multi_cap'=>null,'stacking_approved'=>false,'pro_percent'=>10,'secondary_percent'=>10])]);
 $products=[['id_card','Carte d’identité','بطاقة الهوية',49,0],['qr_tag','Médaille argent','ميدالية فضية',49,0],['qr_tag_gold','Médaille or','ميدالية ذهبية',59,0],['combo','Combo argent','باقة فضية',79,1],['combo_gold','Combo or','باقة ذهبية',89,1]];
 foreach($products as $r){$pdo->prepare('INSERT IGNORE INTO products(code,name_fr,name_ar,price_tnd,is_combo) VALUES(?,?,?,?,?)')->execute($r);if(!$r[4])$pdo->prepare('UPDATE products SET commercial_approved=1 WHERE code=? AND price_tnd=?')->execute([$r[0],$r[3]]);}
};
