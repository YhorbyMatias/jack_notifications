CREATE TABLE IF NOT EXISTS `core_notifications` (
  `notification_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `type` enum('tipo1','tipo2','tipo3') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `params` varchar(254) DEFAULT NULL,
  `active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `core_notifications_token` (
  `ntoken_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `token` varchar(64) NOT NULL,
  `record` datetime NOT NULL,
  PRIMARY KEY (`ntoken_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;