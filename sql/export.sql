-- --------------------------------------------------------
-- Host:                         spx-webtest-s01
-- Server version:               8.0.33 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for furi01db
DROP DATABASE IF EXISTS `furi01db`;
CREATE DATABASE IF NOT EXISTS `furi01db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `furi01db`;

-- Dumping structure for table furi01db.auditlog
DROP TABLE IF EXISTS `auditlog`;
CREATE TABLE IF NOT EXISTS `auditlog` (
  `auditLogId` int NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `entity` varchar(255) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `entry` longtext,
  PRIMARY KEY (`auditLogId`)
) ENGINE=InnoDB AUTO_INCREMENT=643 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.auditlog: ~40 rows (approximately)
INSERT INTO `auditlog` (`auditLogId`, `timestamp`, `entity`, `action`, `entry`) VALUES
	(602, '2025-03-21 00:07:19', 'User', 'Login', 'MemberId:14, UserName:x - Successful login.'),
	(603, '2025-03-21 00:07:20', 'User', 'Destroy', 'DESTROY member object: memberId:14, UserName:x'),
	(604, '2025-03-21 00:07:27', 'User', 'Destroy', 'DESTROY member object: memberId:14, UserName:x'),
	(605, '2025-03-21 00:07:37', 'User', 'logout', 'memberId:14, UserName:x - has logged out'),
	(606, '2025-03-21 00:07:37', 'User', 'Destroy', 'DESTROY member object: memberId:14, UserName:x'),
	(607, '2025-03-21 00:09:39', 'User', 'Login', 'MemberId:14, UserName:x - Successful login.'),
	(608, '2025-03-21 00:09:39', 'User', 'Destroy', 'DESTROY member object: memberId:14, UserName:x'),
	(609, '2025-03-21 00:16:33', 'User', 'logout', 'memberId:14, UserName:x - has logged out'),
	(610, '2025-03-21 00:18:13', 'User', 'User Exists Check', 'Verified: User Does Not Exist:<jimmy>'),
	(612, '2025-03-21 00:18:13', 'User', 'Save', 'Add Successful: memberId:0, UserName:jimmy'),
	(613, '2025-03-21 00:18:44', 'User', 'Login', 'MemberId:18, UserName:jimmy - Successful login.'),
	(614, '2025-03-21 00:25:26', 'User', 'Login', 'MemberId:18, UserName:jimmy - Successful login.'),
	(615, '2025-03-21 00:34:23', 'User', 'logout', 'memberId:18, UserName:jimmy - has logged out'),
	(616, '2025-03-21 00:35:50', 'User', 'Login', 'MemberId:14, UserName:x - Successful login.'),
	(617, '2025-03-21 00:36:14', 'User', 'Save', 'Update Successful: memberId:14, UserName:x'),
	(618, '2025-03-21 00:39:11', 'User', 'Save', 'Update Successful: memberId:14, UserName:x'),
	(619, '2025-03-21 00:42:36', 'User', 'Save', 'Update Successful: memberId:14, UserName:x'),
	(620, '2025-03-21 00:42:41', 'User', 'logout', 'memberId:14, UserName:x - has logged out'),
	(621, '2025-03-21 00:42:44', 'User', 'Login', 'MemberId:14, UserName:x - Successful login.'),
	(622, '2025-03-21 00:42:55', 'User', 'Delete', 'Delete Successful: memberId:14, UserName:x'),
	(623, '2025-03-21 00:43:23', 'User', 'User Exists Check', 'Verified: User Does Not Exist:<x>'),
	(624, '2025-03-21 00:43:24', 'User', 'Save', 'Add Successful: memberId:0, UserName:x'),
	(625, '2025-04-10 00:19:09', 'User', 'User Exists Check', 'Verified: User Does Not Exist:<u>'),
	(626, '2025-04-10 00:19:09', 'User', 'Save', 'Add Successful: memberId:0, UserName:u'),
	(627, '2025-04-10 00:19:17', 'User', 'Login', 'MemberId:20, UserName:u - Successful login.'),
	(628, '2025-04-10 00:19:26', 'User', 'logout', 'memberId:20, UserName:u - has logged out'),
	(629, '2025-04-10 00:19:35', 'User', 'Login', 'MemberId:20, UserName:u - Successful login.'),
	(630, '2025-04-10 00:19:45', 'User', 'Save', 'Update Successful: memberId:20, UserName:u'),
	(631, '2025-04-10 00:19:49', 'User', 'logout', 'memberId:20, UserName:u - has logged out'),
	(632, '2025-04-10 00:19:55', 'User', 'Login', 'MemberId:20, UserName:u - Successful login.'),
	(633, '2025-04-29 23:59:27', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(634, '2025-04-30 00:04:33', 'User', 'logout', 'memberId:19, UserName:x - has logged out'),
	(635, '2025-04-30 00:04:38', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(636, '2025-05-01 04:23:30', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(637, '2025-05-02 02:16:45', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(638, '2025-05-04 23:49:00', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(639, '2025-05-05 00:25:12', 'User', 'logout', 'memberId:19, UserName:x - has logged out'),
	(640, '2025-05-07 01:46:32', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(641, '2025-05-07 04:16:15', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.'),
	(642, '2025-05-07 04:27:17', 'User', 'Login', 'MemberId:19, UserName:x - Successful login.');

-- Dumping structure for table furi01db.cinemalocations
DROP TABLE IF EXISTS `cinemalocations`;
CREATE TABLE IF NOT EXISTS `cinemalocations` (
  `locationId` int NOT NULL AUTO_INCREMENT,
  `locationName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '0',
  `GPS` varchar(100) DEFAULT '0',
  `address` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`locationId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.cinemalocations: ~1 rows (approximately)
INSERT INTO `cinemalocations` (`locationId`, `locationName`, `GPS`, `address`) VALUES
	(1, 'Chatswood', '123 chatswood road', '123 chatswood road');

-- Dumping structure for table furi01db.cinemas
DROP TABLE IF EXISTS `cinemas`;
CREATE TABLE IF NOT EXISTS `cinemas` (
  `cinemaId` int NOT NULL AUTO_INCREMENT,
  `locationId` int DEFAULT NULL,
  `cinemaName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cinemaId`),
  KEY `locationId` (`locationId`),
  CONSTRAINT `locationId` FOREIGN KEY (`locationId`) REFERENCES `cinemalocations` (`locationId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.cinemas: ~1 rows (approximately)
