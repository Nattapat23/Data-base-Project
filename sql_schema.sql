CREATE TABLE `accounts` (
  `account_id` char(36) COLLATE utf8mb4_general_ci NOT NULL,
  `userName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lastName` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Content` (
  `ContentID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ContentType` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Title` mediumtext COLLATE utf8mb4_general_ci,
  `OriginalTitle` mediumtext COLLATE utf8mb4_general_ci,
  `IsAdult` tinyint(1) DEFAULT NULL,
  `ReleaseYear` int DEFAULT NULL,
  `EndYear` int DEFAULT NULL,
  `RuntimeMinutes` int DEFAULT NULL,
  `Genres` mediumtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`ContentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ContentCrew` (
  `ContentID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `Directors` mediumtext COLLATE utf8mb4_general_ci,
  `Writers` mediumtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`ContentID`),
  CONSTRAINT `contentcrew_ibfk_1` FOREIGN KEY (`ContentID`) REFERENCES `Content` (`ContentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ContentPerson` (
  `ContentID` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `Ordering` int NOT NULL,
  `PersonID` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `RoleCategory` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Job` mediumtext COLLATE utf8mb4_general_ci,
  `Characters` mediumtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`ContentID`,`Ordering`,`PersonID`),
  KEY `PersonID` (`PersonID`),
  CONSTRAINT `contentperson_ibfk_1` FOREIGN KEY (`ContentID`) REFERENCES `Content` (`ContentID`),
  CONSTRAINT `contentperson_ibfk_2` FOREIGN KEY (`PersonID`) REFERENCES `Person` (`PersonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Episode` (
  `EpisodeID` varchar(20) NOT NULL,
  `SeriesID` varchar(20) DEFAULT NULL,
  `SeasonNumber` int DEFAULT NULL,
  `EpisodeNumber` int DEFAULT NULL,
  PRIMARY KEY (`EpisodeID`),
  KEY `SeriesID` (`SeriesID`),
  CONSTRAINT `episode_ibfk_1` FOREIGN KEY (`EpisodeID`) REFERENCES `Content` (`ContentID`),
  CONSTRAINT `episode_ibfk_2` FOREIGN KEY (`SeriesID`) REFERENCES `Content` (`ContentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `Person` (
  `PersonID` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Name` mediumtext COLLATE utf8mb4_unicode_ci,
  `BirthYear` int DEFAULT NULL,
  `DeathYear` int DEFAULT NULL,
  `PrimaryProfession` mediumtext COLLATE utf8mb4_unicode_ci,
  `KnownForTitles` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`PersonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `Rating` (
  `ContentID` varchar(20) NOT NULL,
  `AverageRating` decimal(3,1) DEFAULT NULL,
  `NumVotes` int DEFAULT NULL,
  PRIMARY KEY (`ContentID`),
  CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`ContentID`) REFERENCES `Content` (`ContentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `subscription` (
  `account_id` char(36) COLLATE utf8mb4_general_ci NOT NULL,
  `subplan_type` char(1) COLLATE utf8mb4_general_ci NOT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  PRIMARY KEY (`account_id`,`subplan_type`),
  KEY `subplan_type` (`subplan_type`),
  CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`),
  CONSTRAINT `subscription_ibfk_2` FOREIGN KEY (`subplan_type`) REFERENCES `subscriptionPlan` (`subplan_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subscriptionPlan` (
  `subplan_type` char(1) COLLATE utf8mb4_general_ci NOT NULL,
  `planName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `streamsLimit` int NOT NULL,
  PRIMARY KEY (`subplan_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `watchHistory` (
  `watchHistoryID` int NOT NULL AUTO_INCREMENT,
  `account_id` char(36) COLLATE utf8mb4_general_ci NOT NULL,
  `content_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `timeWach` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`watchHistoryID`),
  UNIQUE KEY `unique_user_content` (`account_id`,`content_id`),
  KEY `content_id` (`content_id`),
  CONSTRAINT `watchhistory_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `Content` (`ContentID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
