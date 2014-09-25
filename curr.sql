-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: thatmust_oc_ben
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL COMMENT '	',
  `display_name` varchar(45) DEFAULT NULL,
  `email_address` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authors`
--

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` VALUES (1,'Benjamin','Roberts','Ben Roberts','ben@thatmustbe.me');
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (0,'Uncategorized'),(2,'IndieWeb');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_posts`
--

DROP TABLE IF EXISTS `categories_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_posts` (
  `category_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_posts`
--

LOCK TABLES `categories_posts` WRITE;
/*!40000 ALTER TABLE `categories_posts` DISABLE KEYS */;
INSERT INTO `categories_posts` VALUES (0,5),(0,6),(2,7);
/*!40000 ALTER TABLE `categories_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_syndication`
--

DROP TABLE IF EXISTS `comment_syndication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_syndication` (
  `comment_syndication_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) DEFAULT NULL,
  `syndication_site_id` int(11) DEFAULT NULL,
  `syndication_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`comment_syndication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_syndication`
--

LOCK TABLES `comment_syndication` WRITE;
/*!40000 ALTER TABLE `comment_syndication` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment_syndication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `body` text,
  `author_name` varchar(200) DEFAULT NULL,
  `author_url` varchar(300) DEFAULT NULL,
  `author_image` varchar(300) DEFAULT NULL,
  `source_name` varchar(45) DEFAULT NULL,
  `source_url` varchar(150) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `approved` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_field`
--

