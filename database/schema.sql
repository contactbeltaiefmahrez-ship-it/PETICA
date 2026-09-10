-- =========================================================================
-- PETICA — MySQL schema for XAMPP (MySQL 5.7+/MariaDB 10.2+, InnoDB, utf8mb4)
--
-- This is the ORIGINAL platform's data model (built first on Node/Express
-- + SQLite), translated table-for-table to MySQL and collapsed into its
-- final shape — the SQLite version went through 4 incremental migrations;
-- this file is what running all 4 in order produces, written directly
-- rather than replayed as history, since a fresh XAMPP install has no
-- history to preserve.
--
-- Import via phpMyAdmin ("Import" tab) or:
--   mysql -u root petica < schema.sql
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- USERS & AUTH
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          VARCHAR(20) NOT NULL CHECK (role IN ('customer','professional','veterinarian','admin')),
  status        VARCHAR(20) NOT NULL DEFAULT 'active' CHECK (status IN ('active','suspended')),
  language      VARCHAR(2) NOT NULL DEFAULT 'fr' CHECK (language IN ('fr','ar')),
  is_demo       TINYINT(1) NOT NULL DEFAULT 0,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sessions are native PHP sessions (see includes/auth.php) — this table
-- exists for parity with the original design (real logout / forced
-- revocation) but PHP's session store is the actual source of truth for
-- "is this browser currently logged in".
CREATE TABLE IF NOT EXISTS auth_tokens (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  token_id    VARCHAR(190) NOT NULL UNIQUE,
  user_agent  VARCHAR(255),
  ip          VARCHAR(64),
  expires_at  DATETIME NOT NULL,
  revoked_at  DATETIME,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_auth_tokens_user (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  token_hash  VARCHAR(255) NOT NULL UNIQUE,
  expires_at  DATETIME NOT NULL,
  used_at     DATETIME,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- PROFILES
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS customer_profiles (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL UNIQUE,
  first_name      VARCHAR(100) NOT NULL,
  last_name       VARCHAR(100) NOT NULL,
  national_id     VARCHAR(50),
  phone_primary   VARCHAR(30) NOT NULL,
  phone_secondary VARCHAR(30),
  governorate     VARCHAR(100) NOT NULL,
  address         TEXT NOT NULL,
  postal_code     VARCHAR(20),
  crm_status      VARCHAR(20) NOT NULL DEFAULT 'active' CHECK (crm_status IN ('lead','active','at_risk','inactive')),
  crm_source      VARCHAR(50) NOT NULL DEFAULT 'site',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS professional_profiles (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  user_id             INT NOT NULL UNIQUE,
  business_name       VARCHAR(150) NOT NULL,
  profession          VARCHAR(20) NOT NULL CHECK (profession IN ('veterinarian','pet_shop','groomer','trainer','breeder','shelter','other')),
  profession_other    VARCHAR(100),
  phone               VARCHAR(30) NOT NULL,
  city                VARCHAR(100) NOT NULL,
  verification_status VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (verification_status IN ('pending','approved','rejected')),
  verification_notes  TEXT,
  verified_at         DATETIME,
  verified_by         INT,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_pro_profiles_status (verification_status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (verified_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- COMPANIONS & IDENTITY
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS species_codes (
  species VARCHAR(20) PRIMARY KEY,
  code    CHAR(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO species_codes (species, code) VALUES
  ('dog', 'DOG'), ('cat', 'CAT'), ('horse', 'HRS'),
  ('bird', 'BRD'), ('rabbit', 'RAB'), ('other', 'OTH');

-- Atomic per-species counters — incremented inside a single transaction
-- (see includes/serial.php) so two simultaneous registrations can never
-- receive the same serial number.
CREATE TABLE IF NOT EXISTS serial_counters (
  species_code VARCHAR(3) PRIMARY KEY,
  last_value   INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS companions (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  serial_number         VARCHAR(20) NOT NULL UNIQUE,
  species               VARCHAR(20) NOT NULL,
  species_code          CHAR(3) NOT NULL,
  name                  VARCHAR(100) NOT NULL,
  breed                 VARCHAR(100),
  sex                   VARCHAR(10) CHECK (sex IN ('male','female')),
  date_of_birth         DATE,
  colors_marks          VARCHAR(255),
  weight_kg             DECIMAL(6,2),
  microchip_number      VARCHAR(50),
  vaccinated            TINYINT(1) NOT NULL DEFAULT 0,
  medical_info          TEXT,
  photo_path            VARCHAR(255),
  paw_photo_path        VARCHAR(255),
  status                VARCHAR(20) NOT NULL DEFAULT 'present' CHECK (status IN ('present','lost','deceased')),
  lost_message          TEXT,
  lost_since            DATETIME,
  public_contact_name   VARCHAR(150),
  public_contact_phone  VARCHAR(30),
  public_show_breed     TINYINT(1) NOT NULL DEFAULT 1,
  created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_companions_status (status),
  INDEX idx_companions_serial (serial_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Join table rather than a single owner_id on companions: models real
-- co-ownership without ever needing a new identity or a new QR.
CREATE TABLE IF NOT EXISTS companion_owners (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  companion_id   INT NOT NULL,
  user_id        INT NOT NULL,
  relationship   VARCHAR(20) NOT NULL DEFAULT 'primary' CHECK (relationship IN ('primary','secondary')),
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_companion_owner (companion_id, user_id),
  INDEX idx_companion_owners_user (user_id),
  INDEX idx_companion_owners_companion (companion_id),
  FOREIGN KEY (companion_id) REFERENCES companions(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Name-only "additional owner" invites (simple flow: name, then
-- confirmation) — kept separate from companion_owners, which requires a
-- real user_id.
CREATE TABLE IF NOT EXISTS companion_owner_invites (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  companion_id   INT NOT NULL,
  name           VARCHAR(150) NOT NULL,
  status         VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','confirmed')),
  invited_by     INT NOT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  confirmed_at   DATETIME,
  INDEX idx_owner_invites_companion (companion_id),
  FOREIGN KEY (companion_id) REFERENCES companions(id) ON DELETE CASCADE,
  FOREIGN KEY (invited_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Two permanent, independent QR destinations per companion. `token` is
-- what's actually encoded/printed — resolution always reads the *current*
-- companion row, so editing info never touches this table.
CREATE TABLE IF NOT EXISTS qr_identities (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  companion_id  INT NOT NULL,
  kind          VARCHAR(20) NOT NULL CHECK (kind IN ('identity','tag')),
  token         VARCHAR(64) NOT NULL UNIQUE,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_companion_kind (companion_id, kind),
  FOREIGN KEY (companion_id) REFERENCES companions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- VETERINARY RECORDS
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS veterinary_records (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  companion_id     INT NOT NULL,
  clinic_name      VARCHAR(150),
  visit_date       DATE NOT NULL,
  reason           VARCHAR(255) NOT NULL,
  diagnosis        TEXT,
  treatment        TEXT,
  vaccination      TINYINT(1) NOT NULL DEFAULT 0,
  vaccination_name VARCHAR(150),
  medication       TEXT,
  notes            TEXT,
  created_by       INT NOT NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_vetrec_companion (companion_id, visit_date),
  FOREIGN KEY (companion_id) REFERENCES companions(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS veterinary_attachments (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  record_id     INT NOT NULL,
  file_path     VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type     VARCHAR(100) NOT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (record_id) REFERENCES veterinary_records(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- PROFESSIONAL ↔ CLIENT + SESSION NOTES (distinct from vet records —
-- a groomer/trainer/breeder session note, not a medical record)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS professional_clients (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  professional_user_id  INT NOT NULL,
  customer_user_id      INT NOT NULL,
  notes                 TEXT,
  created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_pro_client (professional_user_id, customer_user_id),
  INDEX idx_pro_clients_pro (professional_user_id),
  INDEX idx_pro_clients_customer (customer_user_id),
  FOREIGN KEY (professional_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS session_notes (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  companion_id          INT NOT NULL,
  professional_user_id  INT NOT NULL,
  session_type          VARCHAR(20) NOT NULL DEFAULT 'consultation'
                          CHECK (session_type IN ('consultation','training','grooming','breeding','observation','other')),
  session_date          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  note                  TEXT NOT NULL,
  recommendation        TEXT,
  follow_up_date        DATE,
  created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_session_notes_companion (companion_id, session_date),
  INDEX idx_session_notes_pro (professional_user_id),
  FOREIGN KEY (companion_id) REFERENCES companions(id) ON DELETE CASCADE,
  FOREIGN KEY (professional_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- PRODUCTS & ORDERS
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  code       VARCHAR(30) NOT NULL UNIQUE CHECK (code IN ('id_card','qr_tag','qr_tag_gold','combo','combo_gold')),
  name_fr    VARCHAR(150) NOT NULL,
  name_ar    VARCHAR(150) NOT NULL,
  price_tnd  DECIMAL(8,2) NOT NULL,
  is_combo   TINYINT(1) NOT NULL DEFAULT 0,
  active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS promo_codes (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  code                  VARCHAR(50) NOT NULL UNIQUE,
  discount_type         VARCHAR(10) NOT NULL CHECK (discount_type IN ('percent','fixed')),
  discount_value        DECIMAL(8,2) NOT NULL,
  min_order_tnd         DECIMAL(8,2) NOT NULL DEFAULT 0,
  applicable_product_id INT,
  usage_limit           INT,
  per_user_limit        INT NOT NULL DEFAULT 1,
  used_count            INT NOT NULL DEFAULT 0,
  active                TINYINT(1) NOT NULL DEFAULT 1,
  expires_at            DATETIME,
  created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (applicable_product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS promo_code_redemptions (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  promo_code_id  INT NOT NULL,
  user_id        INT NOT NULL,
  order_id       INT NOT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  order_number      VARCHAR(20) NOT NULL UNIQUE,
  user_id           INT NOT NULL,
  companion_id      INT,
  subtotal_tnd      DECIMAL(8,2) NOT NULL,
  discount_tnd      DECIMAL(8,2) NOT NULL DEFAULT 0,
  delivery_fee_tnd  DECIMAL(8,2) NOT NULL DEFAULT 8,
  total_tnd         DECIMAL(8,2) NOT NULL,
  promo_code_id     INT,
  status            VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (status IN
                      ('pending','contact_required','confirmed','approved','in_production',
                       'ready','shipped','delivered','completed','cancelled','rejected')),
  shipping_address  TEXT NOT NULL,
  notes             TEXT,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (companion_id) REFERENCES companions(id),
  FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  order_id        INT NOT NULL,
  product_id      INT NOT NULL,
  quantity        INT NOT NULL DEFAULT 1,
  unit_price_tnd  DECIMAL(8,2) NOT NULL,
  line_total_tnd  DECIMAL(8,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_status_history (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  order_id    INT NOT NULL,
  status      VARCHAR(20) NOT NULL,
  note        TEXT,
  changed_by  INT,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (changed_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- NOTIFICATIONS
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  type         VARCHAR(50) NOT NULL,
  title        VARCHAR(150) NOT NULL,
  body         TEXT NOT NULL,
  related_type VARCHAR(50),
  related_id   INT,
  read_at      DATETIME,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notifications_user (user_id, read_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- CRM
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS crm_notes (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  customer_user_id INT NOT NULL,
  author_user_id   INT NOT NULL,
  note             TEXT NOT NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_crm_notes_customer (customer_user_id),
  FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (author_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS crm_interactions (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  customer_user_id INT NOT NULL,
  author_user_id   INT NOT NULL,
  channel          VARCHAR(20) NOT NULL CHECK (channel IN ('call','whatsapp','email','message','in_person','other')),
  summary          TEXT NOT NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_crm_interactions_customer (customer_user_id, created_at),
  FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (author_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS crm_followups (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  customer_user_id INT NOT NULL,
  author_user_id   INT NOT NULL,
  title            VARCHAR(200) NOT NULL,
  due_date         DATE,
  status           VARCHAR(20) NOT NULL DEFAULT 'scheduled' CHECK (status IN ('pending','scheduled','completed')),
  notes            TEXT,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at     DATETIME,
  INDEX idx_crm_followups_customer (customer_user_id),
  INDEX idx_crm_followups_status (status),
  FOREIGN KEY (customer_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (author_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- THE BILLION MOVEMENT — holds no running counter on purpose. The
-- remaining count is always `target - COUNT(companions)`, computed live.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS billion_movement (
  id               INT PRIMARY KEY CHECK (id = 1),
  target           BIGINT NOT NULL DEFAULT 1000000000,
  milestone_config TEXT,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO billion_movement (id, target) VALUES (1, 1000000000);

-- ---------------------------------------------------------------------
-- NERO — scripted knowledge base (data-driven so content can be edited
-- without a deploy; can be swapped for a real LLM later behind the same
-- ask() interface in includes/nero.php)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS nero_answers (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  lang       VARCHAR(2) NOT NULL CHECK (lang IN ('fr','ar')),
  keywords   TEXT NOT NULL,   -- JSON array of match keywords
  answer     TEXT NOT NULL,
  active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- SYSTEM
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS system_settings (
  `key`      VARCHAR(100) PRIMARY KEY,
  value      TEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_logs (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  actor_user_id  INT,
  action         VARCHAR(100) NOT NULL,
  entity_type    VARCHAR(50),
  entity_id      INT,
  meta           TEXT,
  ip             VARCHAR(64),
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_actor (actor_user_id),
  FOREIGN KEY (actor_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
