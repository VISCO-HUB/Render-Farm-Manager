CREATE DATABASE  IF NOT EXISTS `dr_manager` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `dr_manager`;
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
INSERT INTO `dr` VALUES ('kng-render-002',0,'7','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.123',NULL,NULL,'2017-04-19 17:58:31'),('kng-render-003',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.122',NULL,NULL,'2017-04-19 17:58:31'),('kng-render-004',0,'8','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.121',NULL,NULL,'2017-04-19 17:58:32'),('kng-render-005',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.120',NULL,NULL,'2017-04-19 17:58:29'),('kng-render-006',0,'8','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.119',NULL,NULL,'2017-04-19 17:58:32'),('kng-render-007',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.118',NULL,NULL,'2017-04-19 17:58:29'),('kng-render-008',0,'10','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.3.117',NULL,NULL,'2017-04-19 17:58:33'),('lviv-nb-mv',0,'96','VRaySpawner 2014=notfound;VRaySpawner 2016=notfound;BACKBURNER_SRV_200=notfound;DR_Corona=notfound;VRaySpawner 2017=notfound;=notfound;','192.168.0.120',NULL,NULL,'2017-03-13 12:54:28'),('lviv-renamd-001',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=1;VRaySpawner 2017=1;=notfound;','192.168.0.131',NULL,NULL,'2017-04-19 17:58:32'),('lviv-renamd-002',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=1;VRaySpawner 2017=1;=notfound;','192.168.0.145',NULL,NULL,'2017-04-19 17:58:33'),('lviv-render-001',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.51','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-002',0,'1','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.52','n.kit','rv','2017-04-19 17:58:31'),('lviv-render-003',0,'4','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.53','n.kit','rv','2017-04-19 17:58:32'),('lviv-render-004',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.54','n.kit','rv','2017-04-19 17:58:31'),('lviv-render-005',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.55','n.kit','rv','2017-04-19 17:58:33'),('lviv-render-006',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.56','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-007',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.57','n.kit','rv','2017-04-19 17:58:32'),('lviv-render-008',0,'4','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.58','n.kit','rv','2017-04-19 17:58:32'),('lviv-render-009',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.59','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-010',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.60','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-011',0,'10','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=1;VRaySpawner 2017=1;=notfound;','192.168.0.61',NULL,NULL,'2017-04-14 10:18:40'),('lviv-render-012',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.62','n.kit','rv','2017-04-19 17:58:32'),('lviv-render-013',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.63','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-014',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.64','n.kit','rv','2017-04-19 17:58:33'),('lviv-render-015',0,'7','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.65','n.kit','rv','2017-04-19 17:58:31'),('lviv-render-016',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.66','n.kit','rv','2017-04-19 17:58:31'),('lviv-render-017',0,'1','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.67','n.kit','rv','2017-04-19 17:58:32'),('lviv-render-018',0,'1','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.68','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-019',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.69','n.kit','rv','2017-04-19 17:58:31'),('lviv-render-020',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.70','n.kit','rv','2017-04-19 17:58:33'),('lviv-render-021',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.71','n.kit','rv','2017-04-19 17:58:33'),('lviv-render-022',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.72','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-023',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.73','n.kit','rv','2017-04-19 17:58:28'),('lviv-render-024',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.74','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-025',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.75','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-026',0,'8','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.76','n.kit','rv','2017-04-19 17:58:32'),('lviv-render-027',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.77','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-028',0,'3','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.78','n.kit','rv','2017-04-19 17:58:28'),('lviv-render-029',0,'7','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.79','n.kit','rv','2017-04-19 17:58:33'),('lviv-render-030',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.80','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-031',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.81','n.kit','rv','2017-04-19 17:58:29'),('lviv-render-032',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.82','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-033',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.83','n.kit','rv','2017-04-19 17:58:30'),('lviv-render-034',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=1;DR_Corona=1;VRaySpawner 2017=4;=notfound;','192.168.0.84','n.kit','rv','2017-04-19 17:58:34'),('max3d004',0,'1','VRaySpawner 2014=notfound;VRaySpawner 2016=notfound;DR Corona=notfound;BACKBURNER_SRV_200=1;=notfound;','192.168.0.111',NULL,NULL,'2016-10-10 14:31:26'),('max3d026',0,'26','VRaySpawner 2014=notfound;VRaySpawner 2016=notfound;BACKBURNER_SRV_200=notfound;DR_Corona=notfound;VRaySpawner 2017=notfound;=notfound;','192.168.0.160',NULL,NULL,'2017-03-13 12:47:31'),('oslo-render-001',0,'26','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=notfound;=notfound;','192.168.4.115',NULL,NULL,'2017-04-19 17:58:34'),('oslo-render-002',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=notfound;=notfound;','192.168.4.114',NULL,NULL,'2017-04-19 17:58:31'),('oslo-render-004',0,'21','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=notfound;=notfound;','192.168.4.124',NULL,NULL,'2017-04-19 14:58:17'),('oslo-render-005',0,'27','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=notfound;=notfound;','192.168.4.123',NULL,NULL,'2017-04-19 14:57:38'),('svg-render-001',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.71',NULL,NULL,'2017-04-19 17:58:33'),('svg-render-002',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.72',NULL,NULL,'2017-04-19 17:58:29'),('svg-render-003',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.73',NULL,NULL,'2017-04-19 17:58:31'),('svg-render-004',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.74',NULL,NULL,'2017-04-19 17:58:34'),('svg-render-005',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.75',NULL,NULL,'2017-04-19 17:58:31'),('svg-render-006',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.76',NULL,NULL,'2017-04-19 17:58:30'),('svg-render-007',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.77',NULL,NULL,'2017-04-19 17:58:33'),('svg-render-008',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.78',NULL,NULL,'2017-04-19 17:58:32'),('svg-render-009',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.79',NULL,NULL,'2017-04-19 17:58:34'),('svg-render-010',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.80',NULL,NULL,'2017-04-19 17:58:34'),('svg-render-011',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.81',NULL,NULL,'2017-04-19 17:58:28'),('svg-render-012',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.82',NULL,NULL,'2017-04-19 17:58:30'),('svg-render-013',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.83',NULL,NULL,'2017-04-19 17:58:31'),('svg-render-014',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.84',NULL,NULL,'2017-04-19 17:58:34'),('svg-render-015',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.85',NULL,NULL,'2017-04-19 17:58:29'),('svg-render-016',0,'0','VRaySpawner 2014=1;VRaySpawner 2016=1;BACKBURNER_SRV_200=4;DR_Corona=notfound;VRaySpawner 2017=1;=notfound;','192.168.1.86',NULL,NULL,'2017-04-19 17:58:29');
/*!40000 ALTER TABLE `dr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global`
--

DROP TABLE IF EXISTS `global`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global` (
  `name` varchar(45) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global`
--

LOCK TABLES `global` WRITE;
/*!40000 ALTER TABLE `global` DISABLE KEYS */;
INSERT INTO `global` VALUES ('email','0'),('idle','60'),('message','Offline'),('notify','0'),('status','1');
/*!40000 ALTER TABLE `global` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'VRaySpawner 2014',0),(2,'VRaySpawner 2016',0),(8,'BACKBURNER_SRV_200',0),(9,'DR_Corona',0),(11,'VRaySpawner 2017',0);
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
INSERT INTO `users` VALUES ('a.chupryna','192.168.0.167','22445e44c0a0a312b8987b60ab5779ff','192.168.0.52',0),('A.havrylin','193.84.22.46','22445e44c0a0a312b8987b60ab5779ff','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.60',0),('a.pavlyuk','','','192.168.0.61|192.168.0.62|192.168.0.63|192.168.0.64|192.168.0.65|192.168.0.66|192.168.0.69|192.168.0.68|192.168.0.67|192.168.0.70|192.168.0.71',0),('d.klishch','193.84.22.46','22445e44c0a0a312b8987b60ab5779ff','192.168.0.53',0),('e.astafiev','193.84.22.46','b5f09223940fecfece73a298045ee267','192.168.3.123|192.168.3.122',1),('e.chernopiskyi','82.117.234.5','f7c40710caf0800d3d45d28798407938',NULL,0),('g.kupriychuk','192.168.0.155','22445e44c0a0a312b8987b60ab5779ff','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55',0),('i.bodak','192.168.0.145','22445e44c0a0a312b8987b60ab5779ff','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.145|192.168.0.131',0),('i.koziy','','','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.60',0),('jrender','192.168.0.84','c4ca4238a0b923820dcc509a6f75849b','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.63|192.168.0.64|192.168.0.65|192.168.0.66|192.168.0.62|192.168.0.61|192.168.0.60|192.168.0.67|192.168.0.68|192.168.0.69|192.168.0.70|192.168.0.81|192.168.0.82|192.168.0.80|192.168.0.79|192.168.0.72|192.168.0.73|192.168.0.74|192.168.0.75|192.168.0.76|192.168.0.77|192.168.0.78|192.168.0.84|192.168.0.83',0),('k.marev','193.84.22.46','22445e44c0a0a312b8987b60ab5779ff','192.168.3.123|192.168.3.122',0),('le.eide','192.168.1.1','025af11703a50b74034db74349f49d3f',NULL,0),('m.assachia','193.84.22.46','2f74e8fecd3ff85f20974ede9dfb4c6d','192.168.0.56|192.168.0.66|192.168.0.72',0),('n.kit','193.84.22.46','d8578edf8458ce06fbc5bb76a58c5ca4','192.168.0.68|192.168.0.67|192.168.0.66',1),('o.berget','80.241.87.210','2af9b1ba42dc5eb01743e6b3759b6e4b',NULL,0),('o.kosyanchuk','193.84.22.46','b5f09223940fecfece73a298045ee267','192.168.1.80|192.168.1.81|192.168.1.75',0),('o.labintsev','193.84.22.46','22445e44c0a0a312b8987b60ab5779ff','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.58|192.168.0.59|192.168.0.57|192.168.0.60',0),('o.olshanskyi','193.84.22.46','22445e44c0a0a312b8987b60ab5779ff','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56',0),('o.stasevych','192.168.0.139','0a721eb4617ef5a81f66a46b932bc5c6','192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.60|192.168.0.61|192.168.0.62|192.168.0.63|192.168.0.64|192.168.0.65|192.168.0.66|192.168.0.71|192.168.0.72|192.168.0.73',0),('r.khomyn','193.84.22.46','f70d668f0d51d10c18c39ebba2b5e82a',NULL,1),('s.timonov','','','192.168.0.51|192.168.0.53|192.168.0.52|192.168.0.54|192.168.0.55',0),('tCukisan','193.84.22.46','81fcc27b392cb0539b8c2fb343a5bd47','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.58|192.168.0.59|192.168.0.60|192.168.0.61|192.168.0.62|192.168.0.63|192.168.0.64|192.168.0.65|192.168.0.66|192.168.0.67',0),('v.lukyanenko','193.84.22.46','c4ca4238a0b923820dcc509a6f75849b','192.168.3.123|192.168.3.122',1),('v.melnykovych','193.84.22.46','a160f3129b419e3d179b2cd309c4b4c1','192.168.0.57',1),('v.terletskyi','92.112.225.3','46295c07dd673555f8497e6161a2ca7e','192.168.0.54|192.168.0.55',0),('v.zabolotnyi','192.168.0.134','1a78531add46af6c780fc542e5a36f82','192.168.0.81|192.168.0.80|192.168.0.79|192.168.0.78|192.168.0.77|192.168.0.76|192.168.0.75|192.168.0.74|192.168.0.73|192.168.0.72|192.168.0.71|192.168.0.70|192.168.0.69|192.168.0.68|192.168.0.67|192.168.0.66|192.168.0.65|192.168.0.64|192.168.0.62|192.168.0.61|192.168.0.63|192.168.0.82|192.168.0.83',0),('y.bozhyk','193.84.22.46','46295c07dd673555f8497e6161a2ca7e','192.168.3.121|192.168.3.118|192.168.3.117',0),('y.shyryy','193.84.22.46','22445e44c0a0a312b8987b60ab5779ff','192.168.0.51|192.168.0.52|192.168.0.53|192.168.0.54|192.168.0.55|192.168.0.56|192.168.0.57|192.168.0.65|192.168.0.64|192.168.0.63|192.168.0.62|192.168.0.61|192.168.0.60|192.168.0.59|192.168.0.58',0);
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

-- Dump completed on 2017-04-19 17:58:34
