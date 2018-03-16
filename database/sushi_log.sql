CREATE TABLE `sushi_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'auto_increment ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `osushi_id` int(6) NOT NULL COMMENT 'Osushi ID',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Osushi log table'
/*!50100 PARTITION BY LIST (log_id)
(PARTITION p0 VALUES IN (0) ENGINE = InnoDB,
 PARTITION p1 VALUES IN (1) ENGINE = InnoDB,
 PARTITION p2 VALUES IN (2) ENGINE = InnoDB,
 PARTITION p3 VALUES IN (3) ENGINE = InnoDB) */