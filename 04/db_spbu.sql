-- =============================================
-- DATABASE: db_24jam
-- =============================================

CREATE DATABASE IF NOT EXISTS `db_24jam`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `db_24jam`;

-- ---------------------------------------------
-- Tabel: spbu
-- ---------------------------------------------

CREATE TABLE IF NOT EXISTS `spbu` (
  `id`        INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_spbu` VARCHAR(100) NOT NULL,
  `no_wa`     VARCHAR(20)  NOT NULL,
  `status`    ENUM('yes','no') NOT NULL DEFAULT 'no',
  `latitude`  DOUBLE       NOT NULL,
  `longitude` DOUBLE       NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
