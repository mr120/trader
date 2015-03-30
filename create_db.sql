-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.41-0ubuntu0.14.04.1 - (Ubuntu)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for trader
DROP DATABASE IF EXISTS `trader`;
CREATE DATABASE IF NOT EXISTS `trader` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `trader`;


-- Dumping structure for table trader.message
DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `timePlaced` datetime NOT NULL,
  `amountBuy` double NOT NULL,
  `amountSell` double NOT NULL,
  `currencyFrom` varchar(3) NOT NULL,
  `currencyTo` varchar(3) NOT NULL,
  `rate` double NOT NULL,
  `originatingCountry` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table trader.ohlc
DROP TABLE IF EXISTS `ohlc`;
CREATE TABLE IF NOT EXISTS `ohlc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currencyFrom` varchar(3) NOT NULL,
  `currencyTo` varchar(3) NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL,
  `high` float NOT NULL,
  `low` float NOT NULL,
  `dateAdded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
