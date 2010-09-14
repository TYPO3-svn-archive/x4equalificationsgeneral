#
# Table structure for table 'tx_x4equalificationgeneral_list'
#
CREATE TABLE `tx_x4equalification_list` (
  `uid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `crdate` int(11) NOT NULL default '0',
  `cruser_id` int(11) NOT NULL default '0',
  `deleted` tinyint(4) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  `organizer` blob NOT NULL,
  `title` tinytext NOT NULL,
  `type` int(11) NOT NULL default '0',
  `finished` varchar(255) NOT NULL default '0',
  `abstract` text character set latin1 collate latin1_german2_ci NOT NULL,
  `pictures` blob NOT NULL,
  `abortet` varchar(255) NOT NULL default '0',
  `student` blob NOT NULL,
  PRIMARY KEY  (`uid`),
  KEY `parent` (`pid`)
);



#
# Table structure for table 'tx_x4equalificationgeneral_student'
#
CREATE TABLE tx_x4equalificationgeneral_student (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	firstname tinytext NOT NULL,
	lastname tinytext NOT NULL,
	address text NOT NULL,
	phone tinytext NOT NULL,
	email tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_x4equalificationgeneral_cat (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	`name` varchar(255) NOT NULL,
	`plural` varchar(255) NOT NULL,

	PRIMARY KEY (uid),
);