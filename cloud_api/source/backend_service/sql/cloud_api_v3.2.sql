CREATE DATABASE  IF NOT EXISTS `cloud_api` /*!40100 DEFAULT CHARACTER SET utf8 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cloud_api`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cloud_api
-- ------------------------------------------------------
-- Server version	8.0.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `detectionresulttable`
--

DROP TABLE IF EXISTS `detectionresulttable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detectionresulttable` (
  `detection_id` int NOT NULL AUTO_INCREMENT,
  `image_id` int NOT NULL,
  `crack_coordinates` text NOT NULL,
  `confidence_score` float DEFAULT NULL,
  `severity_level` varchar(50) DEFAULT NULL,
  `annotated_image_path` varchar(255) DEFAULT NULL,
  `detection_time` datetime NOT NULL,
  PRIMARY KEY (`detection_id`),
  KEY `detectiontable_fk` (`image_id`),
  CONSTRAINT `detectiontable_fk` FOREIGN KEY (`image_id`) REFERENCES `imagetable` (`image_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detectionresulttable`
--

LOCK TABLES `detectionresulttable` WRITE;
/*!40000 ALTER TABLE `detectionresulttable` DISABLE KEYS */;
INSERT INTO `detectionresulttable` VALUES (7001,5001,'[{\"x1\":100,\"y1\":200,\"x2\":150,\"y2\":250}]',0.95,'high','/detections/detection_7001.jpg','2023-10-01 10:00:00');
/*!40000 ALTER TABLE `detectionresulttable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_flight_area`
--

DROP TABLE IF EXISTS `device_flight_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_flight_area` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `device_sn` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `file_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sync_status` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sync_code` int NOT NULL DEFAULT '0',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_flight_area`
--

LOCK TABLES `device_flight_area` WRITE;
/*!40000 ALTER TABLE `device_flight_area` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_flight_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dronetable`
--

DROP TABLE IF EXISTS `dronetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dronetable` (
  `drone_data_id` int NOT NULL AUTO_INCREMENT,
  `mission_id` int NOT NULL,
  `timestamp` datetime NOT NULL,
  `coordinates` varchar(255) NOT NULL,
  `altitude` float DEFAULT NULL,
  `speed` float DEFAULT NULL,
  PRIMARY KEY (`drone_data_id`),
  KEY `dronetable_fk` (`mission_id`),
  CONSTRAINT `dronetable_fk` FOREIGN KEY (`mission_id`) REFERENCES `missiontable` (`mission_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dronetable`
--

LOCK TABLES `dronetable` WRITE;
/*!40000 ALTER TABLE `dronetable` DISABLE KEYS */;
INSERT INTO `dronetable` VALUES (3001,1001,'2023-10-01 09:05:00','22.3193,114.1694',50.5,5.2);
/*!40000 ALTER TABLE `dronetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_area_file`
--

DROP TABLE IF EXISTS `flight_area_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_area_file` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `object_key` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sign` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'sha256',
  `size` int NOT NULL,
  `latest` tinyint(1) NOT NULL COMMENT 'The latest version？',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI_FILE_ID` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_area_file`
--

LOCK TABLES `flight_area_file` WRITE;
/*!40000 ALTER TABLE `flight_area_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_area_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flight_area_property`
--

DROP TABLE IF EXISTS `flight_area_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_area_property` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `element_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'dfence/nfz',
  `enable` tinyint(1) NOT NULL,
  `sub_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'options: Circle ',
  `radius` int NOT NULL DEFAULT '0' COMMENT 'unit: cm',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNI_AREA_ID` (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight_area_property`
--

LOCK TABLES `flight_area_property` WRITE;
/*!40000 ALTER TABLE `flight_area_property` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight_area_property` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagetable`
--

DROP TABLE IF EXISTS `imagetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagetable` (
  `image_id` int NOT NULL,
  `mission_id` int NOT NULL,
  `original_image_path` varchar(255) NOT NULL,
  `processed_image_path` varchar(255) DEFAULT NULL,
  `upload_time` datetime NOT NULL,
  `detection_status` varchar(50) NOT NULL DEFAULT 'pending',
  `drone_coordinates` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `imagetable_fk` (`mission_id`),
  CONSTRAINT `imagetable_fk` FOREIGN KEY (`mission_id`) REFERENCES `missiontable` (`mission_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagetable`
--

LOCK TABLES `imagetable` WRITE;
/*!40000 ALTER TABLE `imagetable` DISABLE KEYS */;
INSERT INTO `imagetable` VALUES (5001,1001,'/uploads/mission_001/original/img1.jpg','/uploads/mission_001/processed/img1_annotated.jpg','2023-10-01 09:15:00','processed','22.3193,114.1694');
/*!40000 ALTER TABLE `imagetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loginrecord`
--

DROP TABLE IF EXISTS `loginrecord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loginrecord` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ipAddr` varchar(15) NOT NULL,
  `lastLogin` int NOT NULL,
  `result` tinyint(1) NOT NULL,
  `retryCount` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf16;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loginrecord`
--

LOCK TABLES `loginrecord` WRITE;
/*!40000 ALTER TABLE `loginrecord` DISABLE KEYS */;
INSERT INTO `loginrecord` VALUES (6,'::1',1738936158,1,1);
/*!40000 ALTER TABLE `loginrecord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_file`
--

DROP TABLE IF EXISTS `logs_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_file` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the file in the bucket.',
  `size` bigint NOT NULL DEFAULT '0' COMMENT 'file size',
  `logs_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The logs_id in the manage_device_logs table.',
  `device_sn` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The sn of the device.',
  `fingerprint` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'file fingerprint',
  `object_key` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The key of the file in the bucket.',
  `status` tinyint(1) NOT NULL COMMENT 'Whether the upload was successful. 1: success; 0: failed;',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_id_UNIQUE` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Logs file information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_file`
--

LOCK TABLES `logs_file` WRITE;
/*!40000 ALTER TABLE `logs_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_file_index`
--

DROP TABLE IF EXISTS `logs_file_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_file_index` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `boot_index` int NOT NULL COMMENT 'The file index reported by the dock.',
  `file_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The file_id in the logs_file table.',
  `start_time` bigint NOT NULL COMMENT 'The file start time reported by the dock.',
  `end_time` bigint NOT NULL COMMENT 'The file end time reported by the dock.',
  `size` bigint NOT NULL COMMENT 'The file size reported by the dock.',
  `device_sn` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The sn of the device.',
  `domain` int NOT NULL COMMENT 'This parameter corresponds to the domain in the device dictionary table.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='The boot index table corresponding to the logs file.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_file_index`
--

LOCK TABLES `logs_file_index` WRITE;
/*!40000 ALTER TABLE `logs_file_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_file_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_device`
--

DROP TABLE IF EXISTS `manage_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_device` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `device_sn` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'dock, drone, remote control',
  `device_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'undefined' COMMENT 'model of the device. This parameter corresponds to the device name in the device dictionary table.',
  `user_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The account used when the device was bound.',
  `nickname` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'custom name of the device',
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The workspace to which the current device belongs.',
  `device_type` int NOT NULL DEFAULT '-1' COMMENT 'This parameter corresponds to the device type in the device dictionary table.',
  `sub_type` int NOT NULL DEFAULT '-1' COMMENT 'This parameter corresponds to the sub type in the device dictionary table.',
  `domain` int NOT NULL DEFAULT '-1' COMMENT 'This parameter corresponds to the domain in the device dictionary table.',
  `firmware_version` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'firmware version of the device',
  `compatible_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: consistent; 0: inconsistent; Whether the firmware versions are consistent.',
  `version` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'version of the protocol. This field is currently not useful.',
  `device_index` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'Control of the drone, A control or B control.',
  `child_sn` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The device controlled by the gateway.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  `bound_time` bigint DEFAULT NULL COMMENT 'The time when the device is bound to the workspace.',
  `bound_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'The status when the device is bound to the workspace. 1: bound; 0: not bound;',
  `login_time` bigint DEFAULT NULL COMMENT 'The time of the last device login.',
  `device_desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `url_normal` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The icon displayed on the remote control.',
  `url_select` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The icon displayed on the remote control when it is selected.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_sn_UNIQUE` (`device_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COMMENT='Device information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_device`
--

LOCK TABLES `manage_device` WRITE;
/*!40000 ALTER TABLE `manage_device` DISABLE KEYS */;
INSERT INTO `manage_device` VALUES (2,'5YSZM7G0032451','DJI RC Pro','be7c6c3d-afe9-4be4-b9eb-c55066c0914e','DJI RC Pro','e3dea0f5-37f2-4d79-ae58-490af3228069',144,0,2,'02.01.0529',1,'1.2.0','A','1581F5FHC247D00D3R4S',1743582955558,1744984982624,1744981595195,1,1744984981336,'Remote control for Mavic 3E/T and Mavic 3M','',''),(3,'1581F5FHC247D00D3R4S','Mavic 3E','be7c6c3d-afe9-4be4-b9eb-c55066c0914e','Mavic 3E','e3dea0f5-37f2-4d79-ae58-490af3228069',77,0,0,'12.15.0601',1,'1.2.0','','',1743582955622,1744984982625,1743582966727,1,1744984981406,'','','');
/*!40000 ALTER TABLE `manage_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_device_dictionary`
--

DROP TABLE IF EXISTS `manage_device_dictionary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_device_dictionary` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domain` int NOT NULL COMMENT 'This parameter corresponds to the domain in the Product Type section of the document. 0: drone; 1: payload; 2: remote control; 3: dock;',
  `device_type` int NOT NULL COMMENT 'This parameter corresponds to the type in the Product Type section of the document.',
  `sub_type` int NOT NULL COMMENT 'This parameter corresponds to the sub_type in the Product Type section of the document.',
  `device_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'This parameter corresponds to the name in the Product Type section of the document.',
  `device_desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'remark',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3 COMMENT='Device product enum';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_device_dictionary`
--

LOCK TABLES `manage_device_dictionary` WRITE;
/*!40000 ALTER TABLE `manage_device_dictionary` DISABLE KEYS */;
INSERT INTO `manage_device_dictionary` VALUES (1,0,60,0,'Matrice 300 RTK',NULL),(2,0,67,0,'Matrice 30',NULL),(3,0,67,1,'Matrice 30T',NULL),(4,1,20,0,'Z30',NULL),(5,1,26,0,'XT2',NULL),(6,1,39,0,'FPV',NULL),(7,1,41,0,'XTS',NULL),(8,1,42,0,'H20',NULL),(9,1,43,0,'H20T',NULL),(10,1,50,65535,'P1','include 24 and 35 and 50mm'),(11,1,52,0,'M30 Camera',NULL),(12,1,53,0,'M30T Camera',NULL),(13,1,61,0,'H20N',NULL),(14,1,165,0,'DJI Dock Camera',NULL),(15,1,90742,0,'L1',NULL),(16,2,56,0,'DJI Smart Controller','Remote control for M300'),(17,2,119,0,'DJI RC Plus','Remote control for M30'),(18,3,1,0,'DJI Dock',''),(19,0,77,0,'Mavic 3E',NULL),(20,0,77,1,'Mavic 3T',NULL),(21,1,66,0,'Mavic 3E Camera',NULL),(22,1,67,0,'Mavic 3T Camera',NULL),(23,2,144,0,'DJI RC Pro','Remote control for Mavic 3E/T and Mavic 3M'),(24,0,77,2,'Mavic 3M',NULL),(25,1,68,0,'Mavic 3M Camera',NULL),(26,0,89,0,'Matrice 350 RTK',NULL),(27,3,2,0,'DJI Dock2',NULL),(28,0,91,0,'M3D',NULL),(29,0,91,1,'M3TD',NULL),(30,1,80,0,'M3D Camera',NULL),(31,1,81,0,'M3TD Camera',NULL);
/*!40000 ALTER TABLE `manage_device_dictionary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_device_firmware`
--

DROP TABLE IF EXISTS `manage_device_firmware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_device_firmware` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firmware_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `file_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'undefined' COMMENT 'The file name of the firmware package, including the file suffix',
  `firmware_version` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'It needs to be formatted according to the official firmware version. 00.00.0000',
  `object_key` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'The object key of the firmware package in the bucket.',
  `file_size` bigint NOT NULL COMMENT 'The size of the firmware package.',
  `file_md5` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The md5 of the firmware package.',
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `release_note` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The release note of the firmware package.',
  `release_date` bigint NOT NULL COMMENT 'The release date of the firmware package.',
  `user_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'The name of the creator.',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Availability of the firmware package. 1: available; 0: unavailable',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_firmware_id` (`firmware_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Firmware file information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_device_firmware`
--

LOCK TABLES `manage_device_firmware` WRITE;
/*!40000 ALTER TABLE `manage_device_firmware` DISABLE KEYS */;
/*!40000 ALTER TABLE `manage_device_firmware` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_device_hms`
--

DROP TABLE IF EXISTS `manage_device_hms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_device_hms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hms_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `tid` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The tid when the device reports the hms message.',
  `bid` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The bid when the device reports the hms message.',
  `sn` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Which device reported the message.',
  `level` smallint NOT NULL COMMENT 'hms level. 0: notice; 1: caution; 2: warning.',
  `module` tinyint NOT NULL COMMENT 'Which module''s message. 0: flight task; 1:device manage; 2: media; 3: hms.',
  `hms_key` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The key of the hms message, according to which the message text is obtained.',
  `message_zh` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Chinese message.',
  `message_en` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'English message.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_hms_id` (`hms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Device''s hms information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_device_hms`
--

LOCK TABLES `manage_device_hms` WRITE;
/*!40000 ALTER TABLE `manage_device_hms` DISABLE KEYS */;
/*!40000 ALTER TABLE `manage_device_hms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_device_logs`
--

DROP TABLE IF EXISTS `manage_device_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_device_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `logs_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the creator.',
  `device_sn` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The sn of the device.',
  `logs_info` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'A description of the log issue.',
  `happen_time` bigint DEFAULT NULL COMMENT 'The time when the logging problem occurred.',
  `status` tinyint NOT NULL COMMENT '1: uploading; 2: done 3: canceled; 4: failed;',
  `update_time` bigint NOT NULL,
  `create_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_id_UNIQUE` (`logs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Log for uploading logs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_device_logs`
--

LOCK TABLES `manage_device_logs` WRITE;
/*!40000 ALTER TABLE `manage_device_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `manage_device_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_device_payload`
--

DROP TABLE IF EXISTS `manage_device_payload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_device_payload` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payload_sn` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The sn of the device payload.',
  `payload_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'undefined' COMMENT 'model of the payload. This parameter corresponds to the device name in the device dictionary table.',
  `payload_type` smallint NOT NULL COMMENT 'This parameter corresponds to the device type in the device dictionary table.',
  `sub_type` smallint NOT NULL COMMENT 'This parameter corresponds to the sub type in the device dictionary table.',
  `firmware_version` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'firmware version of the device payload',
  `payload_index` smallint NOT NULL COMMENT 'The location of the payload on the device.',
  `device_sn` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Which device the current payload belongs to.',
  `payload_desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `control_source` varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payload_sn_UNIQUE` (`payload_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COMMENT='The payload information of the device.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_device_payload`
--

LOCK TABLES `manage_device_payload` WRITE;
/*!40000 ALTER TABLE `manage_device_payload` DISABLE KEYS */;
INSERT INTO `manage_device_payload` VALUES (12,'1581F5FHC247D00D3R4S-0','Mavic 3E Camera',66,0,'11.09.02.33',0,'1581F5FHC247D00D3R4S',NULL,'A',1744059237437,1744984982683);
/*!40000 ALTER TABLE `manage_device_payload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_firmware_model`
--

DROP TABLE IF EXISTS `manage_firmware_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_firmware_model` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firmware_id` varchar(64) NOT NULL,
  `device_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'model of the device. This parameter corresponds to the device name in the device dictionary table.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_firmware_model`
--

LOCK TABLES `manage_firmware_model` WRITE;
/*!40000 ALTER TABLE `manage_firmware_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `manage_firmware_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_user`
--

DROP TABLE IF EXISTS `manage_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the account.',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The password of the account.',
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Which workspace the current account belongs to.',
  `user_type` smallint NOT NULL COMMENT 'The type of account. Different sides need to be logged in with the corresponding type of account. 1: web; 2: pilot.',
  `mqtt_username` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The account name used by the current account when logging into the emqx server.',
  `mqtt_password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The account password used by the current account when logging into the emqx server.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COMMENT='System account.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_user`
--

LOCK TABLES `manage_user` WRITE;
/*!40000 ALTER TABLE `manage_user` DISABLE KEYS */;
INSERT INTO `manage_user` VALUES (1,'a1559e7c-8dd8-4780-b952-100cc4797da2','adminPC','adminPC','e3dea0f5-37f2-4d79-ae58-490af3228069',1,'admin','admin',1634898410751,1650880112310),(2,'be7c6c3d-afe9-4be4-b9eb-c55066c0914e','pilot','pilot123','e3dea0f5-37f2-4d79-ae58-490af3228069',2,'pilot','pilot123',1634898410751,1634898410751);
/*!40000 ALTER TABLE `manage_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manage_workspace`
--

DROP TABLE IF EXISTS `manage_workspace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manage_workspace` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `workspace_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the workspace.',
  `workspace_desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The description of the workspace.',
  `platform_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The platform name of the workspace.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  `bind_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The binding code for this workspace is required when the dock connects to a third-party cloud.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspace_id_UNIQUE` (`workspace_id`),
  UNIQUE KEY `bind_code_UNIQUE` (`bind_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manage_workspace`
--

LOCK TABLES `manage_workspace` WRITE;
/*!40000 ALTER TABLE `manage_workspace` DISABLE KEYS */;
INSERT INTO `manage_workspace` VALUES (1,'e3dea0f5-37f2-4d79-ae58-490af3228069','Test Group One','Cloud Sample Test Platform','Cloud Api Platform',1634898410751,1634898410751,'qwe');
/*!40000 ALTER TABLE `manage_workspace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_element_coordinate`
--

DROP TABLE IF EXISTS `map_element_coordinate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_element_coordinate` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `element_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The element_id in the logs_file table.',
  `longitude` decimal(18,14) NOT NULL COMMENT 'The longitude of this element.',
  `latitude` decimal(17,14) NOT NULL COMMENT 'The latitude of this element.',
  `altitude` decimal(17,14) DEFAULT NULL COMMENT 'The altitude of this element. If the element is point, it is null.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='The coordinate information corresponding to the element.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_element_coordinate`
--

LOCK TABLES `map_element_coordinate` WRITE;
/*!40000 ALTER TABLE `map_element_coordinate` DISABLE KEYS */;
/*!40000 ALTER TABLE `map_element_coordinate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_group`
--

DROP TABLE IF EXISTS `map_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `group_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the group.',
  `group_type` int NOT NULL COMMENT 'The type of the group. 0: custome; 1: default; 2: app shared; see developer document for detail.',
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The workspace_id in the manage_workspace table.',
  `is_distributed` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'element group distributed status. Only data with value 1 is displayed on the pilot map. 1: true; 0: false.',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether to lock. If locked, the elements under this element group cannot be deleted and modified. 1: locked; 0: unlock.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_id_UNIQUE` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin COMMENT='The group information of the map element.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_group`
--

LOCK TABLES `map_group` WRITE;
/*!40000 ALTER TABLE `map_group` DISABLE KEYS */;
INSERT INTO `map_group` VALUES (1,'e3dea0f5-37f2-4d79-ae58-490af3228060','Pilot Share Layer',2,'e3dea0f5-37f2-4d79-ae58-490af3228069',1,0,1638330077356,1638330077356),(2,'e3dea0f5-37f2-4d79-ae58-490af3228011','Default Layer',1,'e3dea0f5-37f2-4d79-ae58-490af3228069',1,0,1638330077356,1638330077356),(3,'d58479c8-4a80-4036-aa55-8beffb7158e9','Custom Flight Area',0,'e3dea0f5-37f2-4d79-ae58-490af3228069',1,0,1638330077356,1638330077356);
/*!40000 ALTER TABLE `map_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map_group_element`
--

DROP TABLE IF EXISTS `map_group_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `map_group_element` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `element_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `element_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the element.',
  `display` smallint NOT NULL DEFAULT '1' COMMENT 'It no longer works.',
  `group_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The group_id in the map_group table.',
  `element_type` smallint NOT NULL COMMENT 'element type. 0: point; 1: line; 2: polygon.',
  `username` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the creator.',
  `color` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The color of the element. Hexadecimal.',
  `clamp_to_ground` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether it is on the ground. 1: true; 0: false.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `element_id_UNIQUE` (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin COMMENT='Information about the element corresponding to the group.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map_group_element`
--

LOCK TABLES `map_group_element` WRITE;
/*!40000 ALTER TABLE `map_group_element` DISABLE KEYS */;
/*!40000 ALTER TABLE `map_group_element` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maplabeltable`
--

DROP TABLE IF EXISTS `maplabeltable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maplabeltable` (
  `label_id` int NOT NULL,
  `mission_id` int NOT NULL,
  `label_coordinates` varchar(255) NOT NULL,
  `label_type` varchar(50) NOT NULL,
  `description` text,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maplabeltable`
--

LOCK TABLES `maplabeltable` WRITE;
/*!40000 ALTER TABLE `maplabeltable` DISABLE KEYS */;
INSERT INTO `maplabeltable` VALUES (9001,1001,'22.3195,114.1696','critical_zone','Major crack area',230001,'2023-10-01 10:15:00');
/*!40000 ALTER TABLE `maplabeltable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_file`
--

DROP TABLE IF EXISTS `media_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_file` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `file_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The original name of the file.',
  `file_path` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The path of the file.',
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The workspace to which the file belongs.',
  `fingerprint` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The fingerprint of the file. This property exists only for media files uploaded by Pilot.',
  `tinny_fingerprint` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The tiny fingerprint of the file. This property exists only for media files uploaded by Pilot.',
  `object_key` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The key of the file in the bucket.',
  `sub_file_type` int DEFAULT NULL COMMENT 'This property exists only for image files uploaded by Pilot. 0: normal picture; 1: panorama.',
  `is_original` tinyint(1) NOT NULL COMMENT 'Whether is the original image.',
  `drone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'undefined' COMMENT 'The sn of the drone which create the file.',
  `payload` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'undefined' COMMENT 'The name of the drone payload which create the file.',
  `job_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'The job_id in the wayline_job table. Whether the file belongs to the dock task.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_file_id` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COMMENT='Media file information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_file`
--

LOCK TABLES `media_file` WRITE;
/*!40000 ALTER TABLE `media_file` DISABLE KEYS */;
INSERT INTO `media_file` VALUES (1,'fd1f88ab-ef77-4c1e-b6d1-6a4b0d4ed3f1','DJI_20250403220715_0001_V.JPG','DJI_202504032152_019_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','D8C7C290971FBFCC60059FF0167B90DA','b81a9796f616a996b85015f9b360890a_2025_4_3_22_7_16','drone/DJI_20250403220715_0001_V.JPG',0,1,'','Mavic 3E Camera','',1744048690174,1744048690174),(2,'b1c5437d-9a4c-4144-b6de-7e972676e4d6','DJI_20250408003838_0001_V.JPG','DJI_202504080036_020_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','CBCBC19A5B2E62992C68C242E32489FA','06b7af05d18755b7b4b532be7f9a3a23_2025_4_8_0_38_38','drone/DJI_20250408003838_0001_V.JPG',0,1,'','Mavic 3E Camera','',1744048690761,1744048690761),(3,'bf8593ca-7ee2-4c4a-82e9-dc781debbd85','DJI_20250408045458_0001_V.JPG','DJI_202504080453_021_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','C9F0836264521A9393755E9C3C280DCA','bff36b892c47a95c03873fec2796838e_2025_4_8_4_54_58','drone/DJI_20250408045458_0001_V.JPG',0,1,'1581F5FHC247D00D3R4S','Mavic 3E Camera','',1744059331157,1744059331157),(4,'8a1c9676-822a-4ca6-b025-5ee4c9e3c3af','DJI_20250418210703_0001_V.JPG','DJI_202504182106_028_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','8A67B71F3385B7DA2B76C0436ED1D093','b918dc97d022fef5bc7491dd2368c202_2025_4_18_21_7_4','drone-auto-upload/DJI_20250418210703_0001_V.JPG',0,0,'1581F5FHC247D00D3R4S','Mavic 3E Camera','',1744982565724,1744982565724),(5,'8ab39b26-347b-418e-b8a3-b21cbf33c8e5','DJI_20250418213617_0001_V.JPG','DJI_202504182106_029_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','2514915E891E77C4634F4EF279EA5A56','2876d9a123012ed1a3139f1b4c795c43_2025_4_18_21_36_18','drone-auto-upload/DJI_20250418213617_0001_V.JPG',0,1,'1581F5FHC247D00D3R4S','Mavic 3E Camera','',1744983388413,1744983388414),(6,'5fdf007e-4051-4bd1-84f3-079ddd2a7374','DJI_20250418214817_0002_V.JPG','DJI_202504182106_029_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','C003D553E3913C904961E78B0713E62C','cb4365c55a0ff7648f58e496f1ab6ae4_2025_4_18_21_48_18','drone-auto-upload/DJI_20250418214817_0002_V.JPG',0,1,'1581F5FHC247D00D3R4S','Mavic 3E Camera','',1744984120360,1744984120360),(7,'b5169e84-1ab9-4457-98bb-564727f53db1','DJI_20250418214825_0003_V.JPG','DJI_202504182106_029_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','E06DC53775DF591B082A250783146A70','145fcf1858621ae37847bc9eeb666ae8_2025_4_18_21_48_24','drone-auto-upload/DJI_20250418214825_0003_V.JPG',0,1,'1581F5FHC247D00D3R4S','Mavic 3E Camera','',1744984131596,1744984131596),(8,'2c3a91e8-9031-49bb-b844-93b33f9df896','DJI_20250418220328_0004_V.JPG','DJI_202504182106_029_Test-Group-One','e3dea0f5-37f2-4d79-ae58-490af3228069','4FDA5B2E46C00FA5E257775A9CEBC224','9eb6b9e31d09c3de350e392a0baffe24_2025_4_18_22_3_28','drone-auto-upload/DJI_20250418220328_0004_V.JPG',0,1,'1581F5FHC247D00D3R4S','Mavic 3E Camera','',1744985027407,1744985027407);
/*!40000 ALTER TABLE `media_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_mission_ref`
--

DROP TABLE IF EXISTS `media_mission_ref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_mission_ref` (
  `file_name` varchar(100) NOT NULL,
  `mission_id` int DEFAULT NULL,
  `longitude` decimal(18,14) DEFAULT NULL COMMENT 'The longitude of this element.',
  `latitude` decimal(17,14) DEFAULT NULL COMMENT 'The latitude of this element.',
  `altitude` decimal(17,14) DEFAULT NULL COMMENT 'The altitude of this element. If the element is point, it is null.',
  `relative_altitude` decimal(17,14) DEFAULT NULL,
  `gimbal_yaw_degree` double DEFAULT NULL,
  PRIMARY KEY (`file_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_mission_ref`
--

LOCK TABLES `media_mission_ref` WRITE;
/*!40000 ALTER TABLE `media_mission_ref` DISABLE KEYS */;
INSERT INTO `media_mission_ref` VALUES ('DJI_20250408045458_0001_V.JPG',1003,NULL,NULL,NULL,NULL,NULL),('DJI_20250418220328_0004_V.JPG',1003,0.00000000000000,0.00000000000000,136.74300000000000,0.00000000000000,16.8);
/*!40000 ALTER TABLE `media_mission_ref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missiontable`
--

DROP TABLE IF EXISTS `missiontable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `missiontable` (
  `mission_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `device_sn` varchar(32) DEFAULT NULL,
  `mission_name` varchar(255) NOT NULL,
  `mission_Flight_Route` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'planned',
  `notes` text,
  PRIMARY KEY (`mission_id`),
  KEY `missiontable_fk` (`user_id`),
  CONSTRAINT `missiontable_fk` FOREIGN KEY (`user_id`) REFERENCES `usertable` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missiontable`
--

LOCK TABLES `missiontable` WRITE;
/*!40000 ALTER TABLE `missiontable` DISABLE KEYS */;
INSERT INTO `missiontable` VALUES (1001,230001,NULL,'Building A Inspection',NULL,'22.3193,114.1694','2023-10-01 09:00:00','2023-10-01 10:30:00','completed','Initial inspection'),(1003,230002,'1581F5FHC247D00D3R4S','Building C Inspection',NULL,'22.25349, 113.90445','2025-02-07 00:07:00',NULL,'planned',NULL),(1004,230001,'','Solar Panel Audit',NULL,'22.3351,114.1782','2023-10-03 08:30:00','2023-10-03 10:00:00','planned','Annual maintenance check'),(1005,230001,'','Solar Panel Audit',NULL,'22.3351,114.1782','2023-10-03 08:30:00','2023-10-03 10:00:00','planned','Annual maintenance check'),(1006,230002,'','Building D Inspection',NULL,'22.3193,114.1694','2025-02-07 01:37:00',NULL,'planned',NULL);
/*!40000 ALTER TABLE `missiontable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usertable`
--

DROP TABLE IF EXISTS `usertable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usertable` (
  `user_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` int NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usertable`
--

LOCK TABLES `usertable` WRITE;
/*!40000 ALTER TABLE `usertable` DISABLE KEYS */;
INSERT INTO `usertable` VALUES (230001,'test','$2y$10$gPmO.8ILG.PMcc7UkdQGbuL7kX3nqoAOsKWS9WREJNBLY39DFXGEy','Chan Siu Ming','test@tset.com',1),(230002,'peter','$2y$10$gPmO.8ILG.PMcc7UkdQGbuL7kX3nqoAOsKWS9WREJNBLY39DFXGEy','haha','test2@test.com',1);
/*!40000 ALTER TABLE `usertable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wayline_file`
--

DROP TABLE IF EXISTS `wayline_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wayline_file` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'wayline name',
  `wayline_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `drone_model_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'device product enum. format: domain-device_type-sub_type',
  `payload_model_keys` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'payload product enum. format: domain-device_type-sub_type',
  `workspace_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Which workspace the current wayline belongs to.',
  `sign` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The md5 of the wayline file.',
  `favorited` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the file is favorited or not.',
  `template_types` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'wayline file template type. 0: waypoint;',
  `object_key` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The key of the file in the bucket.',
  `user_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the creator.',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL COMMENT 'required, can''t modify.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `wayline_id_UNIQUE` (`wayline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Wayline file information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wayline_file`
--

LOCK TABLES `wayline_file` WRITE;
/*!40000 ALTER TABLE `wayline_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `wayline_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wayline_job`
--

DROP TABLE IF EXISTS `wayline_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wayline_job` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'uuid',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the job.',
  `file_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The wayline file used for this job.',
  `dock_sn` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Which dock executes the job.',
  `workspace_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Which workspace the current job belongs to.',
  `task_type` int NOT NULL,
  `wayline_type` int NOT NULL COMMENT 'The template type of the wayline.',
  `execute_time` bigint DEFAULT NULL COMMENT 'actual begin time',
  `completed_time` bigint DEFAULT NULL COMMENT 'actual end time',
  `username` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the creator.',
  `begin_time` bigint NOT NULL COMMENT 'planned begin time',
  `end_time` bigint DEFAULT NULL COMMENT 'planned end time',
  `error_code` int DEFAULT NULL,
  `status` int NOT NULL COMMENT '1: pending; 2: in progress; 3: success; 4: cancel; 5: failed',
  `rth_altitude` int NOT NULL COMMENT 'return to home altitude. min: 20m; max: 500m',
  `out_of_control` int NOT NULL COMMENT 'out of control action. 0: go home; 1: hover; 2: landing;',
  `media_count` int NOT NULL DEFAULT '0',
  `create_time` bigint NOT NULL,
  `update_time` bigint NOT NULL,
  `parent_id` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_id_UNIQUE` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Wayline mission information of the dock.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wayline_job`
--

LOCK TABLES `wayline_job` WRITE;
/*!40000 ALTER TABLE `wayline_job` DISABLE KEYS */;
/*!40000 ALTER TABLE `wayline_job` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-18 22:08:35