INSERT INTO `cinemas` (`cinemaId`, `locationId`, `cinemaName`) VALUES
	(1, 1, 'Cinema Alpha');

-- Dumping structure for table furi01db.members
DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `memberId` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `firstName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `lastName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `town` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `postcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '',
  PRIMARY KEY (`memberId`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.members: ~4 rows (approximately)
INSERT INTO `members` (`memberId`, `username`, `password`, `firstName`, `lastName`, `role`, `street`, `town`, `state`, `postcode`, `phone`, `email`) VALUES
	(15, 'attacker', '$2y$10$mjQDXnjF6Oi/DelIZIDNE.A4ouOGqpZJ/I3e4WeVzgdxQ4C.5vK5i', 'iU85H6oF8AiVCPzMkkdFD7Z2PaH6pn1Ox9qmERdycTB631VRyKHIN8UTwomHvkOlBl94hLv2G/iGQuUi/ttESrMmYTtVKIrKLFQnHOHs0C2d0r+uuLwuVqJxVk2DOXQH', 'wG5f53BVsEjPN+fyE5f0GudaMFNFGq/I0wbX27X8Hnw4oEBgfgBNKoCONNXMHmztFF+pNaEw7AyvNWuQYpoBOfWsdobA1+VE5I62DPS2/ydv2uzc3rxvh0j+GalfzuaT', 'custom', 'Dw5K/qhhlo5KEK+H4u6TDtHVbUOSk3WwUM+kvmmWtcbnbSz05G2fJCFyjPkE0DimVVG1PLCVNgz2Iemqo44x+nH19nRJDICqPcsnIvRHYlr6O38v/eM7rG77fzbquqqg', 'ftHzyKdRb+gHznZWyzzo2pnASJFaNDIUPz9PuC8/A/mmnS329qJDLB3RdbWeji7uxaCC0mFbNymVLR8x8/4dtNGDr/JB7vGsLOVDwM5mFhbVyD2PM5hY6leeZTkkcUFQ', '3/u0s/gOEKq/ycvvHcf7IYoM2Htyp73xcdD3OBTz7pmQaBjuQThnVGn/q+2Msdjd9UR6+ZQiwR3jgj4y/0MnKxzYL3c7g43ILthaNoKFKomjdBeSZDE8OvmsHsY8gCZn', '6/ED0l5dCAmPyhkkkS6k5qK5FPb2tpd8xd+dIhVNr/HgTzQwkb+vUCPSLSLihOpbmJvjvH/ItBLC2CIhDDHOfao2ny4VGExa7yl/FKVvcMvCEa8JUknBHkZg4bw8GyGe', 'eq5yBc8OicM2JgFSoObkykxWy2q3Trxona8Ibe+SIrTAKo3GQH1xsT/Gu6DDv/fUCxtIugvYEmbTOJ/AizoXCiVcTtyETmb0yEb+rI67rtTHZU5ohfx4zsq2x/vR9bqc', 'sAf50pbE3P+mS8S2afNMXJkzcwDH1HfEuJ+xi/bEiXovm00zTsiJaMslKvNlSzwJxw6M5tm3tXLy/eYhNCZK0Hz9czqVdIpFhqzXFIwco450E936JBWPwn9ArCw0/VMz'),
	(18, 'jimmy', '$2y$10$z7DrvhHqkt.SlxbVMyRlS.5aavIJ95VT5uC2yeo5VuRj.jNYByaiq', 'NdgQqZss0leLXoR/Ch0tc8B75Rhbu4dxznvoED2Vi2LcWpUkYuxfzCyPos/KFjqvWSvFhy0ryMiCaW1iuhtX9m5VlYQ4USWjADHszwLrXtwNEQSLyjc6WozLO0erJZNa', '6MSt+gOgicivpWaUTkuqpDPcdV30HyN6F4O9fKXbpNzhZtjU73ZBx7BxLFMxq8KC3rdQ3Xv8TzQ6MwVsr27FqQqbyv5G99zUYFYNFd0cjuuXL3nFmOf3PXjT5OJXba84', NULL, '8zwsHNORxzFmF02G8nvsCoyi92/YbTifdVnv6G2onIbpegi7t4gsaXyEQUjYG6lf+WnTjArlADNyZ7qcKBZzJByL4GnG7DL714q3dLZLRyaRrGsdBLML/mNBYygdBPFW', 'XGP7l8NmJJlHXdHHJw08s9/YRwiGdyHld4Aq1JqK/OUDZnngtOD3alznp4IKdwsC78f3UzQYlFy5CLfD5KRe9YR3yBH49hPolxK+glap86hEnhUfxXxw4f/5jRtTjfOp', 'kNWBQJUB6t82b6VEnGCDTLPa5uAGvjvXRXoa3dKb3JuMj4XL6UZveZogjPKVAz8OHuNnXLRPB9WdHKxXyqt0VEIZOjE2Hx/wup8xcNcqEcvwvjoqMFIOHNYUn2xOoPFI', 'v+m7cFhZJgPsZ3oy6DJJpuTZC3rPJJOJjoYCpXK1NLPTDs4llqwNOCGlR/BUkRzCaeiqmjOCoz4wkIGoYdDW5FLN6D46Umu3jvVKpNs0aoPEgF+McHErzr1ViTg1S2UA', 'yDF5OsVAsXfxGGVPBdLag3nbDncuEC7SW96BukXB9IjuHTXTBL8tFugAM6fNG365qeqdyF7lrnVkOfdDsBJIcXU53SsMgKT/um2Hb4TT48AYcsNSJHmyrXYjVzJFaOBw', 'lX15qDT5dibxUuuGOp+vJFLNILXs3moAFiVD+auWqdX0ooPfODvblPvzNmJT6Y4c/+aR8vXkV4TCpgve5YRlqgng4sstskDHD6Jz++miulh3CxRjN3CYZnuBt/DZX0XhWh1IJtQg+2f7Hy+gEGW3Xw=='),
	(19, 'x', '$2y$10$G7B3yujBBlnjGhC/LNyHjeuV5hTqNadMTSkSs6OQ10EDPlkjhTUpO', 'wgYR2kax2ykeXJxOHFoi3gkEH+h79BC8ff0uOd26Igvsi0scEAy3ibzMQiieiteUBiRvow+TmH0fx0qo20G/xzIil3q/IWxum5pAwrQebqQdx0wBD8nWA4IO0R1QmnIG', 'DkLAuN1IBQcFKYmJ3CoHBDWakY7x8tOUui8NCGNoUeEAtnl+Qc5mCxcY0575HKoS0AbKazPPT4qLLgxnadJ1uXLaNabrR36yuyKJI6wM34E5ndtrTnXZVsBEimoc/k/Q', NULL, '5iykfoz4lisDE/SkiWolI4e1qe6SzzXIgXhaCpldpWeG74CKsLlGFHIURKFn+Mz3qAo//Unb6P6VXwfiPA7EAJqBhd2peNkaiQOQ2TiWqaP02/m28/rrbIsVAvuemzoS', '5a7xR/DwseKuhQSi+mCgMZREnt5oXESe/DXpFG22e+6PFB0sOfVNg60jfBcqb2Oon8uiOM1RtD+ntcaO4uAfco5XD6blllHWnpcm0jbsXd8saVviNWUsZIQnwx8QoP5o', 'qNswLxvkgs910MNXj/TBrdrbbsLhmKoVntbJqmIxVYG/6FZNGC3uPVutEvEdx8XM3ANzIUNrbbB4cs3WaDetNkcF3zOoFYN2yoa4laCWwt3PtMQ9jwoEBo3n0+lRsojA', 'tyeF/JRBEFopgQP/Ylwb+mtiZkoFy6EFcB+sI2ezfyiDeveM0BMOoOLWDMYZB5Jo6aXU6EdYSWk5/bDlAPp51lTSfzulQjfv7qfyY1OD2qzVKNCmb2rixynBKl1Bl0pg', '6CwAPazm7mCxJ3Al3YanUkusQTp09nj14VBDvONQ3k86+88UN57ymPe/mnyTWHn0nq7yQpws/kPExL6XJRLQRgigjv9DDUyw4hA/npfJqF9/u/OSvpWWcOy0I3cziXpt', 'bTnIvC7Gx1+xX+xgd7yNoxc/XcOdevDpyWlHSsAKtoDNQ6yAjGfVm4+N7X2N5C7p4sntkK8M9Z0ESMba8wkUjwIgrYJGFnUdMeG705tt1hXm5PWUupyhzZpSe9Nt0ra9+NlVA6nXlU+7NTK0PsSuGg=='),
	(20, 'u', '$2y$10$j71tS8mXcX9ymxav7YtnNO5VkdLWBpLqZHEhAoGhGbRyouDBMZZZG', 'JTGOIT7zUwJAeZEaLLOBYNYZ5RO7nNDBXJWx0il32nNAT40Bc6QdMpiOwHglE646wyeXab4YlJSFt3jgSc1L+rImrg7wMxTICWBLgstWrdbDtIoMFOIEmCwnFMGK7qJz', 'B/y2BrqUFCGYb05f/dVnvrJm4qlVDlc1g00EkV6wLpDLs6gS4MTXVQsr20nf3aEPGmaB3mLf51kE4zWAri1YCzY/b6hnX9wPus+pgVmTI/koCCw9vZOaMeUZBF/UYf1E', NULL, 'SsfEr5Zd6IXeSKxw+5ejVeFz3qhWlA9hjp67IT9PCvqzLgAW+N8VpGQHZzx3suF6mrAGwaqRuEwkulYZFOeckaH2ULc8a3jpzYQYmP5YI+48yOXnTZbNs3p/uf/cUNHc', 'sae1VyJ3f6zMwxh9RMDKKhoM+M0g2hvkCXgCyoTOdPUAumJ3ixxG6Wy8tv8fZWw6wYBCNhNewxE3rVKCyT/Dz0ljUg7UVZWqScyUR6j5ffugSRxKp5KUawSQPxfwRQ4u', 'i0W6FoHNwWNIZKwIAVYOQMM6GrsDXf94mfxSSoH2VyDI4m5BUVnk1EjHUQjLnofg2NL2XhpctsXa+CEbO9HRmcS29leScaHwKlfaAkNbjGmAMBvBIqz8PJBFQq71Frmy', 'BifCCjaIgn8+RzpdiCvwFg79FeOeMUXbeypag8NVyhyWZLH+3F4zBmFoq4ee1WGImBvObTLpJhB4B3vfcn67uq2RQ7ePqrOaqnbuldo/9lQzyYk0OHjrxj5TfClA1ewj', 'G5RD5AQZFxdKGaT5EXtPK03O+MTRGDFToYErKnO1dfLBqS48UyJ/NRryT+oPuuAYQ1m43aBsgMBlILVwSvE0sZLunRHJPo39LyiK33dSLvcm5SWvSinXfcrlTIZnoVAc', 'jkgDBnwNxxL1upVDDw/3WfXrxUX2SP/06KiqkLtg8RHIdB0dWgwoKjLWBnia1s1a7RJK+ptYvHu8DHZM+1fOoQef8QYZ11480QTgEyh8idPuawFO6Z/tcj/vQMEjbMjr');

-- Dumping structure for table furi01db.movies
DROP TABLE IF EXISTS `movies`;
CREATE TABLE IF NOT EXISTS `movies` (
  `movieId` int NOT NULL AUTO_INCREMENT,
  `cinemaId` int DEFAULT NULL,
  `movieName` varchar(50) DEFAULT NULL,
  `posterFile` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `movieDescription` varchar(500) DEFAULT NULL,
  `trailerName` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`movieId`),
  KEY `cinemaId` (`cinemaId`),
  CONSTRAINT `cinemaId` FOREIGN KEY (`cinemaId`) REFERENCES `cinemas` (`cinemaId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.movies: ~0 rows (approximately)
