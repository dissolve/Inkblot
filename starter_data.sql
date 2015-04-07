-- MySQL dump 10.15  Distrib 10.0.14-MariaDB, for Linux (x86_64)
--
-- Host: thatmustbe.me    Database: thatmust_oc_ben
-- ------------------------------------------------------
-- Server version	5.5.40-cll

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
-- Dumping data for table `syndication_site`
--

LOCK TABLES `syndication_site` WRITE;
/*!40000 ALTER TABLE `syndication_site` DISABLE KEYS */;
INSERT INTO `syndication_site` VALUES (1,'Facebook','/image/static/facebook-app.png','https://facebook.com/'),(2,'Twitter','/image/static/twitter.png','https://twitter.com/'),(3,'Google+','/image/static/googleplus.png','https://plus.google.com/'),(4,'Instagram','/image/static/instagram.png','http://instagram.com/');
/*!40000 ALTER TABLE `syndication_site` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (0,'Uncategorized'),(2,'IndieWeb');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Dumping data for table `field_types`
--

LOCK TABLES `field_types` WRITE;
/*!40000 ALTER TABLE `field_types` DISABLE KEYS */;
INSERT INTO `field_types` VALUES (1,'Facebook','/image/static/facebook-app.png','Facebook','https://www.facebook.com/{}',1,NULL,'elsewhere'),(2,'Github','/image/static/github.png','Github','https://github.com/{}',1,NULL,'elsewhere'),(3,'Google+','/image/static/googleplus.png','Google+','https://plus.google.com/{}',1,NULL,'elsewhere'),(4,'Twitter','/image/static/twitter.png','Twitter','https://twitter.com/{}',1,NULL,'elsewhere'),(5,'Tumblr','/image/static/tumblr.png','Tumblr','http://{}.tumblr.com/',1,NULL,'elsewhere'),(6,'Instagram','/image/static/instagram.png','Instagram','http://instagram.com/{}',1,NULL,'elsewhere'),(7,'Kickstarter','/image/static/kickstarter.png','Kickstarte','https://www.kickstarter.com/profile/{}',1,NULL,'elsewhere'),(8,'PGP Key','/image/static/pgp.png','PGP Key','{}',1,NULL,'other'),(9,'AIM: {}','/image/static/aim.png','AIM','aim:goim?screenname={}',1,'u-impp','contact'),(10,'Email: {}','/image/static/gmail.png','Email','mailto:{}',1,NULL,'contact'),(11,'SMS: {}','/image/static/hangouts.png','SMS','sms:{}',1,NULL,'contact'),(12,'Twitter DM','/image/static/twitter.png','Twitter DM','https://mobile.twitter.com/{}/messages',1,NULL,'contact'),(13,'Facebook Message','/image/static/fb-messenger.png','<abbr title=\"Facebook\">FB<abbr> message','fb-messenger://user-thread/{}',1,NULL,'contact'),(14,'Tel: {}','/image/static/dialer.png','Call','tel:{}',1,NULL,'contact');
/*!40000 ALTER TABLE `field_types` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (0,'Public'),(2,'Friends');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-07 11:50:09
