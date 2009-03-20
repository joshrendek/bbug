CREATE TABLE `commits` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(255) default NULL,
  `message` varchar(255) default NULL,
  `sum` varchar(255) default NULL,
  `project` int(11) default NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

CREATE TABLE `status_ups` (
  `id` int(11) NOT NULL auto_increment,
  `_id` int(11) default NULL,
  `by` int(11) default NULL,
  `type` varchar(50) default NULL,
  `project` int(11) default NULL,
  `time` int(11) default NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;


