-- KhanNet / Shomoysoft — Database Setup
-- 1. Create a database called jubaer_khannet in cPanel → MySQL Databases
-- 2. Assign the user 'jubaer' to it with ALL PRIVILEGES
-- 3. Run this file via cPanel → phpMyAdmin (select the DB, click Import)

CREATE TABLE IF NOT EXISTS `connection_requests` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `mobile`     VARCHAR(20)      NOT NULL,
  `area`       VARCHAR(60)      NOT NULL DEFAULT '',
  `plan`       VARCHAR(200)     NOT NULL DEFAULT '',
  `address`    TEXT,
  `message`    TEXT,
  `status`     ENUM('new','contacted','connected','cancelled') NOT NULL DEFAULT 'new',
  `notes`      TEXT,
  `ip`         VARCHAR(45)               DEFAULT '',
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shomoysoft_quotes` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `mobile`     VARCHAR(20)      NOT NULL,
  `service`    VARCHAR(100)     NOT NULL DEFAULT '',
  `budget`     VARCHAR(50)      NOT NULL DEFAULT '',
  `details`    TEXT,
  `status`     ENUM('new','contacted','completed','cancelled') NOT NULL DEFAULT 'new',
  `notes`      TEXT,
  `ip`         VARCHAR(45)               DEFAULT '',
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `username`   VARCHAR(50)      NOT NULL,
  `email`      VARCHAR(150)              DEFAULT NULL,
  `password`   VARCHAR(255)     NOT NULL,
  `role`       ENUM('super_admin','admin','viewer') NOT NULL DEFAULT 'viewer',
  `is_active`  TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the first super admin.
-- Default password: Admin@KhanNet2024
-- Generate a fresh hash: php -r "echo password_hash('YourPassword', PASSWORD_BCRYPT);"
-- Replace the hash below before importing on a new server.
INSERT IGNORE INTO `users` (`name`, `username`, `password`, `role`, `is_active`)
VALUES (
  'Administrator',
  'admin',
  '$2y$12$REPLACE_THIS_WITH_A_REAL_BCRYPT_HASH',
  'super_admin',
  1
);
