SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

START TRANSACTION;

-- Таблица `users`
DROP TABLE IF EXISTS `request`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `fullname`   text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthdate`  date         DEFAULT NULL,
  `phone`      varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email`      varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `login`      varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password`   varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin`   tinyint(1)   NOT NULL DEFAULT 0,
  `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Единственный изначальный пользователь — администратор.
-- Логин: Admin26, пароль: Demo20 (bcrypt-хэш сгенерирован password_hash).
INSERT INTO `users` (`id`, `fullname`, `birthdate`, `phone`, `email`, `login`, `password`, `is_admin`, `created_at`) VALUES
(1, 'Администратор', '1990-01-01', '+7(000)000-00-00', 'admin@passazhiram.local', 'Admin26',
 '$2y$12$FoElWYpny0S0/nnT5BITTOTubAIy1ioPYNkluIY6.RwRoAsvnZYCG', 1, CURRENT_TIMESTAMP);

-- Таблица `request`
CREATE TABLE `request` (
  `id`       int          NOT NULL AUTO_INCREMENT,
  `user_id`  int          NOT NULL,
  `date`     datetime     NOT NULL,
  `status`   varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Новая',
  `curses`   varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment`  text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment`  text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `review`   text         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `request_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
