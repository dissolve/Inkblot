-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)
--
-- Host: thatmustbe.me    Database: thatmust_oc_ben
-- ------------------------------------------------------
-- Server version	5.5.37-cll

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
-- Table structure for table `field_types`
--

DROP TABLE IF EXISTS `field_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `field_types` (
  `field_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_label` varchar(45) DEFAULT NULL,
  `field_display_image` varchar(155) DEFAULT NULL,
  `mobile_protocol` varchar(10) DEFAULT NULL,
  `link_format` varchar(100) DEFAULT NULL,
  `is_link` tinyint(1) DEFAULT '1',
  `classes` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`field_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_types`
--

LOCK TABLES `field_types` WRITE;
/*!40000 ALTER TABLE `field_types` DISABLE KEYS */;
INSERT INTO `field_types` VALUES (1,'Facebook',NULL,NULL,'https://www.facebook.com/{}',1,NULL),(2,'Github',NULL,NULL,'https://github.com/{}',1,NULL),(3,'Google+',NULL,NULL,'https://plus.google.com/{}',1,NULL),(4,'Twitter',NULL,NULL,'https://twitter.com/{}',1,NULL),(5,'Tumblr',NULL,NULL,'http://{}.tumblr.com/',1,NULL),(6,'Instagram',NULL,NULL,'http://instagram.com/{}',1,NULL),(7,'Kickstarter',NULL,NULL,'https://www.kickstarter.com/profile/{}',1,NULL),(8,'PGP Key',NULL,NULL,'{}',1,NULL),(9,'AIM: {}',NULL,NULL,'aim://{}',1,NULL),(10,'Email',NULL,NULL,'mailto://{}',1,NULL),(11,'SMS',NULL,NULL,'sms://{}',1,NULL);
/*!40000 ALTER TABLE `field_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friend_field`
--

DROP TABLE IF EXISTS `friend_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friend_field` (
  `friend_field_id` int(11) NOT NULL,
  `friend_id` int(11) DEFAULT NULL,
  `field_type_id` int(11) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`friend_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friend_field`
--

LOCK TABLES `friend_field` WRITE;
/*!40000 ALTER TABLE `friend_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `friend_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friend_group`
--

