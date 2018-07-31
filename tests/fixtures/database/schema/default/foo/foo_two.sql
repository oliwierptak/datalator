CREATE TABLE `foo_two` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bar_id` int(11) unsigned DEFAULT NULL,
  `foo_two_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `foo_two_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_foo_two_key` (`foo_two_key`),
  KEY `idx_foo_two_value` (`foo_two_value`),
  KEY `bar_id` (`bar_id`),
  CONSTRAINT `foo_two_ibfk_1` FOREIGN KEY (`bar_id`) REFERENCES `bar` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
