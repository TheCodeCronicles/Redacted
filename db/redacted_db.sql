-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: redacted_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comment_votes`
--

DROP TABLE IF EXISTS `comment_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_id` (`comment_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comment_votes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comment_votes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_votes`
--

LOCK TABLES `comment_votes` WRITE;
/*!40000 ALTER TABLE `comment_votes` DISABLE KEYS */;
INSERT INTO `comment_votes` VALUES (1,1,1,1,'2025-04-29 17:02:53'),(2,1,2,1,'2025-04-30 10:34:32'),(3,2,2,1,'2025-04-30 12:38:29'),(4,3,3,1,'2025-04-30 14:38:25'),(5,4,3,-1,'2025-04-30 14:38:41'),(6,4,5,1,'2025-05-01 09:10:30'),(7,6,1,1,'2025-05-01 13:15:42'),(8,7,1,1,'2025-05-01 13:16:30'),(9,9,5,1,'2025-05-01 13:47:29'),(10,6,5,1,'2025-05-01 14:45:00'),(11,5,5,1,'2025-05-01 14:49:28'),(12,8,5,1,'2025-05-01 15:09:00'),(13,7,5,1,'2025-05-01 15:30:36'),(14,10,5,1,'2025-05-01 17:05:12'),(15,12,5,-1,'2025-05-01 19:03:07'),(16,5,6,1,'2025-05-01 19:27:28'),(17,7,6,1,'2025-05-01 19:36:19'),(18,11,6,-1,'2025-05-01 19:38:10'),(19,13,4,1,'2025-05-02 06:58:59'),(20,11,4,-1,'2025-05-02 06:59:17'),(21,7,2,1,'2025-05-02 09:35:14'),(22,11,2,-1,'2025-05-02 09:35:25'),(23,17,2,1,'2025-05-05 19:39:20'),(24,12,2,1,'2025-05-05 19:47:10'),(25,8,2,1,'2025-05-05 19:49:18'),(26,15,2,1,'2025-05-05 20:00:48'),(27,5,2,1,'2025-05-05 20:01:17'),(28,21,2,1,'2025-05-05 20:09:00'),(31,3,2,1,'2025-05-05 20:22:09'),(32,4,2,1,'2025-05-05 20:22:23'),(33,20,2,1,'2025-05-05 20:27:31'),(34,23,2,1,'2025-05-05 20:45:38'),(35,24,2,1,'2025-05-05 20:45:52'),(36,27,1,1,'2025-05-05 21:25:53'),(37,15,1,1,'2025-05-05 21:27:51'),(38,22,1,1,'2025-05-05 21:43:04'),(39,31,1,1,'2025-06-20 06:48:27'),(40,11,1,1,'2025-06-20 06:51:21'),(41,35,4,1,'2025-06-20 07:32:13'),(42,18,4,1,'2025-06-20 07:34:18'),(43,9,4,1,'2025-06-20 07:39:38'),(44,38,4,1,'2025-06-20 09:09:56'),(47,27,4,1,'2025-06-20 09:45:15'),(48,39,4,1,'2025-06-20 09:48:12'),(49,5,4,1,'2025-06-20 09:59:08'),(50,40,4,1,'2025-06-20 10:31:36');
/*!40000 ALTER TABLE `comment_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,2,1,'Yooo this is fireee!!!','2025-04-29 14:57:19'),(2,2,2,'Pigeon?? Legendary!!!','2025-04-30 12:38:25'),(3,4,2,'🔥🔥🔥🔥','2025-04-30 13:07:49'),(4,4,3,'kwaai','2025-04-30 14:38:37'),(5,7,5,'🔥🔥🔥🔥','2025-05-01 08:58:26'),(6,7,4,'HEHEHE','2025-05-01 13:11:58'),(7,7,1,'This is [REDACTED]','2025-05-01 13:16:15'),(8,6,1,'404 This post is [REDACTED]','2025-05-01 13:31:29'),(9,8,5,'It\'s giving TRON x PACMAN','2025-05-01 13:47:26'),(10,8,5,'🔥🔥🔥🔥','2025-05-01 17:04:55'),(11,7,5,'No way this actually happened... right!?','2025-05-01 17:31:13'),(12,6,5,'Yooooo!','2025-05-01 17:37:18'),(13,7,6,'This is a loooooooooooooonnnnnnggggggggg coooooooommmmmmeeeennnnntttt!!!!','2025-05-01 19:27:20'),(14,16,2,'Me too, Me too','2025-05-05 19:22:33'),(15,23,2,'Mooiste Muisie Ooit!!','2025-05-05 19:35:27'),(16,17,2,'😔😔😔','2025-05-05 19:36:39'),(17,6,2,'Bruh','2025-05-05 19:38:03'),(18,7,2,'Fuckkkk Offff','2025-05-05 19:42:03'),(19,7,2,'Test','2025-05-05 19:42:50'),(20,12,2,'The ting go skrrrrraaaa!!','2025-05-05 19:57:46'),(21,8,2,'Nice','2025-05-05 20:08:56'),(22,40,2,'Woahhh','2025-05-05 20:42:38'),(23,38,2,'Hampter','2025-05-05 20:44:44'),(24,37,2,'Mannnn','2025-05-05 20:45:49'),(25,39,2,'You already know it','2025-05-05 20:56:57'),(26,34,1,'Hehe','2025-05-05 21:22:19'),(27,40,1,'[REDACTED]','2025-05-05 21:25:50'),(28,23,1,'🔥🔥🔥🔥','2025-05-05 21:27:47'),(29,28,1,'Oops...','2025-05-22 11:44:26'),(30,11,1,'WTF','2025-06-17 15:31:52'),(31,19,1,'Ayyyyy','2025-06-20 06:48:22'),(32,21,1,'Pfrrttt','2025-06-20 06:52:23'),(33,33,1,'XD','2025-06-20 06:52:41'),(34,13,1,'A feisty fella I tell ya','2025-06-20 06:52:59'),(35,36,1,'We always knew blackholes existed','2025-06-20 06:53:35'),(36,12,1,'The ting goes skrrrahh, pap, pap, ka-ka-ka Skibiki-pap-pap, and a pu-pu-pudrrrr-boom Skya, du-du-ku-ku-dun-dun Poom, poom','2025-06-20 07:00:57'),(37,7,4,'This is iconic','2025-06-20 07:43:02'),(38,34,4,'Heh he he','2025-06-20 07:56:25'),(39,14,4,'THE BASS AND THE TWEETERS MAKE THE SPEAKERS GO TO WAR!!!!','2025-06-20 09:38:16'),(40,3,4,'Wise Words','2025-06-20 10:31:29'),(41,41,1,'LENGEND!!!!','2025-06-20 12:05:14');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `followers`
--