DROP TABLE IF EXISTS `friend_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friend_group` (
  `friend_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`friend_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friend_group`
--

LOCK TABLES `friend_group` WRITE;
/*!40000 ALTER TABLE `friend_group` DISABLE KEYS */;
INSERT INTO `friend_group` VALUES (0,0),(1,2),(2,2),(3,2),(4,2),(5,2);
/*!40000 ALTER TABLE `friend_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friends` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `main_url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`friend_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friends`
--

LOCK TABLES `friends` WRITE;
/*!40000 ALTER TABLE `friends` DISABLE KEYS */;
INSERT INTO `friends` VALUES (0,''),(1,'http://www.c.me'),(2,'https://k.com'),(3,'http://a.com'),(4,'https://s.org'),(5,'http://t.com');
/*!40000 ALTER TABLE `friends` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `melinks`
--

LOCK TABLES `melinks` WRITE;
/*!40000 ALTER TABLE `melinks` DISABLE KEYS */;
INSERT INTO `melinks` VALUES (1,'https://www.facebook.com/dissolve333',NULL,'Rarely Used','Facebook',NULL,'_blank'),(2,'https://github.com/dissolve',NULL,NULL,'Github',NULL,'_blank'),(3,'https://plus.google.com/+BenRoberts83',NULL,NULL,'Google+',NULL,'_blank'),(4,'https://twitter.com/dissolve333',NULL,'Rarely Used','Twitter',NULL,'_blank'),(5,'http://benthatmustbeme.tumblr.com/',NULL,NULL,'Tumblr',NULL,'_blank'),(6,'http://instagram.com/dissolve333',NULL,NULL,'Instagram',NULL,'_blank'),(7,'https://www.kickstarter.com/profile/thatmustbeme',NULL,NULL,'Kickstarter',NULL,'_blank');
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
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mydata`
--

LOCK TABLES `mydata` WRITE;
/*!40000 ALTER TABLE `mydata` DISABLE KEYS */;
INSERT INTO `mydata` VALUES (1,1,'dissolve333',1,'Rarely Used','me nofollow','_blank'),(2,2,'dissolve',2,NULL,'me nofollow','_blank'),(3,3,'+BenRoberts83',3,NULL,'me nofollow','_blank'),(4,4,'dissolve333',4,'Rarely Used','me nofollow','_blank'),(5,5,'benthatmustbeme',5,NULL,'me nofollow','_blank'),(6,6,'dissolve333',6,NULL,'me nofollow','_blank'),(7,7,'thatmustbeme',7,NULL,'me nofollow','_blank'),(8,8,'/static/ben.gpg',8,NULL,'gpgkey',NULL),(9,9,'xxxxxxxxxx',9,NULL,'nofollow',NULL),(10,10,'xxxxxxxxxx',10,NULL,'nofollow',NULL),(11,11,'xxxxxxxxxx',11,NULL,'nofollow',NULL);
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
INSERT INTO `mydata_group` VALUES (1,0),(2,0),(3,0),(4,0),(5,0),(6,0),(7,0),(8,0),(9,2),(10,2),(11,2);
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
  `post_type` enum('article','note','photo','checkin','event') DEFAULT 'article',
  `replyto` varchar(300) DEFAULT NULL,
  `image_file` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (5,'&lt;p&gt;As of today I am finally setting up my personal site. I have only owned this URL and hosting for it for about a year and thus far had only used it to be my email address.&lt;/p&gt;\r\n\r\n&lt;p&gt;So what is it that lit a fire under me? IndieWebCamp and IndieAuth. I’ll be working on some new ideas I have for how we could get people to host their own contact info. It could be quite nice. Perhaps working with other sites like sync.me to follow an open standard.&lt;/p&gt;\r\n\r\n&lt;p&gt;That would be amazing. Anyway, back to work.&lt;/p&gt;\r\n','2014-03-24 15:01:00','Episode IV: A New Home',1,2014,3,24,1,'Episode_IV_A_New_Home','article',NULL,NULL),(6,'&lt;p&gt;A second post. I am in the midst of building my entire site from scratch.&lt;/p&gt;\r\n\r\n&lt;p&gt;I say from scratch but in reality I am actually ripping apart opencart to build on their basic MVC structure. Hopefully I will have this in github by the end of the day. If not, next week.&lt;/p&gt;\r\n','2014-03-28 13:47:45','Hard At Work',1,2014,3,28,1,'Hard_At_Work','article',NULL,NULL),(7,'&lt;p&gt;As of today, I have webmention working on my site. What does webmention mean? Well start by checking out &lt;a href=&quot;http://indiewebcamp.com&quot; rel=&quot;nofollow&quot;&gt;IndieWebCamp&lt;/a&gt;. Its basically a system for people to like and comment on my posts by simply making posts on their site and sending me some notice of it.&lt;/p&gt;\r\n\r\n&lt;p&gt;I have a lot of work ahead of me still. I want to be able to send out these types of posts, and I don\'t even really have any admin interface for my site. I am typing this right now straight in to the database. Yes, I\'m just that cool.&lt;/p&gt;\r\n\r\n&lt;p&gt;Actually, I just noticed regular mentions aren\'t noted anywhere. I shall have to fix that.&lt;/p&gt;\r\n','2014-04-11 11:37:43','Webmention Glory',1,2014,4,11,1,'Webmention_Glory','article',NULL,NULL),(17,'&lt;p&gt;This was taken at Niagara Falls, Canada.&amp;nbsp; They have a cool little walkway under the falls.&lt;/p&gt;\r\n','2014-04-24 12:21:26','',1,2014,4,24,1,'_','note','http://waterpigs.co.uk/notes/4VUFr4/',NULL),(18,'&lt;p&gt;Haha, I know this video.&amp;nbsp; It was mentioned on TWiG, and they actually purchased my Twitter handle from me.&lt;/p&gt;\r\n','2014-04-25 11:31:50','',1,2014,4,25,1,'_','note','  http://tiny.n9n.us/2014/04/25/video-embed-test',NULL),(19,'&lt;p&gt;My decent into the &lt;a href=&quot;http://indiewebcamp.com&quot;&gt;Indieweb&lt;/a&gt; continues...&lt;/p&gt;\r\n\r\n&lt;p&gt;I have been working as I can to keep adding new features to &lt;a href=&quot;https://github.com/dissolve/openblog&quot;&gt;OpenBlog&lt;/a&gt;.&amp;nbsp; And I have to say, I am pretty proud of what I have done thus far. \'&lt;/p&gt;\r\n\r\n&lt;p&gt;New features now include having Notes, showing mentions of my posts and notes, and just recently I have finally started adding the ability to actually send webmentions.&amp;nbsp; Now the question is.&amp;nbsp; What to work on next.&lt;/p&gt;\r\n\r\n&lt;p&gt;Here is the current list&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;clean up admin controls - This will be necessary at some point since I want to have this responsive enough to work on my phone.&amp;nbsp; Posting a quick note from mobile would be great.&amp;nbsp; Currently many of the controls don\'t even work.&lt;/li&gt;\r\n	&lt;li&gt;fix the design - I am no artist, but I clearly know this site needs a lot of work.&amp;nbsp; The header could certainly use fixing and comment display is not pretty.&amp;nbsp;&amp;nbsp; I also have a lot to learn to make sure all my microformats markup is correct.&lt;/li&gt;\r\n	&lt;li&gt;Syndicate to Silos - I want to at least syndicate to 1 location as a test, but really I feel this should wait until my site looks halfway decent. (see previous entry)&lt;/li&gt;\r\n	&lt;li&gt;indieAuth - I\'d like to be able to have friends easily able to log in with indieAuth to my site and from there get my personal info and get access to anything extra I add (private photos, etc.)&amp;nbsp; This could be pretty essential for some of the things I want to really get to work on.&lt;/li&gt;\r\n	&lt;li&gt;Contacts List - I want to have a page the gets populated from friend\'s sites that is my list of contacts and info.&amp;nbsp; Personal access only, and a mobile design.&amp;nbsp; Basically start to implement &lt;a href=&quot;http://tantek.com/2014/084/b1/urls-people-focused-mobile-communication&quot;&gt;Tantek\'s Mobile Communication Ideas&lt;/a&gt;.&amp;nbsp; This would mean translating my current address book in (probably just some csv import so I have a start point too).&lt;/li&gt;\r\n	&lt;li&gt;Anything else you can suggest.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;What do you guys think I should work on next?&lt;/p&gt;\r\n','2014-04-25 15:27:35','OpenBlog Developement Still Going Strong',1,2014,4,25,2,'openblog_devlopement_still_going','article',NULL,NULL),(20,'','2014-05-20 17:20:40','',1,2014,5,20,1,NULL,'photo',NULL,'/image/uploaded/igoXu1Zk.jpg'),(21,'','2014-05-20 22:32:23','',1,2014,5,21,1,NULL,'photo',NULL,'/image/uploaded/igvGvTAI.jpg'),(22,'&lt;p&gt;I finally have some free time again and it is taking the form of some template updates.&lt;/p&gt;\r\n\r\n&lt;p&gt;While sempress worked for a quick solution (and I am still using a number of elements of it), I really need something new.&amp;nbsp; So I have begun work on a new layout.&amp;nbsp; Its not quite going to work the way I had originally imagined it, but I believe that it will suffice for now.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;More to come soon now that I\'m out of the weeds of my last project.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n','2014-07-24 15:24:55','A little free time and some new style',1,2014,7,24,1,'free_time_and_style','article',NULL,NULL),(23,'&lt;p&gt;I always seem to manage to find the limits of what is possible with CSS.&amp;nbsp; I have learned a ton of tricks to get around things, but there are just some things which CSS cannot do alone.&amp;nbsp; And I don\'t really want to go down the JS route.&amp;nbsp; Its possible but not very pretty.&amp;nbsp; So instead I spend an hour playing with potential new ways to organize my site... and none of them are really what I want.&lt;/p&gt;\r\n','2014-07-25 21:50:20','CSS Limits',1,2014,7,26,1,'css_limits','note',NULL,NULL),(24,'','2014-08-05 11:12:41','',1,2014,8,5,1,NULL,'photo',NULL,'/image/uploaded/igbbO33x.jpg'),(25,'Test of OwnYourGram Integration','2014-08-05 11:19:39','',1,2014,8,5,2,NULL,'photo',NULL,'/image/uploaded/igLEkVtw.jpg'),(26,'Life is hard for a papillion','2014-08-05 21:52:06','',1,2014,8,6,1,NULL,'photo',NULL,'/image/uploaded/igWAouWl.jpg'),(28,'&lt;p&gt;So you may notice development picking up again.&amp;nbsp; I finally said &quot;to hell with windows&quot; and put Linux on my laptop.&amp;nbsp; Now first, a little background.&lt;/p&gt;\r\n\r\n&lt;p&gt;I pretty much exclusively use Linux.&amp;nbsp; My home computer is Funtoo Linux (a more up to the minute variant of Gentoo).&amp;nbsp; And my work computer is Ubuntu (I would have used Gentoo or Funtoo as well if it weren\'t that I was already installed for me).&amp;nbsp; So why did I have a Windows laptop?&amp;nbsp; Well a few years back I started a consulting job and screen sharing in to some ridiculous machine on the other side of the country basically requiring a Windows machine with a large screen.&amp;nbsp; Thus I came to own this beast of a laptop.&amp;nbsp; As a side note, I hope I never have to deal with Microsoft Sharepoint EVER AGAIN.&lt;/p&gt;\r\n\r\n&lt;p&gt;Two years ago I stopped that consulting job and since then this laptop has basically sat unused.&amp;nbsp; It did however come in handy whenever I just needed a Windows machine.&amp;nbsp; Minor things usually, but over the last few months that has dwindled off to almost nothing.&amp;nbsp; So I decided that since I really don\'t want to deal with rebuilding everything on a laptop, I\'d just go with something simple.&amp;nbsp; I set aside several hours for install and set up the new system.&amp;nbsp; My impression of the whole process was &quot;WOW. They have made this easy now!&quot;&amp;nbsp; The install was fast and painless.&amp;nbsp; It knew I was installing on a laptop.&amp;nbsp; It connected to my wifi and waited asked me if I wanted to just do all the system updates as it installed.&amp;nbsp; It even knew that I had a Windows 7 installed and asked exactly what it wanted me to do with it.&amp;nbsp; Now, to be honest, I did cheat a little.&amp;nbsp; I did not use a CD and instead just DD\'d the image on to my flash drive.&amp;nbsp; So there was no lag at all in the install.&amp;nbsp; By the time the few hours were up I had just about everything set up, more or less how I wanted it.&lt;/p&gt;\r\n\r\n&lt;p&gt;So that takes us back to now and a laptop has basically become my IndieWebComp-uter.&lt;/p&gt;\r\n\r\n&lt;p&gt;So now that I have a development platform, I have a laundry list of items to get through in the code.&lt;/p&gt;\r\n\r\n&lt;p&gt;Obviously the site needs some design work.&amp;nbsp; I think I\'m just going to go back to the method I had originally planned and will just have two streams on the home page, The first will have images and notes only and will be on the left.&amp;nbsp; The right side will be larger and designed for longer form posts.&amp;nbsp; Future types will have to just be classified to either side.&amp;nbsp; But all that falls under templates.&amp;nbsp; I certainly welcome any contributions on that front.&lt;/p&gt;\r\n\r\n&lt;p&gt;Next I do plan to do a ton of work on the administration pages for openblog.&amp;nbsp; This work somewhat depresses me since its all work that won\'t really be seem by site users, but it really needs to be all sorted out.&lt;/p&gt;\r\n','2014-08-06 19:48:42','Goodbye <s>Pork Pie</s> Windows Hat',1,2014,8,6,2,'goodbye_windows_hat','article',NULL,NULL),(31,'This is an initial test of a nost posted via Quill (quill.p3k.io).  Lets see if I have all the bugs worked out.','2014-08-07 14:43:51','',1,2014,8,7,1,NULL,'note',NULL,NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webmentions`
--

LOCK TABLES `webmentions` WRITE;
/*!40000 ALTER TABLE `webmentions` DISABLE KEYS */;
INSERT INTO `webmentions` VALUES (4,'2014-04-08 11:51:07','http://waterpigs.co.uk/notes/4VUFr4/','http://ben.thatmustbe.me/post/2014/3/28/1/Hard_At_Work','OK',NULL,NULL,NULL,'200',2,NULL,NULL),(5,'2014-04-08 13:38:40','http://kylewm.com/like/2014/04/08/1','http://ben.thatmustbe.me/post/2014/3/28/1/Hard_At_Work','OK',NULL,NULL,NULL,'200',NULL,NULL,1),(6,'2014-04-08 13:47:39','https://snarfed.org/2014-04-08_8926','http://ben.thatmustbe.me/post/2014/3/28/1/Hard_At_Work','OK',NULL,NULL,NULL,'200',NULL,NULL,2),(7,'2014-04-08 13:48:44','https://snarfed.org/2014-04-08_8929','http://ben.thatmustbe.me/','OK',NULL,NULL,NULL,'200',NULL,NULL,3),(8,'2014-07-18 04:44:05','http://home.tylergillies.club/2014/07/18/testing-webmention/','http://ben.thatmustbe.me/post/2014/4/11/1/Webmention_Glory','queued',NULL,NULL,NULL,'202',NULL,NULL,NULL),(9,'2014-07-24 15:52:57','http://gregorlove.com/notes/2014/07/24/1/','http://ben.thatmustbe.me/post/2014/7/24/1/free_time_and_style','queued',NULL,NULL,NULL,'202',NULL,NULL,NULL);
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

-- Dump completed on 2014-08-13 16:36:40