INSERT INTO `movies` (`movieId`, `cinemaId`, `movieName`, `posterFile`, `movieDescription`, `trailerName`) VALUES
	(1, 1, 'A Minecraft Movie', 'https://cdn.eventcinemas.com.au/cdn/resources/movies/17992/images/largeposter.jpg', 'Trying to leave their troubled lives behind, twin brothers (Jordan) return to their hometown to start again, only to discover that an even greater evil is waiting to welcome them back.', '8B1EtVPBSMw');

-- Dumping structure for table furi01db.orderitems
DROP TABLE IF EXISTS `orderitems`;
CREATE TABLE IF NOT EXISTS `orderitems` (
  `orderItemId` int NOT NULL AUTO_INCREMENT,
  `orderId` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderItemId`),
  KEY `orderitem` (`orderId`),
  CONSTRAINT `orderitem` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.orderitems: ~0 rows (approximately)

-- Dumping structure for table furi01db.orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `orderId` int NOT NULL AUTO_INCREMENT,
  `memberId` int NOT NULL DEFAULT '0',
  `cost` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderId`),
  KEY `memberorder` (`memberId`),
  CONSTRAINT `memberorder` FOREIGN KEY (`memberId`) REFERENCES `members` (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.orders: ~0 rows (approximately)

-- Dumping structure for table furi01db.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `sessionId` int NOT NULL AUTO_INCREMENT,
  `movieId` int DEFAULT NULL,
  `cinemaId` int DEFAULT NULL,
  `time` time DEFAULT NULL,
  `seatCost` float DEFAULT NULL,
  PRIMARY KEY (`sessionId`),
  KEY `movieId` (`movieId`),
  KEY `cinemaId2` (`cinemaId`),
  CONSTRAINT `cinemaId2` FOREIGN KEY (`cinemaId`) REFERENCES `cinemas` (`cinemaId`),
  CONSTRAINT `movieId` FOREIGN KEY (`movieId`) REFERENCES `movies` (`movieId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table furi01db.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`sessionId`, `movieId`, `cinemaId`, `time`, `seatCost`) VALUES
	(1, 1, 1, '10:24:42', 20),
	(2, 1, 1, '09:59:27', 19);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