DROP TABLE IF EXISTS `followers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `follower_id` (`follower_id`),
  KEY `following_id` (`following_id`),
  CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `followers`
--

LOCK TABLES `followers` WRITE;
/*!40000 ALTER TABLE `followers` DISABLE KEYS */;
INSERT INTO `followers` VALUES (5,5,1,'2025-05-02 16:26:26'),(7,5,2,'2025-05-03 08:01:34'),(8,1,2,'2025-05-03 08:02:06'),(9,1,5,'2025-05-03 08:02:12'),(10,6,2,'2025-05-03 08:02:53'),(14,4,2,'2025-05-03 08:54:32'),(15,2,7,'2025-05-05 18:47:33'),(16,2,8,'2025-05-05 18:56:50'),(18,1,7,'2025-06-17 11:43:33'),(19,4,5,'2025-06-20 07:39:53'),(20,4,3,'2025-06-20 07:45:17'),(22,4,7,'2025-06-20 09:10:08'),(23,4,1,'2025-06-20 09:49:08'),(24,4,8,'2025-06-20 10:47:34');
/*!40000 ALTER TABLE `followers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `is_upvote` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_topics`
--

DROP TABLE IF EXISTS `post_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_topics` (
  `post_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`topic_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `post_topics_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_topics_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_topics`
--

LOCK TABLES `post_topics` WRITE;
/*!40000 ALTER TABLE `post_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_votes`
--

DROP TABLE IF EXISTS `post_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vote` (`user_id`,`post_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `post_votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_votes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_votes`
--

