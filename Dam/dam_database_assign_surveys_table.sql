-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: dam_database
-- ------------------------------------------------------
-- Server version	8.0.33

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
-- Table structure for table `assign_surveys_table`
--

DROP TABLE IF EXISTS `assign_surveys_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assign_surveys_table` (
  `surveyID` int DEFAULT NULL,
  `RuserID` int DEFAULT NULL,
  `SuserID` int DEFAULT NULL,
  `assignedID` int NOT NULL,
  PRIMARY KEY (`assignedID`),
  KEY `SuserID` (`SuserID`),
  KEY `RuserID` (`RuserID`),
  KEY `surveyID` (`surveyID`),
  CONSTRAINT `assign_surveys_table_ibfk_1` FOREIGN KEY (`SuserID`) REFERENCES `surveyor_user_table` (`SuserID`),
  CONSTRAINT `assign_surveys_table_ibfk_2` FOREIGN KEY (`RuserID`) REFERENCES `respondent_user_table` (`RuserID`),
  CONSTRAINT `assign_surveys_table_ibfk_3` FOREIGN KEY (`surveyID`) REFERENCES `surveys_table` (`surveyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assign_surveys_table`
--

LOCK TABLES `assign_surveys_table` WRITE;
/*!40000 ALTER TABLE `assign_surveys_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `assign_surveys_table` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-04 15:27:46
