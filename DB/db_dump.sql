-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema expense_app
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema expense_app
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `expense_app` DEFAULT CHARACTER SET latin1 ;
USE `expense_app` ;

-- -----------------------------------------------------
-- Table `expense_app`.`configurations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`configurations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `type` VARCHAR(50) NOT NULL,
  `editable` TINYINT(1) NOT NULL DEFAULT '1',
  `weight` INT(11) NULL DEFAULT '0',
  `autoload` TINYINT(1) NULL DEFAULT '1',
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 29
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `expense_app`.`email_queue`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`email_queue` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `from_email` VARCHAR(255) NULL DEFAULT NULL,
  `from_name` VARCHAR(255) NULL DEFAULT NULL,
  `email_to` TEXT NOT NULL,
  `email_cc` TEXT NULL DEFAULT NULL,
  `email_bcc` TEXT NULL DEFAULT NULL,
  `email_reply_to` VARCHAR(255) NULL DEFAULT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `config` VARCHAR(30) NOT NULL DEFAULT 'default',
  `template` VARCHAR(50) NOT NULL,
  `layout` VARCHAR(50) NOT NULL DEFAULT 'default',
  `theme` VARCHAR(50) NULL DEFAULT NULL,
  `format` VARCHAR(5) NOT NULL DEFAULT 'html',
  `template_vars` TEXT NULL DEFAULT NULL,
  `headers` TEXT NULL DEFAULT NULL,
  `sent` TINYINT(1) NULL DEFAULT '0',
  `locked` TINYINT(1) NULL DEFAULT '0',
  `send_tries` INT(2) NULL DEFAULT '0',
  `send_at` DATETIME NOT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `expense_app`.`vendors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`vendors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(225) NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `expense_app`.`expenses_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`expenses_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(225) NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `expense_app`.`roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(200) NULL DEFAULT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `alias` (`alias` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `expense_app`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` INT(11) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `full_name` VARCHAR(200) NOT NULL,
  `password` VARCHAR(200) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `token_created` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email` (`email` ASC),
  INDEX `role_id` (`role_id` ASC),
  CONSTRAINT `users_ibfk_1`
    FOREIGN KEY (`role_id`)
    REFERENCES `expense_app`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `expense_app`.`expenses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`expenses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(225) NOT NULL,
  `reference_no` VARCHAR(225) NOT NULL,
  `vendor_id` INT(11) NOT NULL,
  `expenses_type_id` INT(11) NOT NULL,
  `expense_date` DATE NOT NULL,
  `user_id` INT(11) NOT NULL,
  `amount` FLOAT NOT NULL,
  `description` TEXT NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT '1',
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_vendor_id_idx` (`vendor_id` ASC),
  INDEX `fk_type_id_idx` (`expenses_type_id` ASC),
  INDEX `fk_user_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_vendor_id`
    FOREIGN KEY (`vendor_id`)
    REFERENCES `expense_app`.`vendors` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_type_id`
    FOREIGN KEY (`expenses_type_id`)
    REFERENCES `expense_app`.`expenses_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `expense_app`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `expense_app`.`resources`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`resources` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(225) NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `expense_app`.`logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `resource_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `details` TEXT NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_res_id_idx` (`resource_id` ASC),
  INDEX `fk_use_id_idx` (`user_id` ASC),
  CONSTRAINT `fk_res_id`
    FOREIGN KEY (`resource_id`)
    REFERENCES `expense_app`.`resources` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_use_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `expense_app`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `expense_app`.`phinxlog`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`phinxlog` (
  `version` BIGINT(20) NOT NULL,
  `migration_name` VARCHAR(100) NULL DEFAULT NULL,
  `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` TIMESTAMP NULL,
  PRIMARY KEY (`version`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `expense_app`.`roles_resources`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_app`.`roles_resources` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` INT(11) NOT NULL,
  `resource_id` INT(11) NOT NULL,
  `permission` ENUM('C','R','U','D','A') NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_role_id_idx` (`role_id` ASC),
  INDEX `fk_resources_id_idx` (`resource_id` ASC),
  CONSTRAINT `fk_role_id`
    FOREIGN KEY (`role_id`)
    REFERENCES `expense_app`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_resources_id`
    FOREIGN KEY (`resource_id`)
    REFERENCES `expense_app`.`resources` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `expense_app`.`expenses_types`
-- -----------------------------------------------------
START TRANSACTION;
USE `expense_app`;
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (1, 'Rent', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (2, 'Utilities', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (3, 'Insurance', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (4, 'Fees', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (5, 'Wages', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (6, 'Taxes', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (7, 'Interest', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (8, 'Supplies', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (9, 'Maintenance', DEFAULT, NULL);
INSERT INTO `expense_app`.`expenses_types` (`id`, `name`, `created`, `updated`) VALUES (10, 'Travel', DEFAULT, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `expense_app`.`roles`
-- -----------------------------------------------------
START TRANSACTION;
USE `expense_app`;
INSERT INTO `expense_app`.`roles` (`id`, `alias`, `name`, `description`, `created`, `modified`) VALUES (1, 'admin', 'Super Administrator', 'Super', NULL, NULL);
INSERT INTO `expense_app`.`roles` (`id`, `alias`, `name`, `description`, `created`, `modified`) VALUES (2, 'accountant', 'Manager', 'Manager', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `expense_app`.`users`
-- -----------------------------------------------------
START TRANSACTION;
USE `expense_app`;
INSERT INTO `expense_app`.`users` (`id`, `role_id`, `email`, `full_name`, `password`, `status`, `created`, `modified`, `token_created`) VALUES (1, 1, 'admin@myexpenses.com', 'Super Administrator', '$2y$10$eCfZI35afNew0mO.uT0fq.xoubVCsuxyRvxR3YCH2lZ7ro.yk6qs6', 1, NULL, NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `expense_app`.`resources`
-- -----------------------------------------------------
START TRANSACTION;
USE `expense_app`;
INSERT INTO `expense_app`.`resources` (`id`, `name`, `created`, `updated`) VALUES (1, 'Users', DEFAULT, NULL);
INSERT INTO `expense_app`.`resources` (`id`, `name`, `created`, `updated`) VALUES (2, 'Expenses', DEFAULT, NULL);
INSERT INTO `expense_app`.`resources` (`id`, `name`, `created`, `updated`) VALUES (3, 'Expense Type', DEFAULT, NULL);
INSERT INTO `expense_app`.`resources` (`id`, `name`, `created`, `updated`) VALUES (4, 'Vendors', DEFAULT, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `expense_app`.`roles_resources`
-- -----------------------------------------------------
START TRANSACTION;
USE `expense_app`;
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (1, 1, 1, 'C');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (2, 1, 1, 'R');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (3, 1, 1, 'U');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (4, 1, 1, 'D');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (5, 1, 1, 'A');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (6, 2, 1, 'R');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (7, 2, 2, 'C');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (8, 2, 2, 'R');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (9, 2, 2, 'U');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (10, 1, 2, 'C');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (11, 1, 2, 'R');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (12, 1, 2, 'U');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (13, 1, 2, 'A');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (14, 1, 3, 'C');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (15, 1, 4, 'C');
INSERT INTO `expense_app`.`roles_resources` (`id`, `role_id`, `resource_id`, `permission`) VALUES (16, 1, 4, 'R');

COMMIT;

