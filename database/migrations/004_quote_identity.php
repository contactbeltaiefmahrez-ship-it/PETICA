<?php
return function(PDO $pdo): void {
 if(!(int)$pdo->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='orders' AND column_name='quote_id'")->fetchColumn())$pdo->exec('ALTER TABLE orders ADD quote_id CHAR(48) NULL UNIQUE');
};
