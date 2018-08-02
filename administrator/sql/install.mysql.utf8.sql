CREATE TABLE IF NOT EXISTS `#__orgfile` (
  `id` int(11) NOT NULL,
  `file` text NOT NULL,
  `hash` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;