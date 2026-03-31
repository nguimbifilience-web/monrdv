mysqldump: [Warning] Using a password on the command line interface can be insecure.
mysqldump: Error: 'Access denied; you need (at least one of) the PROCESS privilege(s) for this operation' when trying to dump tablespaces
-- MySQL dump 10.13  Distrib 8.4.8, for Linux (x86_64)
--
-- Host: localhost    Database: monrdv
-- ------------------------------------------------------
-- Server version	8.4.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `activity_logs_user_id_index` (`user_id`),
  KEY `activity_logs_created_at_index` (`created_at`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,3,'creation','RendezVous',5,'RDV créé : GRACE Kendra avec Dr. BONGOTHA le 2026-03-12 à 11:30',NULL,NULL,'172.18.0.1','2026-03-27 14:02:32','2026-03-27 14:02:32'),(2,3,'creation','RendezVous',6,'RDV créé : BOUYA Elsie avec Dr. MINKO le 2026-03-28 à 11:30',NULL,NULL,'172.18.0.1','2026-03-27 14:06:33','2026-03-27 14:06:33'),(3,3,'creation','RendezVous',7,'RDV créé : DOUKAGA Brunelle avec Dr. ALLOGHO le 2026-04-02 à 08:00',NULL,NULL,'172.18.0.1','2026-03-27 14:07:01','2026-03-27 14:07:01'),(4,3,'creation','RendezVous',8,'RDV créé : MAGANGA Ursia avec Dr. GNONGO le 2026-04-05 à 08:30',NULL,NULL,'172.18.0.1','2026-03-27 14:07:20','2026-03-27 14:07:20'),(5,3,'creation','RendezVous',9,'RDV créé : MEFAN Chancia avec Dr. MOUSSONGO le 2026-03-27 à 15:00',NULL,NULL,'172.18.0.1','2026-03-27 14:10:36','2026-03-27 14:10:36'),(6,3,'modification','RendezVous',9,'RDV #9 modifié','{\"id\": 9, \"motif\": \"URGENCE\", \"statut\": \"en_attente\", \"date_rv\": \"2026-03-27\", \"heure_rv\": \"15:00:00\", \"created_at\": \"2026-03-27T14:10:36.000000Z\", \"medecin_id\": 5, \"patient_id\": 2, \"updated_at\": \"2026-03-27T14:10:36.000000Z\"}','{\"motif\": \"URGENCE\", \"date_rv\": \"2026-03-27\", \"heure_rv\": \"15:00:00\", \"medecin_id\": \"5\", \"patient_id\": \"2\"}','172.18.0.1','2026-03-27 14:23:31','2026-03-27 14:23:31'),(7,4,'creation','Patient',7,'Patient créé : ALLOGHO Nathan',NULL,NULL,'172.18.0.1','2026-03-30 07:13:00','2026-03-30 07:13:00'),(8,4,'modification','Patient',1,'Patient modifié : DOUKAGA Brunelle','{\"id\": 1, \"nom\": \"DOUKAGA\", \"sexe\": null, \"email\": \"brunell@gmail.com\", \"ville\": null, \"prenom\": \"Brunelle\", \"quartier\": \"PLEIN CIEL\", \"telephone\": \"6585898588\", \"created_at\": \"2026-03-25T13:22:01.000000Z\", \"est_assure\": 1, \"medecin_id\": 1, \"updated_at\": \"2026-03-25T13:22:01.000000Z\", \"assurance_id\": null, \"observations\": null, \"date_naissance\": null, \"notes_medicales\": null, \"numero_assurance\": null}','{\"nom\": \"DOUKAGA\", \"_token\": \"bZi5lqU96guu0ORkmmvbeUJIztLfEnQ7W8j8r03v\", \"prenom\": \"Brunelle\", \"_method\": \"PUT\", \"quartier\": \"PLEIN CIEL\", \"telephone\": \"6585898588\", \"est_assure\": \"1\", \"medecin_id\": \"1\", \"assurance_id\": \"3\"}','172.18.0.1','2026-03-30 07:16:48','2026-03-30 07:16:48'),(9,4,'modification','Patient',3,'Patient modifié : MAGANGA Ursia','{\"id\": 3, \"nom\": \"MAGANGA\", \"sexe\": null, \"email\": \"ursia@gmail.com\", \"ville\": null, \"prenom\": \"Ursia\", \"quartier\": \"Akanda\", \"telephone\": \"0665223322\", \"created_at\": \"2026-03-25T13:25:58.000000Z\", \"est_assure\": 0, \"medecin_id\": 3, \"updated_at\": \"2026-03-27T10:18:00.000000Z\", \"assurance_id\": 3, \"observations\": null, \"date_naissance\": null, \"notes_medicales\": null, \"numero_assurance\": null}','{\"nom\": \"MAGANGA\", \"_token\": \"bZi5lqU96guu0ORkmmvbeUJIztLfEnQ7W8j8r03v\", \"prenom\": \"Ursia\", \"_method\": \"PUT\", \"quartier\": \"Akanda\", \"telephone\": \"0665223322\", \"est_assure\": \"0\", \"medecin_id\": \"3\", \"assurance_id\": \"3\"}','172.18.0.1','2026-03-30 07:16:56','2026-03-30 07:16:56'),(10,4,'modification','Patient',3,'Patient modifié : MAGANGA Ursia','{\"id\": 3, \"nom\": \"MAGANGA\", \"sexe\": null, \"email\": \"ursia@gmail.com\", \"ville\": null, \"prenom\": \"Ursia\", \"quartier\": \"Akanda\", \"telephone\": \"0665223322\", \"created_at\": \"2026-03-25T13:25:58.000000Z\", \"est_assure\": 0, \"medecin_id\": 3, \"updated_at\": \"2026-03-27T10:18:00.000000Z\", \"assurance_id\": 3, \"observations\": null, \"date_naissance\": null, \"notes_medicales\": null, \"numero_assurance\": null}','{\"nom\": \"MAGANGA\", \"_token\": \"bZi5lqU96guu0ORkmmvbeUJIztLfEnQ7W8j8r03v\", \"prenom\": \"Ursia\", \"_method\": \"PUT\", \"quartier\": \"Akanda\", \"telephone\": \"0665223322\", \"est_assure\": \"0\", \"medecin_id\": \"3\", \"assurance_id\": \"3\"}','172.18.0.1','2026-03-30 07:17:06','2026-03-30 07:17:06'),(11,4,'modification','Patient',7,'Patient modifié : ALLOGHO Nathan','{\"id\": 7, \"nom\": \"ALLOGHO\", \"sexe\": null, \"email\": null, \"ville\": null, \"prenom\": \"Nathan\", \"quartier\": \"Akanda\", \"telephone\": \"077525253\", \"created_at\": \"2026-03-30T07:13:00.000000Z\", \"est_assure\": 1, \"medecin_id\": 4, \"updated_at\": \"2026-03-30T07:13:00.000000Z\", \"assurance_id\": null, \"observations\": null, \"date_naissance\": null, \"notes_medicales\": null, \"numero_assurance\": null}','{\"nom\": \"ALLOGHO\", \"_token\": \"bZi5lqU96guu0ORkmmvbeUJIztLfEnQ7W8j8r03v\", \"prenom\": \"Nathan\", \"_method\": \"PUT\", \"quartier\": \"Akanda\", \"telephone\": \"077525253\", \"est_assure\": \"1\", \"medecin_id\": \"4\", \"assurance_id\": \"3\"}','172.18.0.1','2026-03-30 07:17:13','2026-03-30 07:17:13'),(12,4,'creation','Patient',8,'Patient créé : NDOMBI Ruth',NULL,NULL,'172.18.0.1','2026-03-30 07:19:03','2026-03-30 07:19:03'),(13,4,'creation','RendezVous',10,'RDV créé : ALLOGHO Nathan avec Dr. MOUPIGA le 2026-04-02 à 08:00',NULL,NULL,'172.18.0.1','2026-03-30 07:20:32','2026-03-30 07:20:32'),(14,4,'modification','RendezVous',8,'RDV #8 confirmé pour MAGANGA Ursia',NULL,NULL,'172.18.0.1','2026-03-30 12:14:59','2026-03-30 12:14:59'),(15,4,'modification','RendezVous',10,'RDV #10 confirmé pour ALLOGHO Nathan',NULL,NULL,'172.18.0.1','2026-03-30 12:15:03','2026-03-30 12:15:03'),(16,4,'modification','RendezVous',11,'RDV #11 confirmé pour patient patient',NULL,NULL,'172.18.0.1','2026-03-30 12:15:14','2026-03-30 12:15:14'),(17,4,'modification','RendezVous',7,'RDV #7 confirmé pour DOUKAGA Brunelle',NULL,NULL,'172.18.0.1','2026-03-30 12:15:25','2026-03-30 12:15:25'),(18,3,'creation','Medecin',9,'Médecin créé : Dr. Darielle GAGA',NULL,NULL,'172.18.0.1','2026-03-30 12:59:19','2026-03-30 12:59:19'),(19,3,'creation','Medecin',10,'Médecin créé : Dr. Darielle gaga',NULL,NULL,'172.18.0.1','2026-03-30 13:05:36','2026-03-30 13:05:36'),(20,3,'suppression','Medecin',1,'Médecin supprimé : Dr. ALLOGHO SOCHNA',NULL,NULL,'172.18.0.1','2026-03-30 13:14:52','2026-03-30 13:14:52'),(21,3,'suppression','Medecin',5,'Médecin supprimé : Dr. MOUSSONGO Carine',NULL,NULL,'172.18.0.1','2026-03-30 13:14:55','2026-03-30 13:14:55'),(22,3,'suppression','Medecin',6,'Médecin supprimé : Dr. MVOUBOU Rosemonde',NULL,NULL,'172.18.0.1','2026-03-30 13:14:57','2026-03-30 13:14:57'),(23,3,'suppression','Medecin',4,'Médecin supprimé : Dr. MOUPIGA Priscille',NULL,NULL,'172.18.0.1','2026-03-30 13:14:59','2026-03-30 13:14:59'),(24,3,'suppression','Medecin',2,'Médecin supprimé : Dr. MINKO Cynthia',NULL,NULL,'172.18.0.1','2026-03-30 13:15:04','2026-03-30 13:15:04'),(25,3,'suppression','Medecin',3,'Médecin supprimé : Dr. GNONGO joie',NULL,NULL,'172.18.0.1','2026-03-30 13:15:06','2026-03-30 13:15:06'),(26,3,'suppression','Medecin',7,'Médecin supprimé : Dr. MABIKA Amanda',NULL,NULL,'172.18.0.1','2026-03-30 13:15:08','2026-03-30 13:15:08'),(27,3,'suppression','Medecin',8,'Médecin supprimé : Dr. BONGOTHA Larissa',NULL,NULL,'172.18.0.1','2026-03-30 13:15:10','2026-03-30 13:15:10'),(28,3,'suppression','Medecin',9,'Médecin supprimé : Dr. Darielle GAGA',NULL,NULL,'172.18.0.1','2026-03-30 13:15:12','2026-03-30 13:15:12'),(29,3,'suppression','Medecin',10,'Médecin supprimé : Dr. Darielle gaga',NULL,NULL,'172.18.0.1','2026-03-30 13:15:14','2026-03-30 13:15:14'),(30,3,'creation','Medecin',11,'Médecin créé : Dr. ALLOGHO SOCHNA',NULL,NULL,'172.18.0.1','2026-03-30 13:16:02','2026-03-30 13:16:02'),(31,3,'creation','Medecin',12,'Médecin créé : Dr. MOUSSONGO Carine',NULL,NULL,'172.18.0.1','2026-03-30 13:17:02','2026-03-30 13:17:02'),(32,3,'creation','Medecin',13,'Médecin créé : Dr. MINKO Cynthia',NULL,NULL,'172.18.0.1','2026-03-30 13:17:50','2026-03-30 13:17:50'),(33,3,'modification','Medecin',13,'Médecin modifié : Dr. MINKO','{\"id\": 13, \"nom\": \"MINKO\", \"prenom\": \"Cynthia\", \"user_id\": 11, \"telephone\": null, \"created_at\": \"2026-03-30T13:17:50.000000Z\", \"updated_at\": \"2026-03-30T13:17:50.000000Z\", \"heures_mois\": 50, \"tarif_heure\": \"8000.00\", \"jours_travail\": null, \"specialite_id\": \"2\"}','{\"nom\": \"MINKO\", \"prenom\": \"Cynthia\", \"telephone\": \"6585898587\", \"heures_mois\": \"50\", \"tarif_heure\": \"8000.00\", \"specialite_id\": \"2\"}','172.18.0.1','2026-03-30 13:17:57','2026-03-30 13:17:57'),(34,3,'modification','Medecin',13,'Médecin modifié : Dr. MINKO','{\"id\": 13, \"nom\": \"MINKO\", \"prenom\": \"Cynthia\", \"user_id\": 11, \"telephone\": \"6585898587\", \"created_at\": \"2026-03-30T13:17:50.000000Z\", \"updated_at\": \"2026-03-30T13:17:57.000000Z\", \"heures_mois\": 50, \"tarif_heure\": \"8000.00\", \"jours_travail\": null, \"specialite_id\": \"2\"}','{\"nom\": \"MINKO\", \"prenom\": \"Cynthia\", \"telephone\": \"6585898587\", \"heures_mois\": \"50\", \"tarif_heure\": \"8000.00\", \"specialite_id\": \"2\"}','172.18.0.1','2026-03-30 13:18:55','2026-03-30 13:18:55'),(35,3,'creation','Medecin',14,'Médecin créé : Dr. BONGOTHA Larissa',NULL,NULL,'172.18.0.1','2026-03-30 13:22:04','2026-03-30 13:22:04'),(36,3,'creation','RendezVous',12,'RDV créé : DOUKAGA Brunelle avec Dr. ALLOGHO le 2026-04-02',NULL,NULL,'172.18.0.1','2026-03-30 14:06:56','2026-03-30 14:06:56'),(37,3,'creation','RendezVous',13,'RDV créé : ALLOGHO Nathan avec Dr. MINKO le 2026-04-03',NULL,NULL,'172.18.0.1','2026-03-30 14:12:03','2026-03-30 14:12:03'),(38,3,'modification','RendezVous',15,'RDV #15 confirmé pour bouyaa elsie',NULL,NULL,'172.18.0.1','2026-03-30 14:18:11','2026-03-30 14:18:11'),(39,3,'annulation','RendezVous',14,'RDV #14 annulé',NULL,NULL,'172.18.0.1','2026-03-30 14:18:21','2026-03-30 14:18:21'),(40,4,'modification','RendezVous',16,'RDV #16 confirmé pour bouyaa elsie',NULL,NULL,'172.18.0.1','2026-03-31 07:48:30','2026-03-31 07:48:30'),(41,4,'creation','RendezVous',18,'RDV créé : DOUKAGA Brunelle avec Dr. MINKO le 2026-04-02',NULL,NULL,'172.18.0.1','2026-03-31 07:52:27','2026-03-31 07:52:27'),(42,4,'modification','RendezVous',17,'RDV #17 confirmé pour bouyaa elsie',NULL,NULL,'172.18.0.1','2026-03-31 07:53:44','2026-03-31 07:53:44'),(43,3,'modification','RendezVous',17,'RDV #17 confirmé pour bouyaa elsie',NULL,NULL,'172.18.0.1','2026-03-31 07:57:13','2026-03-31 07:57:13'),(44,4,'creation','Patient',12,'Patient créé : BIGNAGNI Lina-praxede',NULL,NULL,'172.18.0.1','2026-03-31 10:48:59','2026-03-31 10:48:59');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assurances`
--

DROP TABLE IF EXISTS `assurances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assurances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('publique','privée') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'privée',
  `taux_couverture` int NOT NULL DEFAULT '0',
  `nom_referent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assurances`
