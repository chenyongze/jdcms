/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

ALTER TABLE `jd_ad` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_ad` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_admin` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_admin` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_album` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_album` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_album_cate` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_album_cate` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_album_items` ADD  `is_cover` int(1) NOT NULL DEFAULT '0';
ALTER TABLE `jd_items` ADD `seller_id` int(11) DEFAULT '0';
ALTER TABLE `jd_items` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_cate` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_cate` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_comments` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_comments` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_site` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_site` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_tags` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_items_tags` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_link` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_link` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_user` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_user` ADD `remark2` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_user_openid` ADD `remark1` VARCHAR(255) DEFAULT '';
ALTER TABLE `jd_user_openid` ADD `remark2` VARCHAR(255) DEFAULT '';

/*Table structure for table `jd_article` */

CREATE TABLE IF NOT EXISTS `jd_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` tinyint(4) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `abst` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `add_time` int(50) NOT NULL DEFAULT '0',
  `ord` int(10) NOT NULL DEFAULT '0',
  `is_best` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

/*Data for the table `jd_article` */

/*Table structure for table `jd_article_cate` */

CREATE TABLE IF NOT EXISTS `jd_article_cate` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `article_nums` int(10) unsigned NOT NULL,
  `ord` int(10) NOT NULL DEFAULT '0',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

/*Data for the table `jd_article_cate` */

/*Table structure for table `jd_shop` */

CREATE TABLE IF NOT EXISTS `jd_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT '0',
  `seller_id` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `img` varchar(255) DEFAULT '',
  `url` varchar(300) DEFAULT '',
  `ord` int(10) DEFAULT '0',
  `is_del` int(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  `seo_title` varchar(100) DEFAULT '',
  `seo_keys` varchar(100) DEFAULT '',
  `seo_desc` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `jd_shop` */

/*Table structure for table `jd_user_follow` */

/*Table structure for table `jd_user_openid` */

CREATE TABLE IF NOT EXISTS `jd_user_openid` (
  `type` varchar(11) DEFAULT NULL,
  `uid` varchar(15) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `info` text,
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `jd_user_openid` */