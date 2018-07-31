CREATE TABLE `foo_three` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bar_id` int(11) unsigned DEFAULT NULL,
  `foo_three_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `foo_three_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_foo_three_key` (`foo_three_key`),
  KEY `idx_foo_three_value` (`foo_three_value`),
  KEY `bar_id` (`bar_id`),
  CONSTRAINT `foo_three_ibfk_1` FOREIGN KEY (`bar_id`) REFERENCES `bar` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
