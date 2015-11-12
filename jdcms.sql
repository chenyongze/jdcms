# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 115.28.18.28 (MySQL 5.5.34-log)
# Database: jdcms
# Generation Time: 2015-11-12 07:26:54 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table jd_ad
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_ad`;

CREATE TABLE `jd_ad` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `size` varchar(255) NOT NULL,
  `code` text NOT NULL,
  `start_time` int(10) NOT NULL,
  `end_time` int(10) NOT NULL,
  `add_time` int(10) NOT NULL,
  `ord` int(10) NOT NULL,
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_ad` WRITE;
/*!40000 ALTER TABLE `jd_ad` DISABLE KEYS */;

INSERT INTO `jd_ad` (`id`, `name`, `size`, `code`, `start_time`, `end_time`, `add_time`, `ord`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,'首页顶部(default) ','970x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(2,'首页底部(default) ','970x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(3,'列表页顶部(default)','970x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(4,'列表页底部(default)','970x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(5,'专辑页顶部(default)','650x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop650.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(6,'专辑页底部(default)','650x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop650.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(7,'单品页(default)','650x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop650.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(8,'单品页(default)','250x250','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop250.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(9,'首页顶部(meilishuo) ','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(10,'首页底部(meilishuo) ','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(11,'列表页顶部(meilishuo)','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(12,'列表页底部(meilishuo)','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(13,'专辑页顶部(meilishuo)','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(14,'专辑页底部(meilishuo)','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(15,'单品页(meilishuo)','950x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(16,'单品页(meilishuo)','250x250','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop250.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(17,'首页顶部(mogujie) ','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(18,'首页底部(mogujie) ','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(19,'列表页顶部(mogujie)','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(20,'列表页底部(mogujie)','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(21,'专辑页顶部(mogujie)','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(22,'专辑页顶部(mogujie)','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(23,'单品页(mogujie)','960x90','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(24,'单品页(mogujie)','250x250','<a href=\"http://www.jdcms.com/\" target=\"_blank\"><img border=\"0\" src=\"/Public/statics/images/adtop250.jpg\" alt=\"简单CMS，购物分享平台\" title=\"简单CMS，购物分享平台\"/></a>',1351053540,1382589540,1351123200,0,0,'',''),
	(25,'登录页(mogujie)','290x45','<a id=\"ad_290x46\" target=\"_blank\" href=\"#\"><img src=\"/Public/statics/images/li_ad.jpg\" width=\"290\" height=\"45\"></a>',1351053540,1382589540,1351123200,0,0,'','');

/*!40000 ALTER TABLE `jd_ad` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_admin`;

CREATE TABLE `jd_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `add_time` int(10) DEFAULT '0',
  `last_time` int(10) DEFAULT '0',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_admin` WRITE;
/*!40000 ALTER TABLE `jd_admin` DISABLE KEYS */;

INSERT INTO `jd_admin` (`id`, `user_name`, `password`, `add_time`, `last_time`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,'chenyongze','f4d1ded501fd849d49554272a4b8608d',1414798277,1439024872,0,'',''),
	(2,'ygm','c33367701511b4f6020ec61ded352059',1414798472,1414811995,0,'','');

/*!40000 ALTER TABLE `jd_admin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_album
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_album`;

CREATE TABLE `jd_album` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '默认专辑',
  `uid` int(10) NOT NULL DEFAULT '0',
  `info` varchar(255) DEFAULT '',
  `cid` int(10) NOT NULL DEFAULT '0',
  `add_time` int(10) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_album` WRITE;
/*!40000 ALTER TABLE `jd_album` DISABLE KEYS */;

INSERT INTO `jd_album` (`id`, `title`, `uid`, `info`, `cid`, `add_time`, `status`, `is_del`, `remark1`, `remark2`)
VALUES
	(176,'这是我分享的一个专辑美容',201,'每个人都需要给自己美容，对自己好点',1,1414800474,1,0,'','');

/*!40000 ALTER TABLE `jd_album` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_album_cate
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_album_cate`;

CREATE TABLE `jd_album_cate` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '我的专辑',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `ord` int(10) NOT NULL DEFAULT '0',
  `seo_title` varchar(100) DEFAULT '',
  `seo_keys` varchar(100) DEFAULT '',
  `seo_desc` varchar(225) DEFAULT '',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_album_cate` WRITE;
/*!40000 ALTER TABLE `jd_album_cate` DISABLE KEYS */;

INSERT INTO `jd_album_cate` (`id`, `title`, `add_time`, `ord`, `seo_title`, `seo_keys`, `seo_desc`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,'美容',1351123200,0,'','','',0,'',''),
	(2,'购物',1351123200,0,'','','',0,'',''),
	(3,'生活',1351123200,0,'','','',0,'',''),
	(4,'家居',1351123200,0,'','','',0,'',''),
	(5,'其它',1351123200,0,'','','',0,'','');

/*!40000 ALTER TABLE `jd_album_cate` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_album_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_album_items`;

CREATE TABLE `jd_album_items` (
  `items_id` int(10) NOT NULL DEFAULT '0',
  `pid` int(10) NOT NULL DEFAULT '0',
  `add_time` int(10) DEFAULT '0',
  `info` varchar(50) DEFAULT '',
  `is_cover` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_album_items` WRITE;
/*!40000 ALTER TABLE `jd_album_items` DISABLE KEYS */;

INSERT INTO `jd_album_items` (`items_id`, `pid`, `add_time`, `info`, `is_cover`)
VALUES
	(1,176,1414800744,'',0);

/*!40000 ALTER TABLE `jd_album_items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_article`;

CREATE TABLE `jd_article` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jd_article_cate
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_article_cate`;

CREATE TABLE `jd_article_cate` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jd_article_collect
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_article_collect`;

CREATE TABLE `jd_article_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `last_time` int(10) NOT NULL DEFAULT '0',
  `char_code` varchar(100) NOT NULL DEFAULT '',
  `s_url` char(10) NOT NULL DEFAULT '',
  `ord` int(10) NOT NULL DEFAULT '0',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `match_urls` varchar(255) NOT NULL DEFAULT '',
  `start_match_nums` int(10) NOT NULL DEFAULT '0',
  `end_match_nums` int(10) NOT NULL DEFAULT '0',
  `inc_nums` int(10) NOT NULL DEFAULT '0',
  `urls` text,
  `start_html` varchar(255) NOT NULL DEFAULT '',
  `end_html` varchar(255) NOT NULL DEFAULT '',
  `include` varchar(255) NOT NULL DEFAULT '',
  `no_include` varchar(255) NOT NULL DEFAULT '',
  `page_rule` varchar(255) NOT NULL DEFAULT '',
  `s_page` char(10) NOT NULL DEFAULT '',
  `title_rule` varchar(255) NOT NULL DEFAULT '',
  `title_filter` varchar(255) NOT NULL DEFAULT '',
  `content_rule` varchar(255) NOT NULL DEFAULT '',
  `content_filter` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jd_article_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_article_tags`;

CREATE TABLE `jd_article_tags` (
  `article_id` int(10) NOT NULL,
  `tag_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jd_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items`;

CREATE TABLE `jd_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  `img` varchar(255) DEFAULT '',
  `info` varchar(280) NOT NULL DEFAULT '',
  `url` varchar(300) DEFAULT '',
  `item_key` varchar(50) DEFAULT '',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `sid` smallint(4) NOT NULL DEFAULT '0',
  `hits` int(10) NOT NULL DEFAULT '0',
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '喜欢数',
  `comments` int(10) NOT NULL DEFAULT '0' COMMENT '评论数',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `last_time` int(10) NOT NULL DEFAULT '0',
  `seo_title` varchar(100) DEFAULT '',
  `seo_keys` varchar(100) DEFAULT '',
  `seo_desc` varchar(255) DEFAULT '',
  `ord` int(10) DEFAULT '0',
  `is_focus` tinyint(1) DEFAULT '0' COMMENT '首页焦点图',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:未审核 1:审核 2:下架',
  `is_index` tinyint(1) NOT NULL DEFAULT '0',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `is_index` (`is_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items` WRITE;
/*!40000 ALTER TABLE `jd_items` DISABLE KEYS */;

INSERT INTO `jd_items` (`id`, `seller_id`, `cid`, `title`, `price`, `img`, `info`, `url`, `item_key`, `uid`, `sid`, `hits`, `likes`, `comments`, `add_time`, `last_time`, `seo_title`, `seo_keys`, `seo_desc`, `ord`, `is_focus`, `status`, `is_index`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,0,2,'yzetest',1.00,'/Uploads/LocalItems/20141101/545421d8e6535.png','','http://item.taobao.com/item.htm?spm=a217o.1297604.1998175424.4.T5O17l&id=41063806660','',154,1,193,11,1,1414799833,0,'','','',0,1,1,1,0,'/Uploads/LocalItems/focus/54542219b2fba.png',''),
	(2,0,3,'切切切切切',1.00,'/Uploads/LocalItems/20141101/545424ecccba1.png','','http://detail.tmall.com/item.htm?spm=a220o.1000855.1998025129.2.FG7iPY&id=19623817893&abbucket=_AB-M32_B2&rn=7dae94df6e45f9cdc94f669c5adcaddc&acm=03054.1003.1.121374&uuid=klCrAi2D_mS3ZDPSME0MCAW5X5ByYGQ7t&abtest=_AB-LR32-PV32_889&scm=1003.1.03054.ITEM_19623817893_121374&pos=2','',201,4,68,0,0,1414800621,0,'','','',0,0,1,1,0,'/Uploads/LocalItems/focus/5475fdcf16065.png','');

/*!40000 ALTER TABLE `jd_items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_items_cate
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items_cate`;

CREATE TABLE `jd_items_cate` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `img` varchar(255) DEFAULT '',
  `pid` smallint(4) NOT NULL DEFAULT '0',
  `item_nums` int(11) NOT NULL DEFAULT '0',
  `ord` smallint(4) NOT NULL DEFAULT '0',
  `seo_title` varchar(100) NOT NULL DEFAULT '',
  `seo_keys` varchar(100) NOT NULL DEFAULT '',
  `seo_desc` varchar(255) NOT NULL DEFAULT '',
  `is_index` tinyint(1) NOT NULL DEFAULT '0',
  `is_del` tinyint(1) DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items_cate` WRITE;
/*!40000 ALTER TABLE `jd_items_cate` DISABLE KEYS */;

INSERT INTO `jd_items_cate` (`id`, `name`, `img`, `pid`, `item_nums`, `ord`, `seo_title`, `seo_keys`, `seo_desc`, `is_index`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,'衣服','/Uploads/Cate/yifu.png',0,0,6,'','','',1,0,'',''),
	(2,'鞋子','/Uploads/Cate/xiezi.png',0,1,5,'','','',1,0,'',''),
	(3,'包包','/Uploads/Cate/baobao.png',0,1,4,'','','',1,0,'',''),
	(4,'配饰','/Uploads/Cate/peishi.png',0,0,3,'','','',1,0,'',''),
	(5,'美妆','/Uploads/Cate/meizhuang.png',0,0,2,'','','',1,0,'',''),
	(6,'家居','/Uploads/Cate/jiaju.png',0,0,1,'','','',1,0,'',''),
	(7,'上衣','/Uploads/Cate/yf_shangyi.png',1,0,5,'','','',1,0,'',''),
	(8,'裙子','/Uploads/Cate/yf_qunzi.png',1,0,4,'','','',1,0,'',''),
	(9,'裤子','/Uploads/Cate/yf_kuzi.png',1,0,3,'','','',1,0,'',''),
	(10,'内衣','/Uploads/Cate/yf_neiyi.png',1,0,2,'','','',1,0,'',''),
	(11,'材质','/Uploads/Cate/yf_caizhi.png',1,0,1,'','','',1,0,'',''),
	(12,'款式','/Uploads/Cate/xz_kuanshi.png',2,0,4,'','','',1,0,'',''),
	(13,'流行元素','/Uploads/Cate/xz_liuxing.png',2,0,3,'','','',1,0,'',''),
	(14,'风格','/Uploads/Cate/xz_fengge.png',2,0,2,'','','',1,0,'',''),
	(15,'材质','/Uploads/Cate/xz_caizhi.png',2,0,1,'','','',1,0,'',''),
	(16,'款式','/Uploads/Cate/bb_kuanshi.png',3,0,4,'','','',1,0,'',''),
	(17,'流行元素','/Uploads/Cate/bb_liuxing.png',3,0,3,'','','',1,0,'',''),
	(18,'风格','/Uploads/Cate/bb_fengge.png',3,0,2,'','','',1,0,'',''),
	(19,'材质','/Uploads/Cate/bb_caizhi.png',3,0,1,'','','',1,0,'',''),
	(20,'款式','/Uploads/Cate/px_kuanshi.png',4,0,4,'','','',1,0,'',''),
	(21,'流行元素','/Uploads/Cate/px_liuxing.png',4,0,3,'','','',1,0,'',''),
	(22,'风格','/Uploads/Cate/px_fengge.png',4,0,2,'','','',1,0,'',''),
	(23,'材质','/Uploads/Cate/px_caizhi.png',4,0,1,'','','',1,0,'',''),
	(24,'护肤','/Uploads/Cate/mz_hufu.png',5,0,4,'','','',1,0,'',''),
	(25,'彩妆','/Uploads/Cate/mz_caizhuang.png',5,0,3,'','','',1,0,'',''),
	(26,'工具','/Uploads/Cate/mz_gongju.png',5,0,2,'','','',1,0,'',''),
	(27,'功效','/Uploads/Cate/mz_gongxiao.png',5,0,1,'','','',1,0,'',''),
	(28,'人气推荐','/Uploads/Cate/jj_tuijian.png',6,0,4,'','','',1,0,'',''),
	(29,'温馨小家','/Uploads/Cate/jj_xiaojia.png',6,0,3,'','','',1,0,'',''),
	(30,'个性厨卫','/Uploads/Cate/jj_chuwei.png',6,0,2,'','','',1,0,'',''),
	(31,'数码相关','/Uploads/Cate/jj_shuma.png',6,0,1,'','','',1,0,'','');

/*!40000 ALTER TABLE `jd_items_cate` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_items_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items_comments`;

CREATE TABLE `jd_items_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `uname` varchar(50) DEFAULT '',
  `info` varchar(280) NOT NULL DEFAULT '',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '' COMMENT '采集商品评论的评论id',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items_comments` WRITE;
/*!40000 ALTER TABLE `jd_items_comments` DISABLE KEYS */;

INSERT INTO `jd_items_comments` (`id`, `items_id`, `uid`, `uname`, `info`, `add_time`, `status`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,1,201,'yongze','这功能不错哦',1414804560,1,0,'','');

/*!40000 ALTER TABLE `jd_items_comments` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_items_likes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items_likes`;

CREATE TABLE `jd_items_likes` (
  `items_id` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items_likes` WRITE;
/*!40000 ALTER TABLE `jd_items_likes` DISABLE KEYS */;

INSERT INTO `jd_items_likes` (`items_id`, `uid`)
VALUES
	(1,201);

/*!40000 ALTER TABLE `jd_items_likes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_items_site
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items_site`;

CREATE TABLE `jd_items_site` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `site_domain` varchar(255) NOT NULL DEFAULT '',
  `site_logo` varchar(255) NOT NULL DEFAULT '',
  `id_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items_site` WRITE;
/*!40000 ALTER TABLE `jd_items_site` DISABLE KEYS */;

INSERT INTO `jd_items_site` (`id`, `name`, `alias`, `site_domain`, `site_logo`, `id_del`, `remark1`, `remark2`)
VALUES
	(1,'淘宝','taobao','taobao.com','taobao.jpg',0,'',''),
	(2,'天猫','tmall','tmall.com','tmall.jpg',0,'',''),
	(3,'拍拍','paipai','paipai.com','paipai.jpg',0,'',''),
	(4,'当当','dangdang','dangdang.com','dangdang.jpg',0,'',''),
	(5,'凡客','vancl','vancl.com','vancl.jpg',0,'',''),
	(6,'京东','360buy','360buy.com','360buy.jpg',0,'',''),
	(7,'草莓派','caomeipai','caomeipai.com','caomeipai.jpg',0,'',''),
	(8,'麦包包','mbaobao','mbaobao.com','mbaobao.jpg',0,'',''),
	(9,'娜拉','nala','nala.com.cn','nala.jpg',0,'','');

/*!40000 ALTER TABLE `jd_items_site` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_items_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items_tags`;

CREATE TABLE `jd_items_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  `item_nums` int(11) NOT NULL DEFAULT '0',
  `pid` smallint(4) DEFAULT '0',
  `sid` smallint(4) DEFAULT '0',
  `seo_title` varchar(100) NOT NULL DEFAULT '',
  `seo_keys` varchar(100) NOT NULL DEFAULT '',
  `seo_desc` varchar(255) NOT NULL DEFAULT '',
  `is_index` tinyint(1) NOT NULL DEFAULT '0',
  `ord` int(10) DEFAULT '0',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items_tags` WRITE;
/*!40000 ALTER TABLE `jd_items_tags` DISABLE KEYS */;

INSERT INTO `jd_items_tags` (`id`, `name`, `item_nums`, `pid`, `sid`, `seo_title`, `seo_keys`, `seo_desc`, `is_index`, `ord`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,'T恤',0,1,7,'','','',1,100,0,'',''),
	(2,'衬衫',0,1,7,'','','',1,100,0,'',''),
	(3,'西装',0,1,7,'','','',0,100,0,'',''),
	(4,'雪纺衫',0,1,7,'','','',1,100,0,'',''),
	(5,'背心',0,1,7,'','','',0,100,0,'',''),
	(6,'吊带衫',0,1,7,'','','',0,100,0,'',''),
	(7,'马夹',0,1,7,'','','',0,100,0,'',''),
	(8,'风衣',0,1,7,'','','',1,100,0,'',''),
	(9,'毛衣',0,1,7,'','','',0,0,0,'',''),
	(10,'卫衣',0,1,7,'','','',0,100,0,'',''),
	(11,'羽绒服',0,1,7,'','','',0,0,0,'',''),
	(12,'呢大衣',0,1,7,'','','',0,0,0,'',''),
	(13,'棉衣',0,1,7,'','','',0,0,0,'',''),
	(14,'外套',0,1,7,'','','',0,0,0,'',''),
	(15,'羊绒衫',0,1,7,'','','',0,0,0,'',''),
	(16,'皮衣',0,1,7,'','','',0,0,0,'',''),
	(17,'夹克',0,1,7,'','','',1,100,0,'',''),
	(18,'连衣裙',0,1,8,'','','',1,0,0,'',''),
	(19,'半身裙',0,1,8,'','','',0,0,0,'',''),
	(20,'长裙',0,1,8,'','','',0,0,0,'',''),
	(21,'短袖裙',0,1,8,'','','',0,0,0,'',''),
	(22,'宽松裙',0,1,8,'','','',0,0,0,'',''),
	(23,'修身裙',0,1,8,'','','',0,0,0,'',''),
	(24,'碎花裙',0,1,8,'','','',0,0,0,'',''),
	(25,'高腰裙',0,1,8,'','','',0,0,0,'',''),
	(26,'吊带裙',0,1,8,'','','',0,0,0,'',''),
	(27,'公主裙',0,1,8,'','','',0,0,0,'',''),
	(28,'背带裙',0,1,8,'','','',0,0,0,'',''),
	(29,'A字裙',0,1,8,'','','',0,0,0,'',''),
	(30,'牛仔裙',0,1,8,'','','',0,0,0,'',''),
	(31,'牛仔裤',0,1,9,'','','',0,100,0,'',''),
	(32,'休闲裤',0,1,9,'','','',0,100,0,'',''),
	(33,'铅笔裤',0,1,9,'','','',0,100,0,'',''),
	(34,'打底裤',0,1,9,'','','',0,100,0,'',''),
	(35,'正装裤',0,1,9,'','','',0,100,0,'',''),
	(36,'棉裤',0,1,9,'','','',0,0,0,'',''),
	(37,'羽绒裤',0,1,9,'','','',0,0,0,'',''),
	(38,'西裤',0,1,9,'','','',0,100,0,'',''),
	(39,'皮裤',0,1,9,'','','',1,100,0,'',''),
	(40,'沙滩裤',0,1,9,'','','',0,100,0,'',''),
	(41,'滑板裤',0,1,9,'','','',0,0,0,'',''),
	(42,'背带裤',0,1,9,'','','',0,100,0,'',''),
	(43,'文胸',0,1,10,'','','',0,0,0,'',''),
	(44,'内裤',0,1,10,'','','',0,0,0,'',''),
	(45,'睡衣',0,1,10,'','','',1,0,0,'',''),
	(46,'家居服',0,1,10,'','','',0,0,0,'',''),
	(47,'丝袜',0,1,10,'','','',0,0,0,'',''),
	(48,'塑身衣',0,1,10,'','','',0,0,0,'',''),
	(49,'安全裤',0,1,10,'','','',0,0,0,'',''),
	(50,'雪纺',0,1,11,'','','',0,100,0,'',''),
	(51,'棉麻',0,1,11,'','','',0,100,0,'',''),
	(52,'真丝',0,1,11,'','','',0,100,0,'',''),
	(53,'牛仔',0,1,11,'','','',0,100,0,'',''),
	(54,'针织',0,1,11,'','','',0,100,0,'',''),
	(55,'莫代尔',0,1,11,'','','',0,100,0,'',''),
	(56,'涤纶',0,1,11,'','','',0,100,0,'',''),
	(57,'羽绒',0,1,11,'','','',0,0,0,'',''),
	(58,'皮质',0,1,11,'','','',0,100,0,'',''),
	(59,'羊绒',0,1,11,'','','',0,100,0,'',''),
	(60,'莱卡',0,1,11,'','','',0,100,0,'',''),
	(61,'花朵鞋',0,2,12,'','','',0,0,0,'',''),
	(62,'豹纹鞋',0,2,12,'','','',0,0,0,'',''),
	(63,'铆钉鞋',0,2,12,'','','',0,0,0,'',''),
	(64,'靴子',0,2,12,'','','',1,0,0,'',''),
	(65,'高帮鞋',0,2,12,'','','',0,0,0,'',''),
	(66,'凉拖',0,2,12,'','','',0,0,0,'',''),
	(67,'鱼嘴鞋',0,2,12,'','','',1,0,0,'',''),
	(68,'洞洞鞋',0,2,12,'','','',0,0,0,'',''),
	(69,'人字拖',0,2,12,'','','',0,0,0,'',''),
	(70,'板鞋',0,2,12,'','','',1,0,0,'',''),
	(71,'雨鞋',0,2,12,'','','',0,0,0,'',''),
	(72,'运动鞋',0,2,12,'','','',1,0,0,'',''),
	(73,'增高鞋',0,2,12,'','','',0,0,0,'',''),
	(74,'厚底鞋',0,2,12,'','','',1,0,0,'',''),
	(75,'单鞋',0,2,12,'','','',1,0,0,'',''),
	(76,'凉鞋',0,2,12,'','','',0,0,0,'',''),
	(77,'帆布鞋',0,2,12,'','','',0,0,0,'',''),
	(78,'尖头鞋',0,2,12,'','','',0,0,0,'',''),
	(79,'娃娃鞋',0,2,12,'','','',1,0,0,'',''),
	(80,'松糕鞋',0,2,12,'','','',0,0,0,'',''),
	(81,'软底鞋',0,2,12,'','','',1,0,0,'',''),
	(82,'套筒',0,2,13,'','','',0,0,0,'',''),
	(83,'不系带',0,2,13,'','','',1,0,0,'',''),
	(84,'系带',0,2,13,'','','',0,0,0,'',''),
	(85,'水钻',0,2,13,'','','',0,0,0,'',''),
	(86,'交叉绑带',0,2,13,'','','',0,0,0,'',''),
	(87,'松糕跟',0,2,13,'','','',0,0,0,'',''),
	(88,'蝴蝶结',0,2,13,'','','',0,0,0,'',''),
	(89,'防水台',0,2,13,'','','',0,0,0,'',''),
	(90,'坡跟',0,2,13,'','','',0,0,0,'',''),
	(91,'性感',0,2,14,'','','',1,0,0,'',''),
	(92,'欧美',0,2,14,'','','',0,0,0,'',''),
	(93,'韩版',0,2,14,'','','',1,0,0,'',''),
	(94,'英伦',0,2,14,'','','',0,0,0,'',''),
	(95,'甜美',0,2,14,'','','',0,0,0,'',''),
	(96,'休闲',0,2,14,'','','',0,0,0,'',''),
	(97,'OL',0,2,14,'','','',0,0,0,'',''),
	(98,'日系',0,2,14,'','','',1,0,0,'',''),
	(99,'罗马',0,2,14,'','','',0,0,0,'',''),
	(100,'中性',0,2,14,'','','',0,0,0,'',''),
	(101,'朋克',0,2,14,'','','',0,0,0,'',''),
	(102,'复古',0,2,14,'','','',1,0,0,'',''),
	(103,'民族',0,2,14,'','','',0,0,0,'',''),
	(104,'简约',0,2,14,'','','',0,0,0,'',''),
	(105,'优雅',0,2,14,'','','',0,0,0,'',''),
	(106,'千层底',0,2,15,'','','',0,0,0,'',''),
	(107,'泡沫',0,2,15,'','','',0,0,0,'',''),
	(108,'复合底',0,2,15,'','','',0,0,0,'',''),
	(109,'橡胶',0,2,15,'','','',0,0,0,'',''),
	(110,'牛筋',0,2,15,'','','',0,0,0,'',''),
	(111,'亮片布',0,2,15,'','','',0,0,0,'',''),
	(112,'混合材质',0,2,15,'','','',0,0,0,'',''),
	(113,'马毛',0,2,15,'','','',0,0,0,'',''),
	(114,'全棉布',0,2,15,'','','',0,0,0,'',''),
	(115,'网布',0,2,15,'','','',0,0,0,'',''),
	(116,'牛皮',0,2,15,'','','',1,0,0,'',''),
	(117,'羊皮',0,2,15,'','','',0,0,0,'',''),
	(118,'塑胶',0,2,15,'','','',0,0,0,'',''),
	(119,'绸缎面',0,2,15,'','','',0,0,0,'',''),
	(120,'牛绒',0,2,15,'','','',0,0,0,'',''),
	(121,'布面',0,2,15,'','','',1,0,0,'',''),
	(122,'牛仔布',0,2,15,'','','',0,0,0,'',''),
	(123,'人造皮革',0,2,15,'','','',0,0,0,'',''),
	(124,'绒面',0,2,15,'','','',0,0,0,'',''),
	(125,'猪皮',0,2,15,'','','',0,0,0,'',''),
	(126,'旅行箱',0,3,16,'','','',0,0,0,'',''),
	(127,'卡包卡套',0,3,16,'','','',0,0,0,'',''),
	(128,'手拿包',0,3,16,'','','',1,0,0,'',''),
	(129,'腿包',0,3,16,'','','',0,0,0,'',''),
	(130,'腰包',0,3,16,'','','',0,0,0,'',''),
	(131,'胸包',0,3,16,'','','',0,0,0,'',''),
	(132,'斜挎包',0,3,16,'','','',1,0,0,'',''),
	(133,'手提包',0,3,16,'','','',0,0,0,'',''),
	(134,'双肩包',0,3,16,'','','',1,0,0,'',''),
	(135,'钱包',0,3,16,'','','',0,0,0,'',''),
	(136,'单肩包',0,3,16,'','','',1,0,0,'',''),
	(137,'镂空',0,3,17,'','','',0,0,0,'',''),
	(138,'草编',0,3,17,'','','',0,0,0,'',''),
	(139,'透明',0,3,17,'','','',1,0,0,'',''),
	(140,'撞色',0,3,17,'','','',1,0,0,'',''),
	(141,'链条',0,3,17,'','','',0,0,0,'',''),
	(142,'格纹',0,3,17,'','','',0,0,0,'',''),
	(143,'车缝线',0,3,17,'','','',0,0,0,'',''),
	(144,'皮带装饰',0,3,17,'','','',0,0,0,'',''),
	(145,'糖果色',0,3,17,'','','',1,0,0,'',''),
	(146,'流苏',0,3,17,'','','',0,0,0,'',''),
	(147,'休闲',0,3,18,'','','',0,0,0,'',''),
	(148,'运动',0,3,18,'','','',0,0,0,'',''),
	(149,'商务',0,3,18,'','','',1,0,0,'',''),
	(150,'复古风',0,3,18,'','','',1,0,0,'',''),
	(151,'民族风',0,3,18,'','','',0,0,0,'',''),
	(152,'清新',0,3,18,'','','',0,0,0,'',''),
	(153,'职业OL',0,3,18,'','','',1,0,0,'',''),
	(154,'淑女',0,3,18,'','','',0,0,0,'',''),
	(155,'甜美',0,3,18,'','','',0,0,0,'',''),
	(156,'横款',0,3,18,'','','',0,0,0,'',''),
	(157,'竖款',0,3,18,'','','',0,0,0,'',''),
	(158,'潮酷',0,3,18,'','','',0,0,0,'',''),
	(159,'可爱',0,3,18,'','','',0,0,0,'',''),
	(160,'卡通',0,3,18,'','','',0,0,0,'',''),
	(161,'日韩',0,3,18,'','','',0,0,0,'',''),
	(162,'欧美',0,3,18,'','','',1,0,0,'',''),
	(163,'牛皮',0,3,19,'','','',0,0,0,'',''),
	(164,'羊皮',0,3,19,'','','',0,0,0,'',''),
	(165,'尼龙',0,3,19,'','','',0,0,0,'',''),
	(166,'牛津布',0,3,19,'','','',0,0,0,'',''),
	(167,'涤纶',0,3,19,'','','',0,100,0,'',''),
	(168,'牛仔布',0,3,19,'','','',0,0,0,'',''),
	(169,'PVC',0,3,19,'','','',0,0,0,'',''),
	(170,'PU',0,3,19,'','','',0,0,0,'',''),
	(171,'帆布',0,3,19,'','','',0,0,0,'',''),
	(172,'项链',0,4,20,'','','',0,0,0,'',''),
	(173,'手链',0,4,20,'','','',1,0,0,'',''),
	(174,'墨镜',0,4,20,'','','',0,0,0,'',''),
	(175,'耳钉',0,4,20,'','','',0,0,0,'',''),
	(176,'发饰',0,4,20,'','','',0,0,0,'',''),
	(177,'戒指',0,4,20,'','','',1,0,0,'',''),
	(178,'手表',0,4,20,'','','',1,0,0,'',''),
	(179,'腰带',0,4,20,'','','',0,0,0,'',''),
	(180,'帽子',0,4,20,'','','',1,0,0,'',''),
	(181,'脚链',0,4,20,'','','',0,0,0,'',''),
	(182,'手镯',0,4,20,'','','',1,0,0,'',''),
	(183,'锁骨链',0,4,20,'','','',0,0,0,'',''),
	(184,'镜框',0,4,20,'','','',0,0,0,'',''),
	(185,'假领',0,4,20,'','','',0,0,0,'',''),
	(186,'草帽',0,4,20,'','','',0,0,0,'',''),
	(187,'丝巾',0,4,20,'','','',0,0,0,'',''),
	(188,'沙滩',0,4,21,'','','',0,0,0,'',''),
	(189,'蕾丝',0,4,21,'','','',1,0,0,'',''),
	(190,'花朵',0,4,21,'','','',0,0,0,'',''),
	(191,'防晒',0,4,21,'','','',0,0,0,'',''),
	(192,'宝石',0,4,21,'','','',0,0,0,'',''),
	(193,'羽毛',0,4,21,'','','',0,0,0,'',''),
	(194,'情侣',0,4,21,'','','',1,0,0,'',''),
	(195,'开运',0,4,21,'','','',0,0,0,'',''),
	(196,'定制',0,4,21,'','','',0,0,0,'',''),
	(197,'欧美',0,4,22,'','','',1,0,0,'',''),
	(198,'个性',0,4,22,'','','',0,0,0,'',''),
	(199,'甜美',0,4,22,'','','',0,0,0,'',''),
	(200,'朋克',0,4,22,'','','',1,0,0,'',''),
	(201,'日系',0,4,22,'','','',0,0,0,'',''),
	(202,'复古',0,4,22,'','','',0,0,0,'',''),
	(203,'韩版',0,4,22,'','','',0,0,0,'',''),
	(204,'森女',0,4,22,'','','',1,0,0,'',''),
	(205,'牛皮',0,4,23,'','','',0,0,0,'',''),
	(206,'925银',0,4,23,'','','',1,0,0,'',''),
	(207,'珐琅',0,4,23,'','','',0,0,0,'',''),
	(208,'玫瑰金',0,4,23,'','','',0,0,0,'',''),
	(209,'水晶',0,4,23,'','','',1,0,0,'',''),
	(210,'保湿',0,5,24,'','','',1,0,0,'',''),
	(211,'防晒',0,5,24,'','','',0,0,0,'',''),
	(212,'补水',0,5,24,'','','',1,0,0,'',''),
	(213,'洁面',0,5,24,'','','',0,0,0,'',''),
	(214,'面膜',0,5,24,'','','',1,0,0,'',''),
	(215,'喷雾',0,5,24,'','','',0,0,0,'',''),
	(216,'手霜',0,5,24,'','','',0,0,0,'',''),
	(217,'眼霜',0,5,24,'','','',1,0,0,'',''),
	(218,'眼影',0,5,25,'','','',0,0,0,'',''),
	(219,'BB霜',0,5,25,'','','',0,0,0,'',''),
	(220,'腮红',0,5,25,'','','',0,0,0,'',''),
	(221,'睫毛膏',0,5,25,'','','',0,0,0,'',''),
	(222,'唇膏',0,5,25,'','','',1,0,0,'',''),
	(223,'眼线',0,5,25,'','','',0,0,0,'',''),
	(224,'蜜粉',0,5,25,'','','',0,0,0,'',''),
	(225,'粉底',0,5,25,'','','',0,0,0,'',''),
	(226,'化妆包',0,5,26,'','','',1,0,0,'',''),
	(227,'化妆套刷',0,5,26,'','','',0,0,0,'',''),
	(228,'化妆刷',0,5,26,'','','',0,0,0,'',''),
	(229,'化妆棉',0,5,26,'','','',0,0,0,'',''),
	(230,'化妆海绵',0,5,26,'','','',0,0,0,'',''),
	(231,'纸膜',0,5,26,'','','',1,0,0,'',''),
	(232,'发卷',0,5,26,'','','',0,0,0,'',''),
	(233,'睫毛夹',0,5,26,'','','',1,0,0,'',''),
	(234,'女用剃毛刀',0,5,26,'','','',0,0,0,'',''),
	(235,'化妆箱',0,5,26,'','','',0,0,0,'',''),
	(236,'美白',0,5,27,'','','',1,0,0,'',''),
	(237,'卸妆油',0,5,27,'','','',0,0,0,'',''),
	(238,'控油',0,5,27,'','','',0,0,0,'',''),
	(239,'祛痘',0,5,27,'','','',1,0,0,'',''),
	(240,'紧肤',0,5,27,'','','',0,0,0,'',''),
	(241,'去黑头',0,5,27,'','','',0,0,0,'',''),
	(242,'抗敏',0,5,27,'','','',1,0,0,'',''),
	(243,'隔离',0,5,27,'','','',0,0,0,'',''),
	(244,'遮瑕',0,5,27,'','','',0,0,0,'',''),
	(245,'药妆',0,5,27,'','','',0,0,0,'',''),
	(246,'去角质',0,5,27,'','','',1,0,0,'',''),
	(247,'护肤',0,5,27,'','','',0,0,0,'',''),
	(248,'盆栽',0,6,28,'','','',0,0,0,'',''),
	(249,'懒人沙发',0,6,28,'','','',0,0,0,'',''),
	(250,'靠垫',0,6,28,'','','',1,0,0,'',''),
	(251,'韩国文具',0,6,28,'','','',0,0,0,'',''),
	(252,'风扇',0,6,28,'','','',0,0,0,'',''),
	(253,'雨伞',0,6,28,'','','',1,0,0,'',''),
	(254,'礼品',0,6,28,'','','',1,0,0,'',''),
	(255,'轻松熊',0,6,28,'','','',1,0,0,'',''),
	(256,'hellokitty',0,6,28,'','','',1,0,0,'',''),
	(257,'多啦A梦',0,6,28,'','','',1,0,0,'',''),
	(258,'创意小物',0,6,28,'','','',0,0,0,'',''),
	(259,'宜家',0,6,28,'','','',1,0,0,'',''),
	(260,'美克美家',0,6,28,'','','',0,0,0,'',''),
	(261,'ZAKKA',0,6,28,'','','',1,0,0,'',''),
	(262,'香薰',0,6,29,'','','',0,0,0,'',''),
	(263,'沙发',0,6,29,'','','',0,0,0,'',''),
	(264,'衣柜',0,6,29,'','','',0,0,0,'',''),
	(265,'床品',0,6,29,'','','',1,0,0,'',''),
	(266,'窗帘',0,6,29,'','','',0,0,0,'',''),
	(267,'地毯',0,6,29,'','','',0,0,0,'',''),
	(268,'收纳',0,6,29,'','','',0,0,0,'',''),
	(269,'坐垫',0,6,29,'','','',0,0,0,'',''),
	(270,'灯饰',0,6,29,'','','',0,0,0,'',''),
	(271,'杯子',0,6,29,'','','',0,0,0,'',''),
	(272,'时钟',0,6,29,'','','',0,0,0,'',''),
	(273,'柜子',0,6,29,'','','',0,0,0,'',''),
	(274,'床',0,6,29,'','','',0,0,0,'',''),
	(275,'墙贴',0,6,29,'','','',0,0,0,'',''),
	(276,'相框',0,6,29,'','','',0,0,0,'',''),
	(277,'加湿器',0,6,29,'','','',1,0,0,'',''),
	(278,'化妆镜',0,6,29,'','','',0,0,0,'',''),
	(279,'勺子',0,6,30,'','','',0,0,0,'',''),
	(280,'便当盒',0,6,30,'','','',0,0,0,'',''),
	(281,'烘焙',0,6,30,'','','',0,0,0,'',''),
	(282,'调味瓶',0,6,30,'','','',0,0,0,'',''),
	(283,'冰格',0,6,30,'','','',0,0,0,'',''),
	(284,'餐具',0,6,30,'','','',0,0,0,'',''),
	(285,'模具',0,6,30,'','','',0,0,0,'',''),
	(286,'马克杯',0,6,30,'','','',1,0,0,'',''),
	(287,'密封罐',0,6,30,'','','',0,0,0,'',''),
	(288,'卫浴',0,6,30,'','','',0,0,0,'',''),
	(289,'烹饪',0,6,30,'','','',0,0,0,'',''),
	(290,'毛巾',0,6,30,'','','',0,0,0,'',''),
	(291,'手机壳',0,6,31,'','','',1,0,0,'',''),
	(292,'绕线器',0,6,31,'','','',0,0,0,'',''),
	(293,'鼠标垫',0,6,31,'','','',0,0,0,'',''),
	(294,'iPad套',0,6,31,'','','',1,0,0,'',''),
	(295,'防尘塞',0,6,31,'','','',0,0,0,'',''),
	(296,'音响',0,6,31,'','','',0,0,0,'',''),
	(297,'相机',0,6,31,'','','',1,0,0,'',''),
	(298,'DIY手机壳',0,6,31,'','','',0,0,0,'',''),
	(299,'手机链',0,6,31,'','','',0,0,0,'',''),
	(300,'耳机',0,6,31,'','','',0,0,0,'',''),
	(301,'娃娃衫',0,1,7,'','','',1,100,0,'',''),
	(302,'蓬蓬上衣',0,1,7,'','','',1,100,0,'',''),
	(303,'裙裤',0,1,9,'','','',1,100,0,'',''),
	(304,'哈伦裤',0,1,9,'','','',1,100,0,'',''),
	(305,'短裤',0,1,9,'','','',1,100,0,'',''),
	(306,'纯棉',0,1,11,'','','',1,100,0,'',''),
	(307,'尼龙',0,1,11,'','','',1,100,0,'',''),
	(308,'test',1,0,0,'','','',0,0,0,'',''),
	(309,',鞋子',1,0,0,'','','',0,0,0,'','');

/*!40000 ALTER TABLE `jd_items_tags` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_items_tags_item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_items_tags_item`;

CREATE TABLE `jd_items_tags_item` (
  `item_id` int(10) NOT NULL,
  `tag_id` int(10) NOT NULL,
  KEY `item_id` (`item_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_items_tags_item` WRITE;
/*!40000 ALTER TABLE `jd_items_tags_item` DISABLE KEYS */;

INSERT INTO `jd_items_tags_item` (`item_id`, `tag_id`)
VALUES
	(1,308),
	(1,309);

/*!40000 ALTER TABLE `jd_items_tags_item` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_link`;

CREATE TABLE `jd_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ord` smallint(5) NOT NULL,
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_link` WRITE;
/*!40000 ALTER TABLE `jd_link` DISABLE KEYS */;

INSERT INTO `jd_link` (`id`, `name`, `url`, `ord`, `is_del`, `remark1`, `remark2`)
VALUES
	(1,'简单CMS','http://www.jdcms.com/',0,0,'',''),
	(2,'下一件','http://www.xiayijian.cn/',0,0,'',''),
	(3,'尚通天地','http://www.shangtongtiandi.com/',0,0,'','');

/*!40000 ALTER TABLE `jd_link` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_shop
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_shop`;

CREATE TABLE `jd_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT '0',
  `seller_id` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
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



# Dump of table jd_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_user`;

CREATE TABLE `jd_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  `passwd` varchar(50) NOT NULL DEFAULT '0',
  `img` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `sex` tinyint(4) NOT NULL DEFAULT '0',
  `brithday` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(50) NOT NULL DEFAULT '',
  `info` varchar(500) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `last_time` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) DEFAULT '',
  `share_num` int(11) DEFAULT '0',
  `age` tinyint(4) DEFAULT '0',
  `is_del` tinyint(1) NOT NULL DEFAULT '0',
  `is_sys` tinyint(1) DEFAULT '0',
  `fans_num` int(10) NOT NULL DEFAULT '0',
  `follow_num` int(10) NOT NULL DEFAULT '0',
  `likes_num` int(10) NOT NULL DEFAULT '0',
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `jd_user` WRITE;
/*!40000 ALTER TABLE `jd_user` DISABLE KEYS */;

INSERT INTO `jd_user` (`id`, `name`, `passwd`, `img`, `email`, `sex`, `brithday`, `address`, `info`, `ip`, `add_time`, `status`, `last_time`, `last_ip`, `share_num`, `age`, `is_del`, `is_sys`, `fans_num`, `follow_num`, `likes_num`, `remark1`, `remark2`)
VALUES
	(1,'520hacker','','user100_1001.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(2,'Wei伟威','','user100_1006.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(3,'a349760632','','user100_1011.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(4,'aiverforu','','user100_1016.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(5,'alin','','user100_1021.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(6,'andy','','user100_1026.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(7,'aqhenry','','user100_1031.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(8,'babyclaire','','user100_1036.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(9,'beaty','','user100_1041.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(10,'bgtwoigu','','user100_1046.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(11,'bjut','','user100_1051.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(12,'bluedoom','','user100_1056.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(13,'bluedoom3','','user100_1061.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(14,'bluegy','','user100_1066.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(15,'brislin','','user100_1071.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(16,'bryan','','user100_1076.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(17,'caoyang','','user100_1081.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(18,'carrycarrie','','user100_1086.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(19,'cecelee87','','user100_1091.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(20,'chenjiao','','user100_1096.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(21,'chenjiayi','','user100_1101.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(22,'chenjueshi','','user100_1106.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(23,'chenlh','','user100_1111.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(24,'chenraymond28','','user100_1116.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(25,'cherry','','user100_1121.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(26,'chunchun','','user100_1126.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(27,'cindy','','user100_1131.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(28,'cjp472','','user100_1136.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(29,'di2yangguan','','user100_1141.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(30,'duckquan97','','user100_1146.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(31,'echo','','user100_1151.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(32,'elieli','','user100_1156.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(33,'elwin','','user100_1091.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(34,'emchali','','user100_1166.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(35,'emojj','','user100_1171.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(36,'emyp','','user100_1176.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(37,'farname任','','user100_1181.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(38,'feelwinds','','user100_1186.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(39,'feijie','','user100_1191.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(40,'flora','','user100_1196.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(41,'flowercctv2000','','user100_1201.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(42,'free','','user100_1206.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(43,'fuckcp','','user100_1211.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(44,'gaofang123','','user100_1216.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(45,'gene','','user100_1221.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(46,'golbz','','user100_1226.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(47,'henter','','user100_1231.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(48,'ibox','','user100_1236.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(49,'iearlcat','','user100_1241.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(50,'ireneshy','','user100_1246.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(51,'jb32han','','user100_1251.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(52,'jeffwu','','user100_1256.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(53,'jessica030603','','user100_1261.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(54,'julie','','user100_1266.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(55,'kakaloka','','user100_1271.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(56,'kaneliu','','user100_1276.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(57,'karon','','user100_1281.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(58,'ken_zhang','','user100_1286.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(59,'kingo0','','user100_1291.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(60,'kmxs','','user100_1296.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(61,'kunee','','user100_1301.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(62,'ky1127','','user100_1306.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(63,'leaves','','user100_1311.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(64,'lglg','','user100_1316.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(65,'li_hongyan','','user100_1321.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(66,'libai','','user100_1326.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(67,'lilna','','user100_1331.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(68,'lisa','','user100_1336.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(69,'liugui79','','user100_1341.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(70,'liujun','','user100_1346.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(71,'liuyangmihu','','user100_1351.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(72,'lk0703','','user100_1356.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(73,'lplsun','','user100_1361.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(74,'lstup','','user100_1366.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(75,'lukeman','','user100_1371.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(76,'lynn','','user100_1376.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(77,'lyon','','user100_1381.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(78,'magicbrighter','','user100_1386.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(79,'magicxdy','','user100_1391.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(80,'maildocgaojingru','','user100_1396.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(81,'manco','','user100_1401.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(82,'martin','','user100_1406.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(83,'me很抽象','','user100_1411.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(84,'misswu','','user100_1416.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(85,'mitang','','user100_1421.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(86,'namelysweet','','user100_1426.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(87,'nemo','','user100_1431.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(88,'neobudda','','user100_1436.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(89,'neria','','user100_1441.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(90,'niliuyou','','user100_1446.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(91,'notnull','','user100_1451.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(92,'omyga小葵','','user100_1456.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(93,'paris','','user100_1461.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(94,'phoenixlaw','','user100_1466.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(95,'prayer0326','','user100_1471.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(96,'rachel','','user100_1476.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(97,'realalley','','user100_1481.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(98,'renwoxuan','','user100_1486.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(99,'riverwind','','user100_1491.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(100,'rosehao2008','','user100_1496.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(101,'rosibo','','user100_1501.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(102,'rron','','user100_1506.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(103,'s123','','user100_1511.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(104,'samsoonzm','','user100_1516.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(105,'saymoon','','user100_1521.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(106,'scott','','user100_1526.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(107,'sdfsdfsdf','','user100_1531.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(108,'sneakercn','','user100_1536.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(109,'spring1142879041','','user100_1541.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(110,'success_gong','','user100_1546.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(111,'summerhe','','user100_1551.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(112,'sunwgjj','','user100_1556.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(113,'下一站天后','','user100_1561.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(114,'不做穿西装的野人','','user100_1566.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(115,'不凑巧仁兄','','user100_1571.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(116,'严丽佳','','user100_1576.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(117,'兰兰呦','','user100_1581.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(118,'出尘子','','user100_1586.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(119,'北京懒猫咪','','user100_1591.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(120,'十七哥','','user100_1596.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(121,'卷边鱼','','user100_1601.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(122,'史蒂芬康康','','user100_1606.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(123,'叶湘伦','','user100_1611.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(124,'启己','','user100_1616.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(125,'咕噜有为','','user100_1621.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(126,'啊啊啊','','user100_1626.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(127,'四月','','user100_1631.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(128,'大冬','','user100_1636.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(129,'大圣','','user100_1641.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(130,'大眼绿毛龟','','user100_1646.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(131,'天缘江湖','','user100_1651.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(132,'天边的依恋','','user100_1656.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(133,'太阳鸟','','user100_1661.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(134,'女王','','user100_1666.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(135,'妖精','','user100_1671.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(136,'宝宝918828','','user100_1676.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(137,'寰宇星景天','','user100_1681.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(138,'小三','','user100_1686.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(139,'小丸子','','user100_1691.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(140,'小妖怪dogut','','user100_1696.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(141,'小娅','','user100_1701.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(142,'小娜空间','','user100_1706.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(143,'小小桃','','user100_1711.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(144,'小屋子','','user100_1716.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(145,'小怪','','user100_1721.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(146,'小猪','','user100_1726.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(147,'小猴纸乐乐','','user100_1731.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(148,'小疯子','','user100_1736.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(149,'小耳朵','','user100_1741.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(150,'小艾','','user100_1746.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(151,'小谢','','user100_1751.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(152,'小雪','','user100_1756.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(153,'小飞侠','','user100_1761.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(154,'尚高楼','','user100_1766.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(155,'就爱田贱人','','user100_1771.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(156,'屠钱钱','','user100_1776.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(157,'岳萌萌琉光蝶','','user100_1781.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(158,'庐陵网','','user100_1786.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(159,'张亮','','user100_1791.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(160,'彩云飞','','user100_1796.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(161,'很淡很淡定','','user100_1801.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(162,'怕冷的Nelly','','user100_1806.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(163,'恢恢','','user100_1811.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(164,'想猫想狗想睡觉','','user100_1816.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(165,'我性随风','','user100_1821.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(166,'我是周歪歪','','user100_1826.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(167,'房子','','user100_1831.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(168,'方可fun','','user100_1836.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(169,'方枪枪的小飞机','','user100_1841.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(170,'早说可以用昵称嘛','','user100_1846.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(171,'晴天安好','','user100_1851.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(172,'曹cal','','user100_1856.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(173,'有木有','','user100_1861.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(174,'朱明祖','','user100_1866.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(175,'李开复','','user100_1871.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(176,'棺材','','user100_1876.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(177,'洋柿子','','user100_1881.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(178,'洋洋妈007','','user100_1886.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(179,'浣熊镇','','user100_1891.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(180,'潇洒花花牛','','user100_1896.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(181,'王国辉','','user100_1901.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(182,'王小熊','','user100_1906.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(183,'王强','','user100_1911.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(184,'珊珊','','user100_1916.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(185,'瞌睡宝宝','','user100_1921.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(186,'秋喜','','user100_1926.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(187,'穆小河','','user100_1931.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(188,'站在网络上','','user100_1936.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(189,'粗面馒头一个','','user100_1941.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(190,'約約約','','user100_1946.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(191,'绝世小妖孽','','user100_1951.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(192,'耗子','','user100_1956.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(193,'脆弱的平衡','','user100_1961.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(194,'草莓留洋','','user100_1966.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(195,'菲宁莫属','','user100_1971.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(196,'落落','','user100_1976.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(197,'蒙蒙','','user100_1981.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(198,'蓝色之恋','','user100_1986.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(199,'衣衣印社','','user100_1991.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(200,'豚豚小驴','','user100_1996.jpg','',0,'2008-08-08','','','127.0.0.1',1351123200,1,1351123200,'127.0.0.1',0,0,0,1,0,0,0,'',''),
	(201,'yongze','f4d1ded501fd849d49554272a4b8608d','1414800278.jpg','18019900503@qq.com',0,'','','','114.246.105.79',1414800259,1,1414811727,'110.96.211.121',1,80,0,0,0,0,1,'',''),
	(202,'keke','f4d1ded501fd849d49554272a4b8608d','','',1,'','','','',1414811909,1,1414811909,'',0,0,0,1,0,0,0,'',''),
	(203,'bb','f4d1ded501fd849d49554272a4b8608d','','',0,'','','','',1414811909,1,1414811909,'',0,0,0,1,0,0,0,'',''),
	(204,'ddd','f4d1ded501fd849d49554272a4b8608d','','',1,'','','','',1414811909,1,1414811909,'',0,0,0,1,0,0,0,'',''),
	(205,'hhh','f4d1ded501fd849d49554272a4b8608d','','',0,'','','','',1414811909,1,1414811909,'',0,0,0,1,0,0,0,'',''),
	(206,'你是我的唯一','f4d1ded501fd849d49554272a4b8608d','','',0,'','','','',1414811909,1,1414811909,'',0,0,0,1,0,0,0,'','');

/*!40000 ALTER TABLE `jd_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jd_user_follow
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_user_follow`;

CREATE TABLE `jd_user_follow` (
  `fans_id` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table jd_user_openid
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jd_user_openid`;

CREATE TABLE `jd_user_openid` (
  `type` varchar(11) DEFAULT NULL,
  `uid` varchar(15) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `info` text,
  `remark1` varchar(255) DEFAULT '',
  `remark2` varchar(255) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
