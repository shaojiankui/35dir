
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;




CREATE TABLE IF NOT EXISTS `dir_advers` (
  `adver_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `adver_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `adver_name` varchar(50) NOT NULL DEFAULT '',
  `adver_url` varchar(255) NOT NULL,
  `adver_code` text NOT NULL,
  `adver_etips` varchar(50) NOT NULL DEFAULT '',
  `adver_days` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `adver_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`adver_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_articles` (
  `art_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `art_title` varchar(100) NOT NULL,
  `art_tags` varchar(50) NOT NULL,
  `copy_from` varchar(50) NOT NULL,
  `copy_url` varchar(200) NOT NULL,
  `art_intro` varchar(200) NOT NULL,
  `art_content` text NOT NULL,
  `art_views` int(10) unsigned NOT NULL DEFAULT '0',
  `art_ispay` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `art_istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `art_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `art_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `art_ctime` int(10) unsigned NOT NULL DEFAULT '0',
  `art_utime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`art_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_categories` (
  `cate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_mod` enum('webdir','article') NOT NULL DEFAULT 'webdir',
  `cate_name` varchar(50) NOT NULL DEFAULT '',
  `cate_dir` varchar(50) NOT NULL DEFAULT '',
  `cate_url` varchar(255) NOT NULL,
  `cate_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cate_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_keywords` varchar(100) NOT NULL,
  `cate_description` varchar(255) NOT NULL DEFAULT '',
  `cate_arrparentid` varchar(255) NOT NULL,
  `cate_arrchildid` text NOT NULL,
  `cate_childcount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cate_postcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_favorites` (
  `fav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `web_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fav_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fav_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_feedbacks` (
  `fb_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fb_nick` varchar(50) NOT NULL,
  `fb_email` varchar(50) NOT NULL DEFAULT '',
  `fb_content` text NOT NULL,
  `fb_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fb_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_labels` (
  `label_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `label_name` varchar(50) NOT NULL DEFAULT '',
  `label_intro` varchar(255) NOT NULL DEFAULT '',
  `label_content` text NOT NULL,
  PRIMARY KEY (`label_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_links` (
  `link_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `link_name` varchar(50) NOT NULL DEFAULT '',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_logo` varchar(255) NOT NULL DEFAULT '',
  `link_hide` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_options` (
  `option_name` varchar(30) NOT NULL DEFAULT '',
  `option_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_pages` (
  `page_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `page_name` varchar(50) NOT NULL DEFAULT '',
  `page_intro` varchar(255) NOT NULL DEFAULT '',
  `page_content` text NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_type` enum('admin','member','qqlogin','recruit','vip') NOT NULL default 'member',
  `user_email` varchar(50) NOT NULL,
  `user_pass` char(32) NOT NULL,
  `open_id` char(32) NOT NULL,
  `nick_name` varchar(20) NOT NULL,
  `user_qq` varchar(20) NOT NULL,
  `user_score` smallint(5) unsigned NOT NULL default '0',
  `verify_code` varchar(32) NOT NULL,
  `user_status` tinyint(1) unsigned NOT NULL default '0',
  `join_time` int(10) unsigned NOT NULL default '0',
  `login_time` int(10) unsigned NOT NULL default '0',
  `login_ip` int(10) unsigned NOT NULL default '0',
  `login_count` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_webdata` (
  `web_id` int(10) unsigned NOT NULL,
  `web_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `web_grank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `web_brank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `web_srank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `web_arank` int(10) unsigned NOT NULL DEFAULT '0',
  `web_instat` int(10) unsigned NOT NULL DEFAULT '0',
  `web_outstat` int(10) unsigned NOT NULL DEFAULT '0',
  `web_voter` int(10) unsigned NOT NULL default '0',
  `web_score` int(10) unsigned NOT NULL default '0',
  `web_fnum` int(10) unsigned NOT NULL DEFAULT '0',
  `web_views` int(10) unsigned NOT NULL DEFAULT '0',
  `web_errors` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `web_itime` int(10) unsigned NOT NULL DEFAULT '0',
  `web_otime` int(10) unsigned NOT NULL DEFAULT '0',
  `web_utime` int(10) unsigned NOT NULL DEFAULT '0',
  `rate_score` int(10) unsigned NOT NULL DEFAULT '0',
  `rate_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`web_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_weblinks` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `web_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deal_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_name` varchar(20) NOT NULL,
  `link_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_pos` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_price` smallint(3) unsigned NOT NULL DEFAULT '0',
  `link_if1` int(10) unsigned NOT NULL,
  `link_if2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_if3` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `link_if4` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `link_intro` varchar(200) NOT NULL,
  `link_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `link_views` int(10) unsigned NOT NULL DEFAULT '0',
  `link_istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_hide` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_websites` (
  `web_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `web_name` varchar(100) NOT NULL DEFAULT '',
  `web_url` varchar(255) NOT NULL DEFAULT '',
  `web_tags` varchar(100) NOT NULL,
  `web_pic` varchar(100) NOT NULL,
  `web_intro` text NOT NULL,
  `web_ispay` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_islink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_ctime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`web_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `dir_options` (`option_name`, `option_value`) VALUES
('site_name', '网站分类目录'),
('site_title', '网站分类目录'),
('site_url', '/'),
('site_root', '/'),
('admin_email', ''),
('site_keywords', '分类目录,优秀网站分类目录'),
('site_description', '提高网站的知名度，為您的网站提高人氣，期待您的加盟~'),
('site_copyright', 'Copyright &copy; 2008-2011 35dir.com All Rights Reserved'),
('register_email_verify', 'no'),
('is_enabled_register', 'yes'),
('site_notice', ''),
('filter_words', ''),
('search_words', ''),
('upload_dir', 'uploads'),
('article_link_num', '3'),
('data_update_cycle', '3'),
('check_link_url', 'dir.dir.com'),
('check_link_name', ''),
('is_enabled_linkcheck', 'yes'),
('submit_close_reason', ''),
('is_enabled_submit', 'yes'),
('is_enabled_gzip', 'yes'),
('link_struct', '0'),
('qq_appkey', 'app_key'),
('qq_appid', 'app_id'),
('is_enabled_connect', 'yes'),
('smtp_host', 'smtp.qq.com'),
('smtp_port', '25'),
('smtp_auth', 'yes'),
('smtp_user', 'test'),
('smtp_pass', 'test');