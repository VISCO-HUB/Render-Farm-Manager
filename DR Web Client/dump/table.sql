-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: dr_manager
-- ------------------------------------------------------
-- Server version	5.7.14-log

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
-- Table structure for table `dr`
--

DROP TABLE IF EXISTS `dr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dr` (
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `cpu` varchar(45) DEFAULT NULL,
  `services` varchar(3000) DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `user` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dr`
--

LOCK TABLES `dr` WRITE;
/*!40000 ALTER TABLE `dr` DISABLE KEYS */;
INSERT INTO `dr` VALUES ('lviv-render-001',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;=notfound;','192.168.0.51',NULL,NULL,'2016-09-28 14:23:35'),('lviv-render-002',0,'0','VRaySpawner 2014=4;VRaySpawner 2016=1;=notfound;','192.168.0.52',NULL,NULL,'2016-09-28 10:27:54'),('lviv-render-003',0,'5','VRaySpawner 2014=1;VRaySpawner 2016=1;=notfound;','192.168.0.53',NULL,NULL,'2016-09-28 14:23:36'),('lviv-render-004',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;=notfound;','192.168.0.54',NULL,NULL,'2016-09-28 14:23:35'),('lviv-render-005',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;=notfound;','192.168.0.55',NULL,NULL,'2016-09-28 14:23:35'),('lviv-render-006',0,'17','VRaySpawner 2014=4;VRaySpawner 2016=1;=notfound;','192.168.0.56',NULL,NULL,'2016-09-28 14:23:35'),('lviv-render-007',0,'0','VRaySpawner 2014=4;VRaySpawner 2016=1;=notfound;','192.168.0.57',NULL,NULL,'2016-09-28 14:23:35'),('lviv-render-008',0,'1','VRaySpawner 2014=4;VRaySpawner 2016=1;=notfound;','192.168.0.58',NULL,NULL,'2016-09-28 14:23:35'),('lviv-render-009',0,'0','VRaySpawner 2014=4;VRaySpawner 2016=1;=notfound;','192.168.0.59',NULL,NULL,'2016-09-28 14:23:36'),('lviv-render-010',0,'1','VRaySpawner 2014=4;VRaySpawner 2016=1;=notfound;','192.168.0.60',NULL,NULL,'2016-09-28 14:23:35'),('max3d026',0,'12','VRaySpawner 2014=notfound;VRaySpawner 2016=notfound;=notfound;','192.168.0.126',NULL,NULL,'2016-09-28 11:06:15');
/*!40000 ALTER TABLE `dr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'VRaySpawner 2014'),(2,'VRaySpawner 2016');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `lastnodes` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('v.lukyanenko','193.84.22.46','c4ca4238a0b923820dcc509a6f75849b','192.168.0.60|192.168.0.51|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.58|192.168.0.59');
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

-- Dump completed on 2016-09-28 14:23:36
