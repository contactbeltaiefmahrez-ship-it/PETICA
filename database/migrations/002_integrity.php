<?php
return function(PDO $pdo): void {
 foreach(['orders'=>['subtotal_tnd','discount_tnd','delivery_fee_tnd','total_tnd'],'order_items'=>['unit_price_tnd','line_total_tnd'],'products'=>['price_tnd']] as $t=>$cols)foreach($cols as $col)$pdo->exec("ALTER TABLE `$t` MODIFY `$col` DECIMAL(12,3) NOT NULL");
 $pdo->exec("INSERT IGNORE INTO crm_contacts(user_id,name,email,phone,kind) SELECT u.id,CONCAT(p.first_name,' ',p.last_name),u.email,p.phone_primary,'customer' FROM users u JOIN customer_profiles p ON p.user_id=u.id WHERE u.is_demo=0");
 // Legacy activity remains queryable in its original tables, joined via contact.user_id.
};
