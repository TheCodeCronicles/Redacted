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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_votes`
--

LOCK TABLES `comment_votes` WRITE;
/*!40000 ALTER TABLE `comment_votes` DISABLE KEYS */;
INSERT INTO `comment_votes` VALUES (1,1,1,1,'2025-04-29 17:02:53'),(2,1,2,1,'2025-04-30 10:34:32'),(3,2,2,1,'2025-04-30 12:38:29'),(4,3,3,1,'2025-04-30 14:38:25'),(5,4,3,-1,'2025-04-30 14:38:41'),(6,4,5,1,'2025-05-01 09:10:30'),(7,6,1,1,'2025-05-01 13:15:42'),(8,7,1,1,'2025-05-01 13:16:30'),(9,9,5,1,'2025-05-01 13:47:29'),(10,6,5,1,'2025-05-01 14:45:00'),(11,5,5,1,'2025-05-01 14:49:28'),(12,8,5,1,'2025-05-01 15:09:00'),(13,7,5,1,'2025-05-01 15:30:36'),(14,10,5,1,'2025-05-01 17:05:12'),(15,12,5,-1,'2025-05-01 19:03:07'),(16,5,6,1,'2025-05-01 19:27:28'),(17,7,6,1,'2025-05-01 19:36:19'),(18,11,6,-1,'2025-05-01 19:38:10'),(19,13,4,1,'2025-05-02 06:58:59'),(20,11,4,-1,'2025-05-02 06:59:17'),(21,7,2,1,'2025-05-02 09:35:14'),(22,11,2,-1,'2025-05-02 09:35:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,2,1,'Yooo this is fireee!!!','2025-04-29 14:57:19'),(2,2,2,'Pigeon?? Legendary!!!','2025-04-30 12:38:25'),(3,4,2,'🔥🔥🔥🔥','2025-04-30 13:07:49'),(4,4,3,'kwaai','2025-04-30 14:38:37'),(5,7,5,'🔥🔥🔥🔥','2025-05-01 08:58:26'),(6,7,4,'HEHEHE','2025-05-01 13:11:58'),(7,7,1,'This is [REDACTED]','2025-05-01 13:16:15'),(8,6,1,'404 This post is [REDACTED]','2025-05-01 13:31:29'),(9,8,5,'It\'s giving TRON x PACMAN','2025-05-01 13:47:26'),(10,8,5,'🔥🔥🔥🔥','2025-05-01 17:04:55'),(11,7,5,'No way this actually happened... right!?','2025-05-01 17:31:13'),(12,6,5,'Yooooo!','2025-05-01 17:37:18'),(13,7,6,'This is a loooooooooooooonnnnnnggggggggg coooooooommmmmmeeeennnnntttt!!!!','2025-05-01 19:27:20');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `followers`
--

LOCK TABLES `followers` WRITE;
/*!40000 ALTER TABLE `followers` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_votes`
--

LOCK TABLES `post_votes` WRITE;
/*!40000 ALTER TABLE `post_votes` DISABLE KEYS */;
INSERT INTO `post_votes` VALUES (1,1,1,1,'2025-04-29 09:14:51'),(8,1,2,1,'2025-04-29 17:02:52'),(13,2,2,1,'2025-04-30 10:34:43'),(31,2,3,1,'2025-04-30 11:26:26'),(32,1,3,1,'2025-04-30 11:26:43'),(43,2,1,1,'2025-04-30 12:46:10'),(50,2,4,1,'2025-04-30 13:07:18'),(51,3,4,1,'2025-04-30 14:38:28'),(52,3,3,1,'2025-04-30 14:38:59'),(53,3,5,1,'2025-04-30 18:37:02'),(56,3,6,1,'2025-04-30 18:52:45'),(57,2,7,1,'2025-05-01 06:50:56'),(58,5,8,1,'2025-05-01 08:58:11'),(59,5,3,1,'2025-05-01 09:00:52'),(60,5,7,1,'2025-05-01 09:01:15'),(61,5,6,1,'2025-05-01 09:01:20'),(62,5,5,1,'2025-05-01 09:10:22'),(64,4,7,1,'2025-05-01 12:50:11'),(65,1,7,1,'2025-05-01 13:16:37'),(66,1,6,1,'2025-05-01 13:31:38'),(82,6,8,1,'2025-05-01 19:28:08'),(84,6,7,1,'2025-05-01 19:28:21'),(85,6,6,1,'2025-05-01 19:38:02'),(86,4,6,1,'2025-05-02 07:04:26'),(87,4,8,1,'2025-05-02 07:19:39'),(88,2,6,1,'2025-05-02 10:29:52');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'YOOOOOO','uploads/68109888504f3_Pigeon Mascot.png','2025-04-29 09:14:48'),(2,1,'We Are [Redacted]','uploads/6810e8bf126cf_Vogels met eleganse in tuxedo.png','2025-04-29 14:57:03'),(3,2,'When life gives you lemons, make lemonade!','uploads/68120034cc63d_181803c7fd180b360f97f1ab5f3d8963.jpg','2025-04-30 10:49:24'),(4,2,'Fake confidence and you\'ll find that it\'s real...',NULL,'2025-04-30 13:07:15'),(5,3,'Werk hierdie?',NULL,'2025-04-30 14:39:47'),(6,3,'Video Test','uploads/68127176f2bf1_Gif.mp4','2025-04-30 18:52:38'),(7,3,'Can\'t Park there mate','uploads/681274f37c5de_Cant park there.mp4','2025-04-30 19:07:31'),(8,5,'PACMAN','uploads/6813379ccf941_aisoW6.webp','2025-05-01 08:58:04'),(9,2,'...','uploads/68149e12c7ba3_nissan-skyline-gt-r-skyline-r34-nissan-gtr-r34-nissan-wallpaper-preview.jpg','2025-05-02 10:27:30');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'WeAre[Redacted]','weareredacted@gmail.com','$2y$10$zpsnHepbFKA3gK/Hi/V8sei6N8Rqzp/9Ab5W/w6DozA/ZoyX2Vani','assets/images/Redacted_Logo.png','How did we manage to have [] in our username you ask... well we\'re the creators','2025-04-29 09:14:33'),(2,'schroeder_fijanko','fijankosc@gmail.com','$2y$10$8Yh1SnupCE1Sa2MTDB9BjOutP.qrbe.1bcF8WAKXyAprWhOtxcY0S','assets/images/default.png','We Are The People','2025-04-30 10:34:28'),(3,'Anthony_van_der_Watt','antwatt2002@gmail.com','$2y$10$qxCcfhaeP.qpUwfInmZKt.Ph6za0js1ga7Me.mRjPBZlqJ13hRsQS','assets/images/default.png','','2025-04-30 14:38:11'),(4,'UnknownChaos','fijanko321@gmail.com','$2y$10$XF9y.DRd56R6OUxH1hY4Ue8AZKF5CDypXmbVe1stIKZdN6LU6bK0S','assets/images/default.png','','2025-05-01 08:40:18'),(5,'The_Unforgiving_Camel','test@gmail.com','$2y$10$6iFMKbtW92SDhqVTH4zit.MPTbXAZdDjV6mrMwB8BVHOgwY6bAgaq','assets/images/default.png','','2025-05-01 08:44:12'),(6,'LabTech','LabTech@gmail.com','$2y$10$SpM0u0CdpWvEpbkm.pcdtOT1c50gKswSKsZ2Sv2o..mSWll4Q1duK','assets/images/default.png','','2025-05-01 08:53:04');
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

-- Dump completed on 2025-05-02 14:24:39
