CREATE TABLE `zealot`.`live_stats` (
  `CountryISO` VARCHAR(2) NOT NULL,
  `Count` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`CountryISO`)
) ENGINE = MEMORY;

CREATE TABLE `zealot`.`meta_stats` (
  `CountryISO` VARCHAR(2) NOT NULL,
  `Count` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`CountryISO`)
) ENGINE = InnoDB;

CREATE TABLE `zealot`.`blacklist` (
  `CountryISO` VARCHAR(2) NOT NULL,
  PRIMARY KEY (`CountryISO`)
) ENGINE = InnoDB;

INSERT INTO `zealot`.`blacklist` (`CountryISO`) VALUES ("CN"), ("BR"), ("RU"), ("NG"), ("VN");
