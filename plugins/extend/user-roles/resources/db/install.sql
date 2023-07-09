CREATE TABLE `sunlight_user_role`
(
    `id`       int(11) NOT NULL AUTO_INCREMENT,
    `user_id`  int(11) NOT NULL,
    `group_id` int(11) NOT NULL,
    `since`    int(11) NOT NULL,
    `until`    int(11) NULL,
    PRIMARY KEY (`id`),
    KEY        `user_id` (`user_id`),
    KEY        `group_id` (`group_id`),
    KEY        `until` (`until`),
    KEY        `since` (`since`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `sunlight_user_group`
    ADD `manageroles` TINYINT(1) NOT NULL DEFAULT '0';

UPDATE `sunlight_user_group`
SET `manageroles` = '1'
WHERE `sunlight_user_group`.`id` = 1;