LOCK TABLES `post_votes` WRITE;
/*!40000 ALTER TABLE `post_votes` DISABLE KEYS */;
INSERT INTO `post_votes` VALUES (1,1,1,1,'2025-04-29 09:14:51'),(8,1,2,1,'2025-04-29 17:02:52'),(13,2,2,1,'2025-04-30 10:34:43'),(31,2,3,1,'2025-04-30 11:26:26'),(32,1,3,1,'2025-04-30 11:26:43'),(43,2,1,1,'2025-04-30 12:46:10'),(50,2,4,1,'2025-04-30 13:07:18'),(51,3,4,1,'2025-04-30 14:38:28'),(52,3,3,1,'2025-04-30 14:38:59'),(53,3,5,1,'2025-04-30 18:37:02'),(56,3,6,1,'2025-04-30 18:52:45'),(57,2,7,1,'2025-05-01 06:50:56'),(58,5,8,1,'2025-05-01 08:58:11'),(59,5,3,1,'2025-05-01 09:00:52'),(60,5,7,1,'2025-05-01 09:01:15'),(61,5,6,1,'2025-05-01 09:01:20'),(62,5,5,1,'2025-05-01 09:10:22'),(65,1,7,1,'2025-05-01 13:16:37'),(66,1,6,1,'2025-05-01 13:31:38'),(82,6,8,1,'2025-05-01 19:28:08'),(84,6,7,1,'2025-05-01 19:28:21'),(85,6,6,1,'2025-05-01 19:38:02'),(86,4,6,-1,'2025-05-02 07:04:26'),(87,4,8,1,'2025-05-02 07:19:39'),(88,2,6,1,'2025-05-02 10:29:52'),(89,1,8,1,'2025-05-02 14:41:50'),(90,5,9,1,'2025-05-02 15:55:46'),(93,2,14,1,'2025-05-05 18:48:56'),(94,2,33,1,'2025-05-05 19:26:34'),(95,2,11,1,'2025-05-05 19:56:18'),(96,2,20,1,'2025-05-05 19:56:22'),(97,2,25,1,'2025-05-05 19:56:25'),(98,2,36,1,'2025-05-05 19:57:11'),(99,2,12,1,'2025-05-05 19:57:24'),(100,2,39,1,'2025-05-05 20:00:07'),(101,2,38,1,'2025-05-05 20:00:18'),(102,2,37,1,'2025-05-05 20:00:21'),(103,1,23,1,'2025-05-05 21:27:33'),(104,1,40,1,'2025-05-05 21:43:00'),(105,1,34,1,'2025-05-05 21:56:32'),(106,1,39,1,'2025-05-05 21:56:50'),(107,1,26,1,'2025-05-05 21:57:36'),(108,1,13,1,'2025-05-05 22:02:58'),(109,1,36,1,'2025-05-22 11:38:33'),(110,1,28,1,'2025-05-22 11:44:12'),(111,1,17,1,'2025-05-22 15:21:25'),(112,1,19,1,'2025-06-17 15:29:59'),(113,1,11,1,'2025-06-17 15:31:33'),(115,4,36,1,'2025-06-20 07:32:50'),(117,4,35,1,'2025-06-20 09:07:38'),(121,4,13,1,'2025-06-20 09:40:45'),(123,4,33,1,'2025-06-20 09:44:12'),(131,4,14,1,'2025-06-20 09:48:06'),(132,4,9,1,'2025-06-20 09:48:37'),(133,4,34,1,'2025-06-20 09:58:39'),(134,4,40,1,'2025-06-20 09:58:54'),(135,4,7,1,'2025-06-20 09:59:04'),(138,4,3,1,'2025-06-20 10:31:21'),(139,1,41,1,'2025-06-20 12:05:06');
/*!40000 ALTER TABLE `post_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'YOOOOOO','uploads/68109888504f3_Pigeon Mascot.png','2025-04-29 09:14:48'),(2,1,'We Are [Redacted]','uploads/6810e8bf126cf_Vogels met eleganse in tuxedo.png','2025-04-29 14:57:03'),(3,2,'When life gives you lemons, make lemonade!','uploads/68120034cc63d_181803c7fd180b360f97f1ab5f3d8963.jpg','2025-04-30 10:49:24'),(4,2,'Fake confidence and you\'ll find that it\'s real...',NULL,'2025-04-30 13:07:15'),(5,3,'Werk hierdie?',NULL,'2025-04-30 14:39:47'),(6,3,'Video Test','uploads/68127176f2bf1_Gif.mp4','2025-04-30 18:52:38'),(7,3,'Can\'t Park there mate','uploads/681274f37c5de_Cant park there.mp4','2025-04-30 19:07:31'),(8,5,'PACMAN','uploads/6813379ccf941_aisoW6.webp','2025-05-01 08:58:04'),(9,2,'...','uploads/68149e12c7ba3_nissan-skyline-gt-r-skyline-r34-nissan-gtr-r34-nissan-wallpaper-preview.jpg','2025-05-02 10:27:30'),(10,7,'Ouch!','uploads/6818f9ab3ccfb_WhatsApp Video 2025-05-01 at 16.26.43_1ad19171.mp4','2025-05-05 17:47:23'),(11,7,'What a view!','uploads/6818f9d4e31d2_WhatsApp Video 2025-05-01 at 16.27.18_6f7e172b.mp4','2025-05-05 17:48:04'),(12,7,'chicken','uploads/6818fa0c8e5b1_WhatsApp Video 2025-05-02 at 03.24.42_13ec1bad.mp4','2025-05-05 17:49:00'),(13,7,'Chihuahua\'s are insane','uploads/6818faab231c9_WhatsApp Video 2025-05-02 at 03.24.43_5b21511d.mp4','2025-05-05 17:51:39'),(14,7,'Timmys trumpet','uploads/6818fae969653_WhatsApp Video 2025-05-02 at 03.24.43_3744f9b0.mp4','2025-05-05 17:52:41'),(15,7,'Shakey wobbles','uploads/6818fb923a061_WhatsApp Video 2025-05-02 at 03.24.44_51d50d30.mp4','2025-05-05 17:55:30'),(16,7,'I dont want to work😔','uploads/6818fbb8954ea_WhatsApp Video 2025-05-02 at 03.24.47_b62822bb.mp4','2025-05-05 17:56:08'),(17,7,'Cashier > Models','uploads/6818fbd56b09f_WhatsApp Video 2025-05-02 at 03.24.47_ea9e75b1.mp4','2025-05-05 17:56:37'),(18,7,'Apex predator','uploads/6818fcb729883_WhatsApp Video 2025-05-02 at 03.24.57_1869173b.mp4','2025-05-05 18:00:23'),(19,7,'What in the bubushka?','uploads/6818fd0f04c63_WhatsApp Video 2025-05-02 at 03.25.35_0fef5d77.mp4','2025-05-05 18:01:51'),(20,7,'Are egypt memes still a thing?','uploads/6818fdb8ea768_WhatsApp Video 2025-05-02 at 03.25.35_e76c70d2.mp4','2025-05-05 18:04:40'),(21,7,'Eish','uploads/6818fe2c3a546_WhatsApp Video 2025-05-02 at 03.25.36_456ebabb.mp4','2025-05-05 18:06:36'),(22,7,'Most important muscle','uploads/6818fe524cca8_WhatsApp Video 2025-05-02 at 03.25.37_87e8a54d.mp4','2025-05-05 18:07:14'),(23,7,'Kurt Darren!!','uploads/6818fe679c54b_WhatsApp Video 2025-05-02 at 03.25.37_1264efec.mp4','2025-05-05 18:07:35'),(24,7,'Me too cat, me too','uploads/6818fe8133bbe_WhatsApp Video 2025-05-02 at 03.25.38_205f85d6.mp4','2025-05-05 18:08:01'),(25,7,'.','uploads/6818fec4d4ec2_WhatsApp Video 2025-05-02 at 03.25.39_2a7fab44.mp4','2025-05-05 18:09:08'),(26,7,'Bonk','uploads/6818fedd24c47_WhatsApp Video 2025-05-02 at 03.25.39_9c7e96ac.mp4','2025-05-05 18:09:33'),(27,7,'Its Friday','uploads/6818ff63c8b82_WhatsApp Video 2025-05-02 at 03.25.39_655f8177.mp4','2025-05-05 18:11:47'),(28,7,'Time for a new one','uploads/6818ff7a81054_WhatsApp Video 2025-05-02 at 03.25.39_6623e0b3.mp4','2025-05-05 18:12:10'),(29,8,'Sights of space','uploads/6819000eecfd9_WhatsApp Video 2025-05-02 at 03.25.40_2312b360.mp4','2025-05-05 18:14:38'),(30,8,'Stay and relax','uploads/68190064ad62d_WhatsApp Video 2025-05-02 at 03.25.40_9785274c.mp4','2025-05-05 18:16:04'),(31,8,'Amazing movie!','uploads/68190082e1c3a_WhatsApp Video 2025-05-02 at 03.25.41_be252b4c.mp4','2025-05-05 18:16:34'),(32,7,'Here\'s a video for the space guy','uploads/681900aad78bf_WhatsApp Video 2025-05-02 at 03.25.40_2ba3bf2b.mp4','2025-05-05 18:17:14'),(33,7,'GOAT','uploads/6819013875bd6_WhatsApp Video 2025-05-02 at 03.25.40_7f76d0d4.mp4','2025-05-05 18:19:36'),(34,7,'Bro stood up','uploads/681901593b216_WhatsApp Video 2025-05-02 at 03.25.41_14fa7b9b.mp4','2025-05-05 18:20:09'),(35,7,'What a good song','uploads/68190175333c7_WhatsApp Video 2025-05-02 at 03.25.41_64962171.mp4','2025-05-05 18:20:37'),(36,8,'More space','uploads/681901ec40190_WhatsApp Video 2025-05-02 at 03.25.41_ca9de378.mp4','2025-05-05 18:22:36'),(37,8,'Did I mention i like space😼','uploads/6819028ab849d_WhatsApp Video 2025-05-02 at 03.25.42_479e2efb.mp4','2025-05-05 18:25:14'),(38,7,'Hampter','uploads/681902c04094d_WhatsApp Video 2025-05-02 at 03.25.42_010129ef.mp4','2025-05-05 18:26:08'),(39,8,'Knowledge is both a blessing and curse','uploads/68190357b187c_WhatsApp Video 2025-05-02 at 03.25.42_a5d56267.mp4','2025-05-05 18:28:39'),(40,8,'\"Imagination is more important than knowledge\" - Albert Einstein','uploads/6819037c86ea9_WhatsApp Video 2025-05-02 at 03.25.43_7783ff5f.mp4','2025-05-05 18:29:16'),(41,4,'Chilling','uploads/68554894c89f1_CatLava.jpg','2025-06-20 11:40:04');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT 'assets/images/default.png',
  `bio` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'WeAre[Redacted]','weareredacted@gmail.com','$2y$10$zpsnHepbFKA3gK/Hi/V8sei6N8Rqzp/9Ab5W/w6DozA/ZoyX2Vani','assets/images/Redacted_Logo.png','How did we manage to have [] in our username you ask... well we\'re the creators','2025-04-29 09:14:33'),(2,'schroeder_fijanko','fijankosc@gmail.com','$2y$10$8Yh1SnupCE1Sa2MTDB9BjOutP.qrbe.1bcF8WAKXyAprWhOtxcY0S','assets/images/default.png','We Are The People','2025-04-30 10:34:28'),(3,'Anthony_van_der_Watt','antwatt2002@gmail.com','$2y$10$J3AkZSGKcvEn5cQ6U.0SoO6GQ0VLDIqugqmmHb3rNBLbplEFklbFq','assets/images/default.png','','2025-04-30 14:38:11'),(4,'UnknownChaos','fijanko321@gmail.com','$2y$10$XF9y.DRd56R6OUxH1hY4Ue8AZKF5CDypXmbVe1stIKZdN6LU6bK0S','uploads/avatars/68554f10e6556.jpg','On The Unhinged Side of Life','2025-05-01 08:40:18'),(5,'The_Unforgiving_Camel','test@gmail.com','$2y$10$6iFMKbtW92SDhqVTH4zit.MPTbXAZdDjV6mrMwB8BVHOgwY6bAgaq','assets/images/default.png','','2025-05-01 08:44:12'),(6,'LabTech','LabTech@gmail.com','$2y$10$SpM0u0CdpWvEpbkm.pcdtOT1c50gKswSKsZ2Sv2o..mSWll4Q1duK','assets/images/default.png','','2025-05-01 08:53:04'),(7,'Meme_Guy','meme@gmail.com','$2y$10$PEMEkbAn2VvYlgtmXbdnmuEdFwJ4SBPYWipJu/o3X5cHER9hyiZxK','assets/images/default.png','','2025-05-05 17:46:46'),(8,'Visuals_Page','visual@gmail.com','$2y$10$r0E7hNYKa97LE2SsdVC6LeaLG1MNr0D7m4EcsfH/aDZHY5D0QNafq','assets/images/default.png','','2025-05-05 18:14:02');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-20 21:08:52