DROP TABLE IF EXISTS `contact_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_field` (
  `contact_field_id` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `field_type_id` int(11) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `order` int(11) DEFAULT '1',
  PRIMARY KEY (`contact_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_field`
--

LOCK TABLES `contact_field` WRITE;
/*!40000 ALTER TABLE `contact_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_group`
--

DROP TABLE IF EXISTS `contact_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_group` (
  `contact_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`contact_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_group`
--

LOCK TABLES `contact_group` WRITE;
/*!40000 ALTER TABLE `contact_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `main_url` varchar(100) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `context`
--

DROP TABLE IF EXISTS `context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `context` (
  `context_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(200) DEFAULT NULL,
  `author_url` varchar(300) DEFAULT NULL,
  `author_image` varchar(300) DEFAULT NULL,
  `source_name` varchar(45) DEFAULT NULL,
  `source_url` varchar(300) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `body` text,
  PRIMARY KEY (`context_id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `context`
--

LOCK TABLES `context` WRITE;
/*!40000 ALTER TABLE `context` DISABLE KEYS */;
/*!40000 ALTER TABLE `context` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `context_syndication`
--

DROP TABLE IF EXISTS `context_syndication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `context_syndication` (
  `context_syndication_id` int(11) NOT NULL AUTO_INCREMENT,
  `context_id` int(11) DEFAULT NULL,
  `syndication_site_id` int(11) DEFAULT NULL,
  `syndication_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`context_syndication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `context_syndication`
--

LOCK TABLES `context_syndication` WRITE;
/*!40000 ALTER TABLE `context_syndication` DISABLE KEYS */;
/*!40000 ALTER TABLE `context_syndication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `context_to_context`
--

DROP TABLE IF EXISTS `context_to_context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `context_to_context` (
  `context_id` int(11) NOT NULL,
  `parent_context_id` int(11) NOT NULL,
  PRIMARY KEY (`context_id`,`parent_context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `context_to_context`
--

LOCK TABLES `context_to_context` WRITE;
/*!40000 ALTER TABLE `context_to_context` DISABLE KEYS */;
/*!40000 ALTER TABLE `context_to_context` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field_types`
--

DROP TABLE IF EXISTS `field_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_types` (
  `field_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_label` varchar(45) DEFAULT NULL,
  `field_display_image` varchar(155) DEFAULT NULL,
  `mobile_label` varchar(100) DEFAULT NULL,
  `link_format` varchar(100) DEFAULT NULL,
  `is_link` tinyint(1) DEFAULT '1',
  `classes` varchar(100) DEFAULT NULL,
  `classification` enum('elsewhere','contact','other') DEFAULT 'elsewhere',
  PRIMARY KEY (`field_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_types`
--

LOCK TABLES `field_types` WRITE;
/*!40000 ALTER TABLE `field_types` DISABLE KEYS */;
INSERT INTO `field_types` VALUES (1,'Facebook','/image/static/facebook-app.png','Facebook','https://www.facebook.com/{}',1,NULL,'elsewhere'),(2,'Github','/image/static/github.png','Github','https://github.com/{}',1,NULL,'elsewhere'),(3,'Google+','/image/static/googleplus.png','Google+','https://plus.google.com/{}',1,NULL,'elsewhere'),(4,'Twitter','/image/static/twitter.png','Twitter','https://twitter.com/{}',1,NULL,'elsewhere'),(5,'Tumblr','/image/static/tumblr.png','Tumblr','http://{}.tumblr.com/',1,NULL,'elsewhere'),(6,'Instagram','/image/static/instagram.png','Instagram','http://instagram.com/{}',1,NULL,'elsewhere'),(7,'Kickstarter','/image/static/kickstarter.png','Kickstarte','https://www.kickstarter.com/profile/{}',1,NULL,'elsewhere'),(8,'PGP Key','/image/static/pgp.png','PGP Key','{}',1,NULL,'other'),(9,'AIM: {}','/image/static/aim.png','AIM','aim:goim?screenname={}',1,'u-impp','contact'),(10,'Email: {}','/image/static/gmail.png','Email','mailto:{}',1,NULL,'contact'),(11,'SMS: {}','/image/static/hangouts.png','SMS','sms:{}',1,NULL,'contact'),(12,'Twitter DM','/image/static/twitter.png','Twitter DM','https://mobile.twitter.com/{}/messages',1,NULL,'contact'),(13,'Facebook Message','/image/static/fb-messenger.png','<abbr title=\"Facebook\">FB<abbr> message','fb-messenger://user-thread/{}',1,NULL,'contact'),(14,'Tel: {}','/image/static/dialer.png','Call','tel:{}',1,NULL,'contact');
/*!40000 ALTER TABLE `field_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (0,'Public'),(2,'Friends');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `source_url` varchar(150) DEFAULT NULL,
  `author_name` varchar(200) DEFAULT NULL,
  `author_url` varchar(300) DEFAULT NULL,
  `author_image` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`like_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mentions`
--

DROP TABLE IF EXISTS `mentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mentions` (
  `mention_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `source_url` varchar(150) DEFAULT NULL,
  `approved` tinyint(4) DEFAULT '0',
  `parse_timestamp` timestamp NULL DEFAULT NULL,
  `author_name` varchar(200) DEFAULT NULL,
  `author_url` varchar(300) DEFAULT NULL,
  `author_image` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`mention_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mentions`
--

LOCK TABLES `mentions` WRITE;
/*!40000 ALTER TABLE `mentions` DISABLE KEYS */;
/*!40000 ALTER TABLE `mentions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mydata`
--

DROP TABLE IF EXISTS `mydata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mydata` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `sorting` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `rel` varchar(45) DEFAULT NULL,
  `target` varchar(45) DEFAULT NULL,
  `on_homepage` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mydata`
--

LOCK TABLES `mydata` WRITE;
/*!40000 ALTER TABLE `mydata` DISABLE KEYS */;
/*!40000 ALTER TABLE `mydata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mydata_group`
--

DROP TABLE IF EXISTS `mydata_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mydata_group` (
  `data_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`data_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mydata_group`
--

LOCK TABLES `mydata_group` WRITE;
/*!40000 ALTER TABLE `mydata_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `mydata_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `content` text,
  `slug` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_context`
--

DROP TABLE IF EXISTS `post_context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_context` (
  `post_id` int(11) NOT NULL,
  `context_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_context`
--

LOCK TABLES `post_context` WRITE;
/*!40000 ALTER TABLE `post_context` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_context` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_replyto`
--

DROP TABLE IF EXISTS `post_replyto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_replyto` (
  `post_replyto_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `in-reply-to` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`post_replyto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_replyto`
--

LOCK TABLES `post_replyto` WRITE;
/*!40000 ALTER TABLE `post_replyto` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_replyto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_syndication`
--

DROP TABLE IF EXISTS `post_syndication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_syndication` (
  `post_syndication_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `syndication_site_id` int(11) DEFAULT NULL,
  `syndication_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`post_syndication_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_syndication`
--

LOCK TABLES `post_syndication` WRITE;
/*!40000 ALTER TABLE `post_syndication` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_syndication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text,
  `timestamp` datetime DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `year` smallint(4) unsigned DEFAULT NULL,
  `month` tinyint(2) unsigned DEFAULT NULL,
  `day` tinyint(2) unsigned DEFAULT NULL COMMENT 'daycount is the post number for that specific day',
  `daycount` tinyint(3) unsigned DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `post_type` enum('article','note','photo','checkin','event','rsvp') DEFAULT 'article',
  `excerpt` varchar(255) DEFAULT NULL,
  `replyto` varchar(300) DEFAULT NULL,
  `image_file` varchar(100) DEFAULT NULL,
  `context_parsed` tinyint(1) DEFAULT '0',
  `syndication_extra` varchar(255) DEFAULT NULL,
  `draft` tinyint(1) DEFAULT '0',
  `edit_timestamp` datetime DEFAULT NULL,
  `event_start_timestamp` datetime DEFAULT NULL,
  `event_end_timestamp` datetime DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `place_name` varchar(100) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `rsvp_attending` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `syndication_site`
--

DROP TABLE IF EXISTS `syndication_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `syndication_site` (
  `syndication_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(45) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `site_url_match` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`syndication_site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `syndication_site`
--

LOCK TABLES `syndication_site` WRITE;
/*!40000 ALTER TABLE `syndication_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `syndication_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `checksum` varchar(80) NOT NULL,
  `scope` varchar(200) DEFAULT NULL,
  `client_id` varchar(200) DEFAULT NULL,
  `last_used` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webmentions`
--

DROP TABLE IF EXISTS `webmentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmentions` (
  `webmention_id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `source_url` varchar(300) DEFAULT NULL,
  `target_url` varchar(300) DEFAULT NULL,
  `webmention_status` varchar(45) DEFAULT NULL,
  `admin_status` varchar(45) DEFAULT NULL,
  `approved_url` varchar(300) DEFAULT NULL,
  `callback_url` varchar(300) DEFAULT NULL,
  `webmention_status_code` varchar(3) DEFAULT '202',
  `resulting_comment_id` int(11) DEFAULT NULL,
  `resulting_mention_id` int(11) DEFAULT NULL,
  `resulting_like_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`webmention_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webmentions`
--

LOCK TABLES `webmentions` WRITE;
/*!40000 ALTER TABLE `webmentions` DISABLE KEYS */;
/*!40000 ALTER TABLE `webmentions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-08 15:29:23