--

LOCK TABLES `assurances` WRITE;
/*!40000 ALTER TABLE `assurances` DISABLE KEYS */;
INSERT INTO `assurances` VALUES (1,'AXA','privée',75,'MABIKA AMANDA','0741414141','amanda@gmail.com',NULL,NULL,'2026-03-25 13:17:48','2026-03-25 13:17:48'),(2,'CNAMGS','privée',75,'IBONI Beatrice','0741414142','nephtalie@gmail.com',NULL,NULL,'2026-03-25 13:19:02','2026-03-25 13:20:26'),(3,'ASCOMA','privée',50,'NGUIMBI Nephtalie','0741414141','nephtalie@gmail.com',NULL,NULL,'2026-03-25 13:19:05','2026-03-27 09:40:32');
/*!40000 ALTER TABLE `assurances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultations`
--

DROP TABLE IF EXISTS `consultations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rendez_vous_id` bigint unsigned DEFAULT NULL,
  `patient_id` bigint unsigned NOT NULL,
  `medecin_id` bigint unsigned NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `taux_couverture` int NOT NULL DEFAULT '0',
  `montant_assurance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `montant_patient` decimal(10,2) NOT NULL,
  `montant_donne` decimal(10,2) NOT NULL DEFAULT '0.00',
  `montant_rendu` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultations_rendez_vous_id_foreign` (`rendez_vous_id`),
  KEY `consultations_patient_id_foreign` (`patient_id`),
  KEY `consultations_medecin_id_foreign` (`medecin_id`),
  KEY `consultations_created_at_index` (`created_at`),
  CONSTRAINT `consultations_medecin_id_foreign` FOREIGN KEY (`medecin_id`) REFERENCES `medecins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consultations_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consultations_rendez_vous_id_foreign` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultations`
--

LOCK TABLES `consultations` WRITE;
/*!40000 ALTER TABLE `consultations` DISABLE KEYS */;
INSERT INTO `consultations` VALUES (8,NULL,7,13,30000.00,50,15000.00,15000.00,15000.00,0.00,NULL,'2026-03-30 14:13:50','2026-03-30 14:13:50'),(9,NULL,1,11,25000.00,50,12500.00,12500.00,150000.00,137500.00,NULL,'2026-03-30 14:14:19','2026-03-30 14:14:19');
/*!40000 ALTER TABLE `consultations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilites`
--

DROP TABLE IF EXISTS `disponibilites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `medecin_id` bigint unsigned NOT NULL,
  `date_travail` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `disponibilites_medecin_id_foreign` (`medecin_id`),
  CONSTRAINT `disponibilites_medecin_id_foreign` FOREIGN KEY (`medecin_id`) REFERENCES `medecins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilites`
--

LOCK TABLES `disponibilites` WRITE;
/*!40000 ALTER TABLE `disponibilites` DISABLE KEYS */;
INSERT INTO `disponibilites` VALUES (195,11,'2026-04-06','2026-03-30 13:52:42','2026-03-30 13:52:42'),(196,11,'2026-04-07','2026-03-30 13:52:43','2026-03-30 13:52:43'),(197,11,'2026-03-31','2026-03-30 13:52:44','2026-03-30 13:52:44'),(198,11,'2026-04-01','2026-03-30 13:52:45','2026-03-30 13:52:45'),(199,11,'2026-04-09','2026-03-30 13:52:48','2026-03-30 13:52:48'),(200,11,'2026-04-08','2026-03-30 13:52:49','2026-03-30 13:52:49'),(201,11,'2026-04-02','2026-03-30 13:52:50','2026-03-30 13:52:50'),(202,11,'2026-04-10','2026-03-30 13:52:53','2026-03-30 13:52:53'),(203,11,'2026-04-03','2026-03-30 13:52:55','2026-03-30 13:52:55'),(204,11,'2026-04-13','2026-03-30 13:52:57','2026-03-30 13:52:57'),(205,11,'2026-04-14','2026-03-30 13:52:58','2026-03-30 13:52:58'),(206,11,'2026-04-15','2026-03-30 13:52:59','2026-03-30 13:52:59'),(207,11,'2026-04-16','2026-03-30 13:53:00','2026-03-30 13:53:00'),(208,11,'2026-04-17','2026-03-30 13:53:01','2026-03-30 13:53:01'),(209,11,'2026-04-20','2026-03-30 13:53:03','2026-03-30 13:53:03'),(210,11,'2026-04-21','2026-03-30 13:53:04','2026-03-30 13:53:04'),(211,11,'2026-04-22','2026-03-30 13:53:05','2026-03-30 13:53:05'),(212,11,'2026-04-23','2026-03-30 13:53:06','2026-03-30 13:53:06'),(213,11,'2026-04-24','2026-03-30 13:53:08','2026-03-30 13:53:08'),(214,11,'2026-04-27','2026-03-30 13:53:10','2026-03-30 13:53:10'),(215,11,'2026-04-28','2026-03-30 13:53:12','2026-03-30 13:53:12'),(216,11,'2026-04-29','2026-03-30 13:53:13','2026-03-30 13:53:13'),(217,11,'2026-04-30','2026-03-30 13:53:14','2026-03-30 13:53:14'),(218,11,'2026-05-01','2026-03-30 13:53:15','2026-03-30 13:53:15'),(220,14,'2026-04-01','2026-03-30 13:53:35','2026-03-30 13:53:35'),(221,14,'2026-04-02','2026-03-30 13:53:35','2026-03-30 13:53:35'),(222,14,'2026-04-03','2026-03-30 13:53:36','2026-03-30 13:53:36'),(223,14,'2026-04-04','2026-03-30 13:53:37','2026-03-30 13:53:37'),(224,14,'2026-04-11','2026-03-30 13:53:38','2026-03-30 13:53:38'),(225,14,'2026-04-10','2026-03-30 13:53:38','2026-03-30 13:53:38'),(226,14,'2026-04-09','2026-03-30 13:53:39','2026-03-30 13:53:39'),(227,14,'2026-04-08','2026-03-30 13:53:40','2026-03-30 13:53:40'),(228,14,'2026-04-07','2026-03-30 13:53:40','2026-03-30 13:53:40'),(229,14,'2026-04-06','2026-03-30 13:53:41','2026-03-30 13:53:41'),(230,14,'2026-04-13','2026-03-30 13:53:42','2026-03-30 13:53:42'),(231,14,'2026-04-14','2026-03-30 13:53:43','2026-03-30 13:53:43'),(232,14,'2026-04-15','2026-03-30 13:53:43','2026-03-30 13:53:43'),(233,14,'2026-04-16','2026-03-30 13:53:44','2026-03-30 13:53:44'),(234,14,'2026-04-17','2026-03-30 13:53:44','2026-03-30 13:53:44'),(235,14,'2026-04-18','2026-03-30 13:53:45','2026-03-30 13:53:45'),(236,14,'2026-04-25','2026-03-30 13:53:46','2026-03-30 13:53:46'),(237,14,'2026-04-24','2026-03-30 13:53:46','2026-03-30 13:53:46'),(238,14,'2026-04-23','2026-03-30 13:53:47','2026-03-30 13:53:47'),(239,14,'2026-04-22','2026-03-30 13:53:48','2026-03-30 13:53:48'),(240,14,'2026-04-21','2026-03-30 13:53:48','2026-03-30 13:53:48'),(241,14,'2026-04-20','2026-03-30 13:53:49','2026-03-30 13:53:49'),(242,14,'2026-04-27','2026-03-30 13:53:50','2026-03-30 13:53:50'),(243,14,'2026-04-28','2026-03-30 13:53:50','2026-03-30 13:53:50'),(244,14,'2026-04-29','2026-03-30 13:53:51','2026-03-30 13:53:51'),(245,14,'2026-04-30','2026-03-30 13:53:51','2026-03-30 13:53:51'),(246,14,'2026-05-01','2026-03-30 13:53:52','2026-03-30 13:53:52'),(247,14,'2026-05-02','2026-03-30 13:53:52','2026-03-30 13:53:52'),(248,14,'2026-05-09','2026-03-30 13:53:53','2026-03-30 13:53:53'),(249,14,'2026-05-08','2026-03-30 13:53:54','2026-03-30 13:53:54'),(259,12,'2026-03-31','2026-03-30 13:54:15','2026-03-30 13:54:15'),(260,12,'2026-04-01','2026-03-30 13:54:17','2026-03-30 13:54:17'),(261,12,'2026-04-02','2026-03-30 13:54:19','2026-03-30 13:54:19'),(262,12,'2026-04-03','2026-03-30 13:54:22','2026-03-30 13:54:22'),(263,12,'2026-04-06','2026-03-30 13:54:24','2026-03-30 13:54:24'),(264,12,'2026-04-07','2026-03-30 13:54:25','2026-03-30 13:54:25'),(265,12,'2026-04-08','2026-03-30 13:54:26','2026-03-30 13:54:26'),(266,12,'2026-04-09','2026-03-30 13:54:27','2026-03-30 13:54:27'),(267,12,'2026-04-10','2026-03-30 13:54:28','2026-03-30 13:54:28'),(268,12,'2026-04-17','2026-03-30 13:54:29','2026-03-30 13:54:29'),(269,12,'2026-04-16','2026-03-30 13:54:30','2026-03-30 13:54:30'),(270,12,'2026-04-15','2026-03-30 13:54:33','2026-03-30 13:54:33'),(271,12,'2026-04-14','2026-03-30 13:54:34','2026-03-30 13:54:34'),(272,12,'2026-04-13','2026-03-30 13:54:35','2026-03-30 13:54:35'),(273,12,'2026-04-20','2026-03-30 13:54:36','2026-03-30 13:54:36'),(274,12,'2026-04-21','2026-03-30 13:54:36','2026-03-30 13:54:36'),(275,12,'2026-04-22','2026-03-30 13:54:37','2026-03-30 13:54:37'),(276,12,'2026-04-24','2026-03-30 13:54:38','2026-03-30 13:54:38'),(277,12,'2026-04-23','2026-03-30 13:54:38','2026-03-30 13:54:38'),(278,12,'2026-04-30','2026-03-30 13:54:42','2026-03-30 13:54:42'),(279,12,'2026-05-01','2026-03-30 13:54:42','2026-03-30 13:54:42'),(280,12,'2026-04-29','2026-03-30 13:54:44','2026-03-30 13:54:44'),(281,12,'2026-04-28','2026-03-30 13:54:45','2026-03-30 13:54:45'),(282,12,'2026-04-27','2026-03-30 13:54:45','2026-03-30 13:54:45'),(283,12,'2026-05-04','2026-03-30 13:54:46','2026-03-30 13:54:46'),(284,12,'2026-05-05','2026-03-30 13:54:47','2026-03-30 13:54:47'),(285,12,'2026-05-06','2026-03-30 13:54:48','2026-03-30 13:54:48'),(286,12,'2026-05-07','2026-03-30 13:54:48','2026-03-30 13:54:48'),(287,12,'2026-05-08','2026-03-30 13:54:49','2026-03-30 13:54:49'),(288,13,'2026-03-31','2026-03-30 13:55:18','2026-03-30 13:55:18'),(289,13,'2026-04-01','2026-03-30 13:55:19','2026-03-30 13:55:19'),(290,13,'2026-04-02','2026-03-30 13:55:20','2026-03-30 13:55:20'),(291,13,'2026-04-03','2026-03-30 13:55:21','2026-03-30 13:55:21'),(293,13,'2026-04-06','2026-03-30 13:55:23','2026-03-30 13:55:23'),(294,13,'2026-04-07','2026-03-30 13:55:24','2026-03-30 13:55:24'),(295,13,'2026-04-08','2026-03-30 13:55:25','2026-03-30 13:55:25'),(296,13,'2026-04-09','2026-03-30 13:55:25','2026-03-30 13:55:25'),(297,13,'2026-04-10','2026-03-30 13:55:26','2026-03-30 13:55:26'),(298,13,'2026-04-17','2026-03-30 13:55:27','2026-03-30 13:55:27'),(299,13,'2026-04-16','2026-03-30 13:55:27','2026-03-30 13:55:27'),(300,13,'2026-04-15','2026-03-30 13:55:28','2026-03-30 13:55:28'),(301,13,'2026-04-14','2026-03-30 13:55:29','2026-03-30 13:55:29'),(302,13,'2026-04-13','2026-03-30 13:55:29','2026-03-30 13:55:29'),(303,13,'2026-04-20','2026-03-30 13:55:30','2026-03-30 13:55:30'),(304,13,'2026-04-21','2026-03-30 13:55:30','2026-03-30 13:55:30'),(305,13,'2026-04-22','2026-03-30 13:55:31','2026-03-30 13:55:31'),(306,13,'2026-04-23','2026-03-30 13:55:32','2026-03-30 13:55:32'),(307,13,'2026-04-24','2026-03-30 13:55:32','2026-03-30 13:55:32'),(308,13,'2026-05-01','2026-03-30 13:55:33','2026-03-30 13:55:33'),(309,13,'2026-04-30','2026-03-30 13:55:33','2026-03-30 13:55:33'),(310,13,'2026-04-29','2026-03-30 13:55:34','2026-03-30 13:55:34'),(311,13,'2026-04-28','2026-03-30 13:55:35','2026-03-30 13:55:35'),(312,13,'2026-04-27','2026-03-30 13:55:35','2026-03-30 13:55:35'),(313,13,'2026-05-04','2026-03-30 13:55:39','2026-03-30 13:55:39'),(314,13,'2026-05-05','2026-03-30 13:55:39','2026-03-30 13:55:39'),(315,13,'2026-05-06','2026-03-30 13:55:40','2026-03-30 13:55:40'),(316,13,'2026-05-07','2026-03-30 13:55:41','2026-03-30 13:55:41'),(317,13,'2026-05-08','2026-03-30 13:55:41','2026-03-30 13:55:41');
/*!40000 ALTER TABLE `disponibilites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents_patient`
--

DROP TABLE IF EXISTS `documents_patient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents_patient` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assurance',
  `fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_patient_patient_id_foreign` (`patient_id`),
  CONSTRAINT `documents_patient_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents_patient`
--

LOCK TABLES `documents_patient` WRITE;
/*!40000 ALTER TABLE `documents_patient` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents_patient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medecins`
--

DROP TABLE IF EXISTS `medecins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medecins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialite_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarif_heure` decimal(10,2) NOT NULL DEFAULT '0.00',
  `heures_mois` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jours_travail` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medecins_specialite_id_index` (`specialite_id`),
  KEY `medecins_user_id_foreign` (`user_id`),
  CONSTRAINT `medecins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medecins`
--

LOCK TABLES `medecins` WRITE;
/*!40000 ALTER TABLE `medecins` DISABLE KEYS */;
INSERT INTO `medecins` VALUES (11,9,'ALLOGHO','SOCHNA','1','0741414141',10000.00,50,'2026-03-30 13:16:02','2026-03-30 13:16:02',NULL),(12,10,'MOUSSONGO','Carine','7','0665223322',7500.00,50,'2026-03-30 13:17:02','2026-03-30 13:17:02',NULL),(13,11,'MINKO','Cynthia','2','6585898587',8000.00,50,'2026-03-30 13:17:50','2026-03-30 13:17:57',NULL),(14,12,'BONGOTHA','Larissa','4','6585898588',7500.00,50,'2026-03-30 13:22:04','2026-03-30 13:22:04',NULL);
/*!40000 ALTER TABLE `medecins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_11_080732_create_specialites_table',1),(5,'2026_03_12_085007_create_assurances_table',1),(6,'2026_03_18_084332_create_medecins_table',1),(7,'2026_03_19_091113_create_patients_table',1),(8,'2026_03_19_091414_create_rendez_vouses_table',1),(9,'2026_03_20_191456_add_is_admin_to_users_table',1),(10,'2026_03_20_191807_add_admin_fields_to_users_table',1),(11,'2026_03_23_073232_add_details_to_patients_table',1),(12,'2026_03_23_104636_add_description_to_assurances_table',2),(13,'2026_03_23_132302_add_est_assure_to_patients_table',2),(14,'2026_03_23_133221_add_medecin_id_to_patients_table',3),(15,'2026_03_24_000000_add_jours_travail_to_medecins_table',3),(16,'2026_03_25_123306_add_details_to_assurances_table',4),(17,'2026_03_25_124511_add_est_assure_to_patients_table',4),(18,'2026_03_25_131656_add_taux_to_assurances_table',5),(19,'2026_03_26_082841_create_disponibilites_table',6),(20,'2026_03_26_101354_rename_specialite_in_medecins_table',7),(21,'2026_03_27_000000_add_icone_to_specialites_table',8),(22,'2026_03_27_000001_add_type_to_assurances_table',8),(23,'2026_03_27_073046_add_icone_to_specialites_table',8),(24,'2026_03_27_000002_add_notes_to_patients_table',9),(25,'2026_03_27_000003_create_consultations_table',9),(26,'2026_03_27_000004_add_tarif_to_specialites_table',10),(27,'2026_03_27_000005_add_tarif_heures_to_medecins_table',11),(28,'2026_03_27_000006_add_montant_donne_rendu_to_consultations_table',12),(29,'2026_03_27_000007_make_rendez_vous_id_nullable_in_consultations',13),(30,'2026_03_27_000008_add_performance_indexes',14),(31,'2026_03_27_131447_make_email_nullable_in_patients',15),(32,'2026_03_27_135106_create_activity_logs_table',16),(33,'2026_03_27_000009_make_email_nullable_in_patients',17),(34,'2026_03_30_000001_add_role_and_user_links',17),(35,'2026_03_30_000002_create_documents_patient_table',18),(36,'2026_03_30_000003_make_heure_rv_nullable',19),(37,'2026_03_30_000004_add_plain_password_to_users',20),(38,'2026_03_30_000005_add_source_to_rendez_vous',21),(39,'2026_03_31_103333_create_patient_validation_codes_table',22);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient_validation_codes`
--

DROP TABLE IF EXISTS `patient_validation_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_validation_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_by` bigint unsigned NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_validation_codes_requested_by_foreign` (`requested_by`),
  CONSTRAINT `patient_validation_codes_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient_validation_codes`
--

LOCK TABLES `patient_validation_codes` WRITE;
/*!40000 ALTER TABLE `patient_validation_codes` DISABLE KEYS */;
INSERT INTO `patient_validation_codes` VALUES (1,'477793','BIGNAGNI','Lina-praxede',4,1,'2026-03-31 10:47:38','2026-03-31 10:37:38','2026-03-31 10:37:48'),(2,'285742','BIGNAGNI','Lina-praxede',4,1,'2026-03-31 10:47:48','2026-03-31 10:37:48','2026-03-31 10:38:22'),(3,'751286','BIGNAGNI','Lina-praxede',4,1,'2026-03-31 10:48:22','2026-03-31 10:38:22','2026-03-31 10:48:28'),(4,'996587','BIGNAGNI','Lina-praxede',4,1,'2026-03-31 10:58:28','2026-03-31 10:48:28','2026-03-31 10:48:44'),(5,'721638','BIGNAGNI','Lina-praxede',4,1,'2026-03-31 10:58:44','2026-03-31 10:48:44','2026-03-31 10:48:59');
/*!40000 ALTER TABLE `patient_validation_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `medecin_id` bigint unsigned DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes_medicales` text COLLATE utf8mb4_unicode_ci,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quartier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `est_assure` tinyint(1) NOT NULL DEFAULT '0',
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `sexe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assurance_id` bigint unsigned DEFAULT NULL,
  `numero_assurance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patients_email_unique` (`email`),
  KEY `patients_medecin_id_foreign` (`medecin_id`),
  KEY `patients_assurance_created` (`assurance_id`,`created_at`),
  KEY `patients_est_assure_index` (`est_assure`),
  KEY `patients_user_id_foreign` (`user_id`),
  CONSTRAINT `patients_assurance_id_foreign` FOREIGN KEY (`assurance_id`) REFERENCES `assurances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patients_medecin_id_foreign` FOREIGN KEY (`medecin_id`) REFERENCES `medecins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (1,NULL,NULL,'DOUKAGA','Brunelle','brunell@gmail.com',NULL,NULL,'6585898588','PLEIN CIEL',1,NULL,NULL,NULL,3,NULL,'2026-03-25 13:22:01','2026-03-30 07:16:48'),(2,NULL,NULL,'MEFAN','Chancia','chancia@gmail.com',NULL,NULL,'0741414146','charbonnages',1,NULL,NULL,NULL,2,NULL,'2026-03-25 13:22:48','2026-03-27 10:18:20'),(3,NULL,NULL,'MAGANGA','Ursia','ursia@gmail.com',NULL,NULL,'0665223322','Akanda',0,NULL,NULL,NULL,3,NULL,'2026-03-25 13:25:58','2026-03-27 10:18:00'),(4,NULL,NULL,'NGOMA','Emmanuelle','emma@gmail.com',NULL,NULL,'077425226','charbonnages',1,NULL,NULL,NULL,1,NULL,'2026-03-25 13:47:18','2026-03-27 13:15:44'),(5,NULL,NULL,'BOUYA','Elsie','elsie@gmail.com',NULL,NULL,'052525265','charbonnages',1,NULL,NULL,NULL,3,NULL,'2026-03-26 09:17:43','2026-03-27 10:17:04'),(6,NULL,NULL,'GRACE','Kendra',NULL,NULL,NULL,'077777777','charbonnages',1,NULL,NULL,NULL,1,NULL,'2026-03-27 13:15:30','2026-03-27 13:15:30'),(7,NULL,NULL,'ALLOGHO','Nathan',NULL,NULL,NULL,'077525253','Akanda',1,NULL,NULL,NULL,3,NULL,'2026-03-30 07:13:00','2026-03-30 07:17:12'),(8,NULL,NULL,'NDOMBI','Ruth','ruth@gmail.com',NULL,NULL,'065585859','nzeng ayong',0,NULL,NULL,NULL,NULL,NULL,'2026-03-30 07:19:03','2026-03-30 07:19:03'),(9,5,NULL,'patient','patient','patient@test.com',NULL,NULL,'077588559',NULL,0,NULL,NULL,NULL,NULL,NULL,'2026-03-30 11:05:19','2026-03-30 11:05:19'),(10,NULL,NULL,'ALLOGHO','SOCHNA','sochna@gmail.com',NULL,NULL,'6585898587',NULL,0,NULL,NULL,NULL,NULL,NULL,'2026-03-30 12:55:44','2026-03-30 12:55:44'),(11,14,NULL,'bouyaa','elsie','bouya@monrdv.ga',NULL,NULL,'0741414141',NULL,0,NULL,NULL,NULL,NULL,NULL,'2026-03-30 14:16:44','2026-03-30 14:16:44'),(12,15,13,'BIGNAGNI','Lina-praxede','praxede@monrdv.ga',NULL,NULL,'0741414146','Akanda',1,NULL,NULL,NULL,NULL,NULL,'2026-03-31 10:48:59','2026-03-31 10:48:59');
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rendez_vous`
--

DROP TABLE IF EXISTS `rendez_vous`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rendez_vous` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` bigint unsigned NOT NULL,
  `medecin_id` bigint unsigned NOT NULL,
  `date_rv` date NOT NULL,
  `heure_rv` time DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `source` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `motif` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rdv_medecin_date_heure` (`medecin_id`,`date_rv`,`heure_rv`),
  KEY `rdv_patient_date` (`patient_id`,`date_rv`),
  KEY `rendez_vous_statut_index` (`statut`),
  CONSTRAINT `rendez_vous_medecin_id_foreign` FOREIGN KEY (`medecin_id`) REFERENCES `medecins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rendez_vous_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rendez_vous`
--

LOCK TABLES `rendez_vous` WRITE;
/*!40000 ALTER TABLE `rendez_vous` DISABLE KEYS */;
INSERT INTO `rendez_vous` VALUES (12,1,11,'2026-04-02',NULL,'confirme','staff','Consultation','2026-03-30 14:06:56','2026-03-30 14:06:56'),(13,7,13,'2026-04-03',NULL,'confirme','staff','control','2026-03-30 14:12:03','2026-03-30 14:12:03'),(14,11,14,'2026-04-03',NULL,'annule','en_ligne','Consultation','2026-03-30 14:17:15','2026-03-30 14:18:21'),(15,11,13,'2026-04-02',NULL,'confirme','en_ligne','control','2026-03-30 14:17:41','2026-03-30 14:18:11'),(16,11,12,'2026-04-03',NULL,'confirme','en_ligne','urgence','2026-03-31 07:47:58','2026-03-31 07:48:30'),(17,11,13,'2026-04-03',NULL,'confirme','en_ligne','consultation','2026-03-31 07:50:25','2026-03-31 07:53:44'),(18,1,13,'2026-04-02',NULL,'confirme','staff','control','2026-03-31 07:52:27','2026-03-31 07:52:27');
/*!40000 ALTER TABLE `rendez_vous` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specialites`
--

DROP TABLE IF EXISTS `specialites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `specialites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarif_consultation` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specialites`
--

LOCK TABLES `specialites` WRITE;
/*!40000 ALTER TABLE `specialites` DISABLE KEYS */;
INSERT INTO `specialites` VALUES (1,'Cardiologie',NULL,25000.00,'2026-03-25 13:11:49','2026-03-27 09:38:31'),(2,'Pneumologie',NULL,30000.00,'2026-03-25 13:12:08','2026-03-27 10:14:22'),(3,'Neurologie',NULL,25000.00,'2026-03-25 13:12:28','2026-03-27 10:13:22'),(4,'Dermatologie',NULL,10000.00,'2026-03-25 13:12:43','2026-03-27 09:39:45'),(5,'Gériatrie',NULL,20000.00,'2026-03-25 13:12:54','2026-03-27 10:12:23'),(6,'Pédiatrie',NULL,18000.00,'2026-03-25 13:13:06','2026-03-27 10:14:04'),(7,'Ophtalmologie',NULL,20000.00,'2026-03-25 13:13:33','2026-03-27 10:13:44');
/*!40000 ALTER TABLE `specialites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plain_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'secretaire',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'test','test@clio.local',NULL,'$2y$12$ycDVJMexezttlzhTZo5klOjBA3pwZ0SAdeg61wW3N8TyDlfGzyUVO','password','secretaire',NULL,'2026-03-26 13:05:07','2026-03-26 13:05:07'),(3,'NGUIMBI FILIENCE','admin@monrdv.ga','2026-03-30 13:43:07','$2y$12$t1UXH5RKpehptK18MRLIQOhv4c2QCko.QAy1CuhXLJqDpuoq2nuQC','password','admin',NULL,'2026-03-27 11:06:12','2026-03-30 13:43:07'),(4,'Secrétaire','user@monrdv.ga','2026-03-30 11:32:37','$2y$12$i9pohuQ2Jbq200tpX9Z8KewoRXw6s8EWEWLOXh6OPLDaLZXSA0kl2','password','secretaire',NULL,'2026-03-27 11:06:12','2026-03-30 11:32:37'),(5,'patient patient','patient@test.com',NULL,'$2y$12$1yAdXVz7FWXsokA2/fvZjemqCgESu9.6ZNMp9GMhM82/MPCytLvcq','password','patient',NULL,'2026-03-30 11:05:19','2026-03-30 11:05:19'),(7,'Dr. Darielle GAGA','gaga@test.com',NULL,'$2y$12$2m5GzjbKQau4iasiwMA2kuUfxj3xK5WpBy1WGpLEtPxV1Itx0mr0u','password','medecin',NULL,'2026-03-30 12:59:19','2026-03-30 12:59:19'),(8,'Dr. Darielle gaga','gaga@gmail.com',NULL,'$2y$12$rgtG3FasngZu8My/3aLHzOtNLa3zScFnrpZtSGfqnHwEyjTfqdsp.','password','medecin',NULL,'2026-03-30 13:05:36','2026-03-30 13:05:36'),(9,'Dr. ALLOGHO SOCHNA','sochna@monrdv.com',NULL,'$2y$12$vBa0mVKkwV5tR15I3mV8Ee0KQAiL3nZk1/bSg5QEHQwuy9nTM5wpG','password','medecin',NULL,'2026-03-30 13:16:02','2026-03-30 13:48:27'),(10,'Dr. MOUSSONGO Carine','carine@monrdv.ga',NULL,'$2y$12$/50MEVBYZfYf2C6VI613se/RfLFinf2J1yi0s1N7Xhp8fDU0Dre1e','password','medecin',NULL,'2026-03-30 13:17:02','2026-03-30 13:17:02'),(11,'Dr. MINKO Cynthia','cynthia@monrdv.ga',NULL,'$2y$12$pFXfbFeq6hKftHm.v/2tsOBCrxpmxwM0HnzHsSEKMKkt8nIugRUcW','password','medecin',NULL,'2026-03-30 13:17:50','2026-03-31 07:18:14'),(12,'Dr. BONGOTHA Larissa','lala@monrdv.ga',NULL,'$2y$12$JrNmYSX2gZxulu1jbWyxW.AxxAjPqZQYmYDkBQfyaTcXgElbTjFYy','password','medecin',NULL,'2026-03-30 13:22:04','2026-03-30 13:22:04'),(13,'Secrétaire 1','secretaire@monrdv.ga','2026-03-30 13:43:07','$2y$12$zJ.TFSX5ixmo48WVK.Sfnua3JkhRd95ed72hyGW4a3UtGTRG5HeD.','password','secretaire',NULL,'2026-03-30 13:43:07','2026-03-30 13:43:07'),(14,'elsie bouyaa','bouya@monrdv.ga',NULL,'$2y$12$pNTnei3ICcO0KqRoMKeAMudWUrRfJKjt0MzG0vY1Qs3OqttGGBJTG','password','patient',NULL,'2026-03-30 14:16:44','2026-03-30 14:16:44'),(15,'Lina-praxede BIGNAGNI','praxede@monrdv.ga',NULL,'$2y$12$bBADaM56myPK0ZAf/iDOi.nvlzKv361m4gkaMSJx1P5MwLBcyHQzu','big5521','patient',NULL,'2026-03-31 10:48:59','2026-03-31 10:48:59');
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

-- Dump completed on 2026-03-31 12:47:04
