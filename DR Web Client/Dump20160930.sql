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
INSERT INTO `dr` VALUES ('lviv-render-001',1,'9','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.51',NULL,'1','2016-09-30 12:30:49'),('lviv-render-002',0,'1','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.52',NULL,'1','2016-09-30 12:29:18'),('lviv-render-003',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.53',NULL,'1','2016-09-30 12:29:21'),('lviv-render-004',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.54',NULL,'1','2016-09-30 12:29:23'),('lviv-render-005',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.55',NULL,'1','2016-09-30 12:29:24'),('lviv-render-006',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.56',NULL,'1','2016-09-30 12:29:27'),('lviv-render-007',0,'4','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.57',NULL,'1','2016-09-30 12:29:33'),('lviv-render-008',0,'1','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.58',NULL,'1','2016-09-30 12:29:37'),('lviv-render-009',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.59',NULL,'1','2016-09-30 12:29:39'),('lviv-render-010',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;DR Corona=notfound;=notfound;','192.168.0.60',NULL,'1','2016-09-30 12:29:43'),('max3d026',0,'8','VRaySpawner 2014=notfound;VRaySpawner 2016=notfound;=notfound;','192.168.0.126',' ','1','2016-09-29 11:05:08');
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
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'VRaySpawner 2014',0),(2,'VRaySpawner 2016',0),(3,'DR Corona',0);
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
  `rights` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('v.lukyanenko','193.84.22.46','c4ca4238a0b923820dcc509a6f75849b','192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.51',1),('v.terletskyi','193.84.22.46','46295c07dd673555f8497e6161a2ca7e','192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.51|192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.60',0);
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

-- Dump completed on 2016-09-30 17:16:11
