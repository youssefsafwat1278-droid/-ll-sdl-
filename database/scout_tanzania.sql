-- ============================================================
-- Scout Tanzania — MySQL Database Script
-- Generated from Laravel migrations
-- Import this file via phpMyAdmin on InfinityFree
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- ============================================================
-- 1. migrations table (Laravel tracking)
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. password_reset_tokens
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. failed_jobs
-- ============================================================
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. personal_access_tokens (Sanctum)
-- ============================================================
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. patrols
-- ============================================================
CREATE TABLE IF NOT EXISTS `patrols` (
    `patrol_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `patrol_name` VARCHAR(50) NOT NULL,
    `patrol_logo_url` VARCHAR(255) NULL DEFAULT NULL,
    `patrol_color` VARCHAR(7) NULL DEFAULT NULL,
    `total_points` INT NOT NULL DEFAULT 0,
    `rank` INT NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `patrols_patrol_name_unique` (`patrol_name`),
    KEY `idx_rank` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. scouts (with local/external ownership columns)
-- ============================================================
CREATE TABLE IF NOT EXISTS `scouts` (
    `scout_id` VARCHAR(10) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `patrol_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `role` ENUM('scout','leader','senior') NOT NULL,
    `photo_url` VARCHAR(255) NULL DEFAULT NULL,
    `initial_price` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `current_price` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `price_change` DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    `price_trend` VARCHAR(10) NOT NULL DEFAULT 'stable',
    `total_points` INT NOT NULL DEFAULT 0,
    `gameweek_points` INT NOT NULL DEFAULT 0,
    `form` DECIMAL(3,1) NOT NULL DEFAULT 0.0,
    `ownership_count` INT NOT NULL DEFAULT 0,
    `local_ownership_count` INT NOT NULL DEFAULT 0,
    `external_ownership_count` INT NOT NULL DEFAULT 0,
    `previous_ownership_count` INT NOT NULL DEFAULT 0,
    `ownership_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `ownership_average` DECIMAL(3,1) NOT NULL DEFAULT 0.0,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `status` ENUM('available','injured','suspended') NOT NULL DEFAULT 'available',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`scout_id`),
    KEY `idx_patrol` (`patrol_id`),
    KEY `idx_ownership` (`ownership_count`, `is_available`),
    KEY `idx_price_trend_price` (`price_trend`, `current_price`),
    CONSTRAINT `scouts_patrol_id_foreign` FOREIGN KEY (`patrol_id`) REFERENCES `patrols` (`patrol_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. users (with email column)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `scout_id` VARCHAR(10) NOT NULL,
    `email` VARCHAR(190) NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(50) NULL DEFAULT NULL,
    `last_name` VARCHAR(50) NULL DEFAULT NULL,
    `patrol_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `photo_url` VARCHAR(255) NULL DEFAULT NULL,
    `team_name` VARCHAR(50) NOT NULL DEFAULT 'فريقي',
    `bank_balance` DECIMAL(4,1) NOT NULL DEFAULT 100.0,
    `total_points` INT NOT NULL DEFAULT 0,
    `gameweek_points` INT NOT NULL DEFAULT 0,
    `free_transfers` INT NOT NULL DEFAULT 3,
    `triple_captain_used` INT NOT NULL DEFAULT 0,
    `bench_boost_used` TINYINT(1) NOT NULL DEFAULT 0,
    `free_hit_used` TINYINT(1) NOT NULL DEFAULT 0,
    `theme` ENUM('light','dark') NOT NULL DEFAULT 'light',
    `language` ENUM('ar','en') NOT NULL DEFAULT 'ar',
    `notifications_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `profile_public` TINYINT(1) NOT NULL DEFAULT 1,
    `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
    `last_login` TIMESTAMP NULL DEFAULT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `users_scout_id_unique` (`scout_id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `idx_scout_id` (`scout_id`),
    KEY `idx_patrol_id` (`patrol_id`),
    KEY `idx_total_points` (`total_points`),
    CONSTRAINT `users_scout_id_foreign` FOREIGN KEY (`scout_id`) REFERENCES `scouts` (`scout_id`) ON DELETE CASCADE,
    CONSTRAINT `users_patrol_id_foreign` FOREIGN KEY (`patrol_id`) REFERENCES `patrols` (`patrol_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. gameweeks
-- ============================================================
CREATE TABLE IF NOT EXISTS `gameweeks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `gameweek_number` INT NOT NULL,
    `name` VARCHAR(100) NULL DEFAULT NULL,
    `date` DATE NOT NULL,
    `location` VARCHAR(100) NULL DEFAULT NULL,
    `photo_url` VARCHAR(255) NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `deadline` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_current` TINYINT(1) NOT NULL DEFAULT 0,
    `is_finished` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `gameweeks_gameweek_number_unique` (`gameweek_number`),
    KEY `idx_gameweek_number` (`gameweek_number`),
    KEY `idx_status` (`is_current`, `is_finished`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. user_teams
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_teams` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `scout_id` VARCHAR(10) NOT NULL,
    `position_in_squad` INT NOT NULL,
    `is_captain` TINYINT(1) NOT NULL DEFAULT 0,
    `is_vice_captain` TINYINT(1) NOT NULL DEFAULT 0,
    `purchase_price` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `current_price` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `unique_user_scout_gw` (`user_id`, `scout_id`, `gameweek_id`),
    KEY `idx_user_gameweek` (`user_id`, `gameweek_id`),
    CONSTRAINT `user_teams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_teams_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_teams_scout_id_foreign` FOREIGN KEY (`scout_id`) REFERENCES `scouts` (`scout_id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. chip_usages
-- ============================================================
CREATE TABLE IF NOT EXISTS `chip_usages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `chip_type` ENUM('triple_captain','bench_boost','free_hit') NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_user_gameweek` (`user_id`, `gameweek_id`),
    CONSTRAINT `chip_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chip_usages_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. scout_gameweek_performances
-- ============================================================
CREATE TABLE IF NOT EXISTS `scout_gameweek_performances` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `scout_id` VARCHAR(10) NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `attendance_points` INT NOT NULL DEFAULT 0,
    `interaction_points` INT NOT NULL DEFAULT 0,
    `uniform_points` INT NOT NULL DEFAULT 0,
    `activity_points` INT NOT NULL DEFAULT 0,
    `service_points` INT NOT NULL DEFAULT 0,
    `committee_points` INT NOT NULL DEFAULT 0,
    `mass_points` INT NOT NULL DEFAULT 0,
    `confession_points` INT NOT NULL DEFAULT 0,
    `group_mass_points` INT NOT NULL DEFAULT 0,
    `tribe_mass_points` INT NOT NULL DEFAULT 0,
    `aswad_points` INT NOT NULL DEFAULT 0,
    `first_group_points` INT NOT NULL DEFAULT 0,
    `largest_patrol_points` INT NOT NULL DEFAULT 0,
    `penalty_points` INT NOT NULL DEFAULT 0,
    `total_points` INT NOT NULL DEFAULT 0,
    `notes` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `unique_scout_gw` (`scout_id`, `gameweek_id`),
    KEY `idx_scout_gameweek` (`scout_id`, `gameweek_id`),
    CONSTRAINT `sgp_scout_id_foreign` FOREIGN KEY (`scout_id`) REFERENCES `scouts` (`scout_id`) ON DELETE CASCADE,
    CONSTRAINT `sgp_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. transfers
-- ============================================================
CREATE TABLE IF NOT EXISTS `transfers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `scout_out_id` VARCHAR(10) NOT NULL,
    `scout_in_id` VARCHAR(10) NOT NULL,
    `price_out` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `price_in` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `transfer_cost` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_user_gameweek` (`user_id`, `gameweek_id`),
    CONSTRAINT `transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `transfers_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE,
    CONSTRAINT `transfers_scout_out_id_foreign` FOREIGN KEY (`scout_out_id`) REFERENCES `scouts` (`scout_id`) ON DELETE NO ACTION,
    CONSTRAINT `transfers_scout_in_id_foreign` FOREIGN KEY (`scout_in_id`) REFERENCES `scouts` (`scout_id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. activities
-- ============================================================
CREATE TABLE IF NOT EXISTS `activities` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `image_url` VARCHAR(255) NULL DEFAULT NULL,
    `activity_date` DATE NOT NULL,
    `location` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_activity_date` (`activity_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. news
-- ============================================================
CREATE TABLE IF NOT EXISTS `news` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `image_url` VARCHAR(255) NULL DEFAULT NULL,
    `author_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_is_featured` (`is_featured`),
    CONSTRAINT `news_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. overall_rankings
-- ============================================================
CREATE TABLE IF NOT EXISTS `overall_rankings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `overall_rank` INT NOT NULL,
    `gameweek_rank` INT NULL DEFAULT NULL,
    `total_points` INT NOT NULL,
    `gameweek_points` INT NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `unique_user_gw` (`user_id`, `gameweek_id`),
    KEY `idx_overall_rank` (`overall_rank`),
    CONSTRAINT `overall_rankings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `overall_rankings_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. services
-- ============================================================
CREATE TABLE IF NOT EXISTS `services` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `icon_url` VARCHAR(255) NULL DEFAULT NULL,
    `is_new` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_is_new` (`is_new`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. patrol_rankings
-- ============================================================
CREATE TABLE IF NOT EXISTS `patrol_rankings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `patrol_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `rank` INT NOT NULL,
    `total_points` INT NOT NULL,
    `gameweek_points` INT NOT NULL DEFAULT 0,
    `point_change` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `unique_patrol_gw` (`patrol_id`, `gameweek_id`),
    KEY `idx_patrol_rank` (`rank`),
    CONSTRAINT `patrol_rankings_patrol_id_foreign` FOREIGN KEY (`patrol_id`) REFERENCES `patrols` (`patrol_id`) ON DELETE CASCADE,
    CONSTRAINT `patrol_rankings_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. price_histories
-- ============================================================
CREATE TABLE IF NOT EXISTS `price_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `scout_id` VARCHAR(10) NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `price_before` DECIMAL(3,1) NOT NULL,
    `price_after` DECIMAL(3,1) NOT NULL,
    `price_change` DECIMAL(2,1) NOT NULL,
    `ownership_count` INT NOT NULL,
    `previous_ownership_count` INT NOT NULL,
    `ownership_average` DECIMAL(3,1) NOT NULL,
    `reason` VARCHAR(50) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_price_scout_gw` (`scout_id`, `gameweek_id`),
    CONSTRAINT `price_histories_scout_id_foreign` FOREIGN KEY (`scout_id`) REFERENCES `scouts` (`scout_id`) ON DELETE CASCADE,
    CONSTRAINT `price_histories_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 19. notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('price_alert','deadline','news','ranking','other') NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `idx_user_unread` (`user_id`, `is_read`),
    CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 20. user_gameweek_points
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_gameweek_points` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `team_points` INT NOT NULL DEFAULT 0 COMMENT 'نقاط الفريق قبل الخصم',
    `transfer_penalty` INT NOT NULL DEFAULT 0 COMMENT 'عقوبة التبديلات الزائدة',
    `net_points` INT NOT NULL DEFAULT 0 COMMENT 'النقاط الصافية بعد الخصم',
    `total_points_after` INT NOT NULL DEFAULT 0 COMMENT 'الإجمالي التراكمي',
    `rank_in_gameweek` INT NOT NULL DEFAULT 0 COMMENT 'ترتيب المستخدم في الجولة',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `user_gameweek_points_user_id_gameweek_id_unique` (`user_id`, `gameweek_id`),
    KEY `user_gameweek_points_gameweek_id_index` (`gameweek_id`),
    KEY `user_gameweek_points_net_points_index` (`net_points`),
    CONSTRAINT `ugp_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ugp_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 21. patrol_gameweek_points
-- ============================================================
CREATE TABLE IF NOT EXISTS `patrol_gameweek_points` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `patrol_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_points` INT NOT NULL DEFAULT 0,
    `total_points_after` INT NOT NULL DEFAULT 0,
    `rank` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `unique_patrol_gameweek_points` (`patrol_id`, `gameweek_id`),
    KEY `idx_patrol_gw_rank` (`gameweek_id`, `rank`),
    CONSTRAINT `pgp_patrol_id_foreign` FOREIGN KEY (`patrol_id`) REFERENCES `patrols` (`patrol_id`) ON DELETE NO ACTION,
    CONSTRAINT `pgp_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 22. free_hit_snapshots (final structure with INT position)
-- ============================================================
CREATE TABLE IF NOT EXISTS `free_hit_snapshots` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `gameweek_id` BIGINT UNSIGNED NOT NULL,
    `scout_id` VARCHAR(10) NOT NULL,
    `position_in_squad` INT NOT NULL,
    `is_captain` TINYINT(1) NOT NULL DEFAULT 0,
    `is_vice_captain` TINYINT(1) NOT NULL DEFAULT 0,
    `purchase_price` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `current_price` DECIMAL(3,1) NOT NULL DEFAULT 9.0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `free_hit_snapshots_user_id_gameweek_id_index` (`user_id`, `gameweek_id`),
    CONSTRAINT `fhs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fhs_gameweek_id_foreign` FOREIGN KEY (`gameweek_id`) REFERENCES `gameweeks` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fhs_scout_id_foreign` FOREIGN KEY (`scout_id`) REFERENCES `scouts` (`scout_id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Record migrations as completed
-- ============================================================
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_100000_create_password_reset_tokens_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2026_01_13_214942_create_patrols_table', 1),
('2026_01_13_214943_create_scouts_table', 1),
('2026_01_13_214943_create_users_table', 1),
('2026_01_13_214944_create_gameweeks_table', 1),
('2026_01_13_214944_create_user_teams_table', 1),
('2026_01_13_214945_create_chip_usages_table', 1),
('2026_01_13_214945_create_scout_gameweek_performances_table', 1),
('2026_01_13_214945_create_transfers_table', 1),
('2026_01_13_214946_create_activities_table', 1),
('2026_01_13_214946_create_news_table', 1),
('2026_01_13_214947_create_overall_rankings_table', 1),
('2026_01_13_214947_create_services_table', 1),
('2026_01_13_214948_create_patrol_rankings_table', 1),
('2026_01_13_214948_create_price_histories_table', 1),
('2026_01_13_214949_create_notifications_table', 1),
('2026_01_14_120000_add_email_to_users_table', 1),
('2026_01_16_120000_create_user_gameweek_points_table', 1),
('2026_01_16_131000_update_patrol_gameweek_points_table', 1),
('2026_01_20_000001_add_local_external_ownership_to_scouts_table', 1),
('2026_01_21_090311_create_free_hit_snapshots_table', 1),
('2026_01_21_130000_change_free_hit_snapshot_position_to_integer', 1),
('2026_01_22_000000_create_patrol_gameweek_points_table', 1),
('2026_01_25_000001_fix_free_hit_snapshots_structure', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DONE! ✅ All 22 tables created successfully
-- Import this file in phpMyAdmin on InfinityFree
-- ============================================================
