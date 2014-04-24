-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: thatmust_oc_ben
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.13.10.2

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (2,6,NULL,'Great work getting webmentions working Ben, and nice cover photo! Where was it taken?','Barnaby Walters','http://waterpigs.co.uk','http://waterpigs.co.uk/photo.jpg',NULL,'http://waterpigs.co.uk/notes/4VUFr4/','2014-04-08 19:51:04',1);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (1,6,NULL,'http://kylewm.com/like/2014/04/08/1','Kyle Mahan','http://kylewm.com','http://kylewm.com/static/img/users/kyle_large.jpg'),(2,6,NULL,'https://snarfed.org/2014-04-08_8926','Ryan Barrett','http://snarfed.org/','https://secure.gravatar.com/avatar/947b5f3f323da0ef785b6f02d9c265d6?s=96&d=https%3A%2F%2Fsecure.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D96&r=G'),(3,NULL,NULL,'https://snarfed.org/2014-04-08_8929','Ryan Barrett','http://snarfed.org/','https://secure.gravatar.com/avatar/947b5f3f323da0ef785b6f02d9c265d6?s=96&d=https%3A%2F%2Fsecure.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D96&r=G');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `melinks`
--

DROP TABLE IF EXISTS `melinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `melinks` (
  `melink_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `title` varchar(300) DEFAULT NULL,
  `value` varchar(45) DEFAULT NULL,
  `sorting` int(11) DEFAULT NULL,
  `target` varchar(45) DEFAULT '_blank',
  PRIMARY KEY (`melink_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `melinks`
--

LOCK TABLES `melinks` WRITE;
/*!40000 ALTER TABLE `melinks` DISABLE KEYS */;
INSERT INTO `melinks` VALUES (1,'https://www.facebook.com/dissolve333',NULL,'Rarely Used','Facebook',NULL,'_blank'),(2,'https://github.com/dissolve',NULL,NULL,'Github',NULL,'_blank'),(3,'https://plus.google.com/+BenRoberts83',NULL,NULL,'Google+',NULL,'_blank'),(4,'https://twitter.com/dissolve333',NULL,'Rarely Used','Twitter',NULL,'_blank');
/*!40000 ALTER TABLE `melinks` ENABLE KEYS */;
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
  `post_type` enum('post','note') DEFAULT 'post',
  `replyto` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (5,'&lt;p&gt;As of today I am finally setting up my personal site. I have only owned this URL and hosting for it for about a year and thus far had only used it to be my email address.&lt;/p&gt;\r\n\r\n&lt;p&gt;So what is it that lit a fire under me? IndieWebCamp and IndieAuth. I’ll be working on some new ideas I have for how we could get people to host their own contact info. It could be quite nice. Perhaps working with other sites like sync.me to follow an open standard.&lt;/p&gt;\r\n\r\n&lt;p&gt;That would be amazing. Anyway, back to work.&lt;/p&gt;\r\n','2014-03-24 15:01:00','Episode IV: A New Home',1,2014,3,24,1,'Episode_IV_A_New_Home','post',NULL),(6,'&lt;p&gt;A second post. I am in the midst of building my entire site from scratch.&lt;/p&gt;\r\n\r\n&lt;p&gt;I say from scratch but in reality I am actually ripping apart opencart to build on their basic MVC structure. Hopefully I will have this in github by the end of the day. If not, next week.&lt;/p&gt;\r\n','2014-03-28 13:47:45','Hard At Work',1,2014,3,28,1,'Hard_At_Work','post',NULL),(7,'&lt;p&gt;As of today, I have webmention working on my site. What does webmention mean? Well start by checking out &lt;a href=&quot;http://indiewebcamp.com&quot; rel=&quot;nofollow&quot;&gt;IndieWebCamp&lt;/a&gt;. Its basically a system for people to like and comment on my posts by simply making posts on their site and sending me some notice of it.&lt;/p&gt;\r\n\r\n&lt;p&gt;I have a lot of work ahead of me still. I want to be able to send out these types of posts, and I don\'t even really have any admin interface for my site. I am typing this right now straight in to the database. Yes, I\'m just that cool.&lt;/p&gt;\r\n\r\n&lt;p&gt;Actually, I just noticed regular mentions aren\'t noted anywhere. I shall have to fix that.&lt;/p&gt;\r\n','2014-04-11 11:37:43','Webmention Glory',1,2014,4,11,1,'Webmention_Glory','post',NULL),(17,'&lt;p&gt;This was taken at Niagara Falls, Canada.&amp;nbsp; They have a cool little walkway under the falls.&lt;/p&gt;\r\n','2014-04-24 12:21:26','',1,2014,4,24,1,'_','note','http://waterpigs.co.uk/notes/4VUFr4/');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webmentions`
--

LOCK TABLES `webmentions` WRITE;
/*!40000 ALTER TABLE `webmentions` DISABLE KEYS */;
INSERT INTO `webmentions` VALUES (4,'2014-04-08 11:51:07','http://waterpigs.co.uk/notes/4VUFr4/','http://ben.thatmustbe.me/post/2014/3/28/1/Hard_At_Work','OK',NULL,NULL,NULL,'200',2,NULL,NULL),(5,'2014-04-08 13:38:40','http://kylewm.com/like/2014/04/08/1','http://ben.thatmustbe.me/post/2014/3/28/1/Hard_At_Work','OK',NULL,NULL,NULL,'200',NULL,NULL,1),(6,'2014-04-08 13:47:39','https://snarfed.org/2014-04-08_8926','http://ben.thatmustbe.me/post/2014/3/28/1/Hard_At_Work','OK',NULL,NULL,NULL,'200',NULL,NULL,2),(7,'2014-04-08 13:48:44','https://snarfed.org/2014-04-08_8929','http://ben.thatmustbe.me/','OK',NULL,NULL,NULL,'200',NULL,NULL,3);
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

-- Dump completed on 2014-04-24 14:58:52
