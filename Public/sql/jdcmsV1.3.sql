/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

INSERT INTO `jd_items_tags`(`name`,`item_nums`,`pid`,`sid`,`seo_title`,`seo_keys`,`seo_desc`,`is_index`,`ord`,`is_del`,`remark1`,`remark2`) VALUES ('ÍÞÍÞÉÀ',0,1,7,'','','',1,100,0,'',''),('ÅîÅîÉÏÒÂ',0,1,7,'','','',1,100,0,'',''),('È¹¿ã',0,1,9,'','','',1,100,0,'',''),('¹þÂ×¿ã',0,1,9,'','','',1,100,0,'',''),('¶Ì¿ã',0,1,9,'','','',1,100,0,'',''),('´¿ÃÞ',0,1,11,'','','',1,100,0,'',''),('ÄáÁú',0,1,11,'','','',1,100,0,'','');
UPDATE `jd_items_tags` SET `ord`=100 WHERE NAME IN('TÐô','³ÄÉÀ','Î÷×°','Ñ©·ÄÉÀ','±³ÐÄ','µõ´øÉÀ','Âí¼Ð','·çÒÂ','¼Ð¿Ë','ÎÀÒÂ','Å£×Ð¿ã','ÐÝÏÐ¿ã','Ç¦±Ê¿ã','´òµ×¿ã','Õý×°¿ã','Î÷¿ã','Æ¤¿ã','É³Ì²¿ã','Ñ©·Ä','ÃÞÂé','ÕæË¿','Å£×Ð','ÕëÖ¯','Äª´ú¶û','µÓÂÚ','Æ¤ÖÊ','ÑòÈÞ','À³¿¨');
UPDATE `jd_items_tags` SET `is_index`=0 WHERE name IN('ÓðÈÞ·þ','ÄØ´óÒÂ','Ã«ÒÂ');

/*Table structure for table `jd_article_collect` */

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

/*Data for the table `jd_article_collect` */