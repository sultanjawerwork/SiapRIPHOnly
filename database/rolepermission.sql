-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: localhost    Database: lasimethris04
-- ------------------------------------------------------
-- Server version	8.0.31

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
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  KEY `role_id_fk_6464585` (`role_id`),
  KEY `permission_id_fk_6464585` (`permission_id`),
  CONSTRAINT `permission_id_fk_6464585` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_id_fk_6464585` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(2,19),(2,20),(2,21),(3,21),(3,19),(3,20),(1,20),(4,21),(4,20),(4,24),(4,19),(4,27),(4,28),(4,29),(4,30),(4,32),(4,31),(4,33),(4,34),(4,39),(4,40),(4,41),(4,42),(2,39),(2,40),(2,41),(2,42),(3,43),(3,44),(3,46),(1,47),(1,48),(1,51),(1,53),(1,54),(1,56),(1,55),(1,57),(1,49),(1,50),(1,52),(3,47),(3,48),(3,49),(3,51),(3,50),(3,52),(3,53),(3,54),(3,56),(3,55),(3,57),(1,66),(1,75),(1,76),(1,77),(1,78),(1,79),(1,80),(1,81),(1,59),(1,60),(1,62),(1,61),(1,63),(1,82),(1,21),(4,47),(4,48),(4,51),(4,53),(4,54),(4,56),(4,55),(4,57),(2,47),(2,48),(2,51),(2,53),(2,54),(2,56),(2,55),(2,57),(1,84),(1,85),(1,83),(1,86),(4,89),(4,90),(2,27),(2,99),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(1,45),(1,58),(1,64),(1,67),(1,69),(1,68),(1,70),(1,65),(1,71),(1,73),(1,72),(1,74),(1,96),(1,92),(1,93),(1,94),(1,95),(1,35),(1,37),(1,36),(1,38),(2,25),(2,29),(2,30),(2,32),(2,31),(2,33),(1,46),(3,58),(3,59),(3,60),(3,62),(3,61),(3,63),(3,64),(3,67),(3,69),(3,68),(3,70),(3,65),(3,71),(3,73),(3,72),(3,74),(4,97),(4,99),(4,98),(4,100),(2,28),(2,34),(4,88),(4,101),(2,24),(4,26),(2,102),(2,103),(2,104),(2,106),(2,105),(2,107),(4,103),(4,102),(4,104),(4,106),(4,105),(4,107),(5,21),(5,20),(5,19),(1,34),(1,109),(1,110),(1,111),(1,112),(1,113),(5,82),(5,58),(5,59),(5,60),(5,62),(5,61),(5,63),(5,64),(5,67),(5,69),(5,68),(5,70),(5,65),(5,71),(5,73),(5,72),(5,74),(5,35),(5,37),(5,36),(5,38),(1,114),(1,115),(1,117),(1,116),(1,118),(2,114),(2,117),(2,97),(2,98),(2,100),(2,87),(2,26),(2,89),(2,88),(2,78),(2,90);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `perm_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `grp_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'user_management_access','5','User management',NULL,'2022-10-05 00:43:13',NULL),(2,'permission_create','1','Permissions',NULL,'2022-10-05 00:47:01',NULL),(3,'permission_edit','2','Permissions',NULL,'2022-10-05 00:46:35',NULL),(4,'permission_show','3','Permissions',NULL,'2022-10-05 00:46:24',NULL),(5,'permission_delete','4','Permissions',NULL,'2022-10-05 00:46:47',NULL),(6,'permission_access','5','Permissions',NULL,'2022-10-05 00:47:13',NULL),(7,'role_create','1','Roles',NULL,'2022-10-05 00:45:23',NULL),(8,'role_edit','2','Roles',NULL,'2022-10-05 00:44:56',NULL),(9,'role_show','3','Roles',NULL,'2022-10-05 00:44:46',NULL),(10,'role_delete','4','Roles',NULL,'2022-10-05 00:45:05',NULL),(11,'role_access','5','Roles',NULL,'2022-10-05 00:45:42',NULL),(12,'user_create','1','Users',NULL,'2022-10-05 00:44:10',NULL),(13,'user_edit','2','Users',NULL,'2022-10-05 00:43:41',NULL),(14,'user_show','3','Users',NULL,'2022-10-05 00:43:04',NULL),(15,'user_delete','4','Users',NULL,'2022-10-05 00:43:53',NULL),(16,'user_access','5','Users',NULL,'2022-10-05 00:44:36',NULL),(17,'audit_log_show','3','Audit Logs',NULL,'2022-10-05 00:47:23',NULL),(18,'audit_log_access','5','Audit Logs',NULL,'2022-10-05 00:47:34',NULL),(19,'profile_password_edit','2','Profile',NULL,'2022-10-05 00:46:09',NULL),(20,'dashboard_access','5','Dashboard','2022-10-05 01:18:49','2022-10-13 08:32:35',NULL),(21,'landing_access','5','Landing','2022-10-05 08:03:17','2022-10-13 08:32:49',NULL),(22,'dashboardv_access','5','Dashboard Verifikator','2022-10-06 23:52:04','2022-10-13 08:34:10','2022-10-13 08:34:10'),(23,'dashboarda_access','5','Dashboard Admin','2022-10-06 23:52:32','2022-10-13 08:34:17','2022-10-13 08:34:17'),(24,'user_task_access','5','Proses RIPH','2022-10-13 08:46:06','2023-02-25 16:53:01',NULL),(25,'pull_access','5','Tarik Data RIPH','2022-10-13 09:16:43','2023-02-25 16:50:47',NULL),(26,'commitment_access','5','Daftar RIPH','2022-10-13 09:17:05','2023-02-25 17:21:56',NULL),(27,'kelompoktani_access','5','Kelompok Tani','2022-11-02 07:29:05','2022-11-02 07:29:05',NULL),(28,'permohonan_access','5','Permohonan','2022-11-02 23:39:24','2023-02-25 17:05:59',NULL),(29,'pengajuan_access','5','Pengajuan Verifikasi','2022-11-02 23:42:35','2023-02-25 16:51:52',NULL),(30,'pengajuan_create','1','Pengajuan Verifikasi','2022-11-02 23:42:55','2023-06-01 20:39:58',NULL),(31,'pengajuan_edit','2','Pengajuan Verifikasi','2022-11-02 23:47:01','2023-02-25 16:51:32',NULL),(32,'pengajuan_show','3','Pengajuan Verifikasi','2022-11-02 23:47:19','2023-02-25 16:51:20',NULL),(33,'pengajuan_delete','4','Pengajuan Verifikasi','2022-11-02 23:47:35','2023-06-01 20:41:10',NULL),(34,'skl_access','5','SKL Terbit','2022-11-02 23:47:53','2023-02-25 17:07:07',NULL),(35,'skl_create','1','Daftar SKL','2022-11-02 23:48:10','2022-11-02 23:48:10',NULL),(36,'skl_edit','2','Daftar SKL','2022-11-02 23:48:24','2022-11-02 23:48:24',NULL),(37,'skl_show','3','Daftar SKL','2022-11-02 23:48:40','2022-11-02 23:48:40',NULL),(38,'skl_delete','4','Daftar SKL','2022-11-02 23:48:54','2022-11-02 23:48:54',NULL),(39,'folder_access','5','Pengelolaan Berkas','2022-11-03 00:43:51','2022-11-03 00:43:51',NULL),(40,'berkas_access','5','Berkas Saya','2022-11-03 00:44:05','2022-11-03 00:44:05',NULL),(41,'galeri_access','5','Galeri Saya','2022-11-03 00:44:19','2022-11-03 00:44:19',NULL),(42,'template_access','5','Template Master','2022-11-03 00:44:35','2022-11-03 00:44:35',NULL),(43,'onfarm_access','5','Onfarm','2022-11-05 08:12:36','2022-11-05 08:12:36',NULL),(44,'online_access','5','Online','2022-11-05 08:12:53','2022-11-05 08:12:53',NULL),(45,'completed_access','5','Completed','2022-11-05 08:13:11','2022-11-05 08:13:11',NULL),(46,'verificator_task_access','5','Verificator task','2022-11-24 08:44:34','2022-11-24 08:44:34',NULL),(47,'feedmsg_access','5','Feed & Messages','2022-11-25 04:09:04','2022-11-25 04:09:04',NULL),(48,'feeds_access','5','Feeds','2022-11-25 04:16:25','2022-11-25 04:16:25',NULL),(49,'feeds_create','1','Feeds','2022-11-25 04:17:07','2022-11-25 04:17:07',NULL),(50,'feeds_edit','2','Feeds','2022-11-25 04:17:26','2022-11-25 04:17:26',NULL),(51,'feeds_show','3','Feeds','2022-11-25 04:17:47','2022-11-25 04:17:47',NULL),(52,'feeds_delete','4','Feeds','2022-11-25 04:19:47','2022-11-25 04:19:47',NULL),(53,'messenger_access','5','Messenger','2022-11-25 04:22:42','2022-11-25 04:22:42',NULL),(54,'messenger_create','1','Messenger','2022-11-25 04:22:58','2022-11-25 04:22:58',NULL),(55,'messenger_edit','2','Messenger','2022-11-25 04:23:13','2022-11-25 04:23:13',NULL),(56,'messenger_show','3','Messenger','2022-11-25 04:23:27','2022-11-25 04:23:27',NULL),(57,'messenger_delete','4','Messenger','2022-11-25 04:23:45','2022-11-25 04:23:45',NULL),(58,'verification_skl_access','5','Verificator SKL','2022-11-26 09:44:37','2022-11-26 09:44:37',NULL),(59,'list_skl_access','5','SKL List','2022-11-26 09:51:36','2022-11-26 09:51:36',NULL),(60,'list_skl_create','1','SKL List','2022-11-26 09:51:57','2022-11-26 09:51:57',NULL),(61,'list_skl_edit','2','SKL List','2022-11-26 09:52:15','2022-11-26 09:52:15',NULL),(62,'list_skl_show','3','SKL List','2022-11-26 09:52:33','2022-11-26 09:52:33',NULL),(63,'list_skl_delete','4','SKL List','2022-11-26 09:52:50','2022-11-26 09:52:50',NULL),(64,'create_skl_access','5','Create SKL','2022-11-26 09:59:53','2022-11-26 09:59:53',NULL),(65,'issued_skl_access','5','Issued SKL','2022-11-26 10:00:11','2022-11-26 10:00:11',NULL),(66,'administrator_access','5','Administrator','2022-11-28 21:58:04','2022-11-28 21:58:04',NULL),(67,'create_skl_create','1','Create SKL','2022-11-28 23:02:46','2022-11-28 23:02:46',NULL),(68,'create_skl_edit','2','Create SKL','2022-11-28 23:23:47','2022-11-28 23:23:47',NULL),(69,'create_skl_show','3','Create SKL','2022-11-28 23:24:09','2022-11-28 23:24:09',NULL),(70,'create_skl_delete','4','Create SKL','2022-11-28 23:24:28','2022-11-28 23:24:28',NULL),(71,'issued_skl_create','1','Issued SKL','2022-11-28 23:24:47','2022-11-28 23:24:47',NULL),(72,'issued_skl_edit','2','Issued SKL','2022-11-28 23:25:04','2022-11-28 23:25:04',NULL),(73,'issued_skl_show','3','Issued SKL','2022-11-28 23:25:24','2022-11-28 23:25:24',NULL),(74,'issued_skl_delete','4','Issued SKL','2022-11-28 23:25:36','2022-11-28 23:25:36',NULL),(75,'master_riph_access','5','Master Data RIPH','2022-11-28 23:51:52','2022-11-28 23:53:58',NULL),(76,'data_report_access','5','Data Report','2022-12-05 00:36:16','2022-12-05 00:36:16',NULL),(77,'master_template_access','5','Master Template','2022-12-05 00:43:40','2022-12-05 00:43:40',NULL),(78,'commitment_list_access','5','Commitment List','2022-12-05 01:40:29','2022-12-05 01:40:29',NULL),(79,'verification_report_access','5','Verification Report','2022-12-05 01:40:45','2022-12-05 01:40:45',NULL),(80,'verif_onfarm_access','5','Onfarm Report','2022-12-05 09:04:53','2022-12-05 09:04:53',NULL),(81,'verif_online_access','5','Online Report','2022-12-05 09:05:07','2022-12-05 09:05:07',NULL),(82,'admin_SKL_access','5','SKL','2022-12-05 09:06:52','2022-12-05 09:06:52',NULL),(83,'master_riph_edit','2','Master Data RIPH','2022-12-15 16:25:11','2022-12-15 16:25:11',NULL),(84,'master_riph_create','1','Master Data RIPH','2022-12-15 16:25:29','2022-12-15 16:25:29',NULL),(85,'master_riph_show','3','Master Data RIPH','2022-12-15 16:25:41','2022-12-15 16:25:41',NULL),(86,'master_riph_delete','4','Master Data RIPH','2022-12-15 16:25:54','2022-12-15 16:25:54',NULL),(87,'commitment_create','1','Commitment','2023-01-07 16:26:10','2023-01-07 17:23:40','2023-01-07 17:23:40'),(88,'commitment_edit','2','Daftar RIPH','2023-01-07 16:26:25','2023-02-25 16:48:48',NULL),(89,'commitment_delete','4','Daftar RIPH','2023-01-07 16:26:38','2023-02-25 16:48:12',NULL),(90,'commitment_show','3','Daftar RIPH','2023-01-07 16:43:55','2023-02-25 16:47:54',NULL),(91,'hello_edit','2','Data Report','2023-01-09 04:54:31','2023-01-09 04:54:39','2023-01-09 04:54:39'),(92,'provinsi_access','5','Provinsi','2023-01-21 13:07:36','2023-01-21 13:07:36',NULL),(93,'kabupaten_access','5','Kabupaten','2023-01-21 13:07:55','2023-01-21 13:07:55',NULL),(94,'kecamatan_access','5','Kecamatan','2023-01-21 13:08:17','2023-01-21 13:08:17',NULL),(95,'desa_access','5','Desa','2023-01-21 13:08:33','2023-01-21 13:08:33',NULL),(96,'master_wilayah_access','5','Master Wilayah','2023-01-21 13:09:30','2023-01-21 13:09:30',NULL),(97,'poktan_create','1','Daftar Kelompok Tani','2023-01-21 15:49:36','2023-02-28 03:08:10',NULL),(98,'poktan_edit','2','Daftar Kelompok Tani','2023-01-21 15:49:51','2023-02-28 03:09:12',NULL),(99,'poktan_show','3','Daftar Kelompok Tani','2023-01-21 15:50:07','2023-02-28 03:08:47',NULL),(100,'poktan_delete','4','Daftar Kelompok Tani','2023-01-21 15:50:21','2023-02-28 03:08:29',NULL),(101,'daftar_riph_create','1','Daftar RIPH','2023-02-25 17:19:49','2023-02-25 17:19:49',NULL),(102,'pks_access','5','Daftar PKS','2023-02-28 03:06:12','2023-02-28 03:06:12',NULL),(103,'poktan_access','5','Daftar Kelompok Tani','2023-02-28 03:07:39','2023-02-28 03:07:39',NULL),(104,'pks_create','1','Daftar PKS','2023-02-28 03:09:42','2023-02-28 03:09:42',NULL),(105,'pks_edit','2','Daftar PKS','2023-02-28 03:09:56','2023-02-28 03:09:56',NULL),(106,'pks_show','3','Daftar PKS','2023-02-28 03:10:14','2023-02-28 03:10:14',NULL),(107,'pks_delete','4','Daftar PKS','2023-02-28 03:10:29','2023-02-28 03:10:29',NULL),(108,'v2access','6','V2 User',NULL,NULL,NULL),(109,'varietas_access','5','Varietas','2023-06-01 21:03:41','2023-06-01 21:03:58',NULL),(110,'varietas_create','1','Varietas',NULL,'2023-06-02 01:32:07',NULL),(111,'varietas_show','3','Varietas',NULL,'2023-06-02 01:32:24',NULL),(112,'varietas_edit','2','Varietas',NULL,'2023-06-02 01:32:57',NULL),(113,'varietas_delete','4','Varietas',NULL,'2023-06-02 01:33:15',NULL),(114,'old_skl_access','5','Old Skl','2023-06-02 04:49:08','2023-06-02 04:49:08',NULL),(115,'old_skl_create','1','Old Skl','2023-06-02 04:53:35','2023-06-02 04:53:35',NULL),(116,'old_skl_edit','2','Old Skl','2023-06-02 04:53:55','2023-06-02 04:53:55',NULL),(117,'old_skl_show','3','Old Skl','2023-06-02 04:54:12','2023-06-02 04:54:12',NULL),(118,'old_skl_delete','4','Old Skl','2023-06-02 04:54:33','2023-06-02 04:54:33',NULL);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  KEY `user_id_fk_6464594` (`user_id`),
  KEY `role_id_fk_6464594` (`role_id`),
  CONSTRAINT `role_id_fk_6464594` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_id_fk_6464594` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,1),(15,4),(23,2),(44,4),(45,4),(21,2),(51,4),(53,2),(54,2),(2,4),(8,2),(2,2),(4,2),(3,5),(5,3),(6,2);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin',NULL,NULL,NULL),(2,'User',NULL,NULL,NULL),(3,'Verifikator','2022-10-07 01:16:33','2022-10-07 01:16:33',NULL),(4,'user_v2','2022-11-02 06:22:27','2022-11-02 06:22:27',NULL),(5,'Pejabat','2023-05-25 10:34:54','2023-05-25 10:34:54',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-06-20  1:10:57
