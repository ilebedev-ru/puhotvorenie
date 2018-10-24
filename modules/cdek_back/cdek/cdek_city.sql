CREATE TABLE `argo`.`ps_cdek_city` (
  `id_cdek_city` INT NOT NULL,
  `id` INT(10) NULL,
  `city_name` VARCHAR(255) NULL,
  `country_id` VARCHAR(255) NULL,
  `country_iso` VARCHAR(255) NULL,
  `country_name` VARCHAR(255) NULL,
  `name` VARCHAR(255) NULL,
  `post_code_array` TEXT NULL,
  `region_id` INT(10) NULL,
  `region_name` VARCHAR(255) NULL,
  PRIMARY KEY (`id_cdek_city`));