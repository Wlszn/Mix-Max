-- Improved phpMyAdmin SQL Dump for Concert Ticket Reservation System
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 09:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Mix-Max`
--

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `venueId` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `capacity` int(10) NOT NULL,
  `imageUrl` varchar(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`venueId`),
  KEY `idx_venue_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventId` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `venueId` int(10) NOT NULL,
  `date` date NOT NULL,
  `startTime` TIME NOT NULL,
  `endTime` TIME NOT NULL,
  `imageUrl` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','cancelled','postponed','completed') NOT NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventId`),
  KEY `idx_event_venue` (`venueId`),
  KEY `idx_event_date` (`date`),
  KEY `idx_event_status` (`status`),
  KEY `idx_event_artist` (`artist`),
  CONSTRAINT `event_venueFK` FOREIGN KEY (`venueId`) REFERENCES `venue` (`venueId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticketId` int(10) NOT NULL AUTO_INCREMENT,
  `eventId` int(10) NOT NULL,
  `section` varchar(20) NOT NULL,
  `rowLetter` varchar(3) NOT NULL,
  `seatNumber` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `heldUntil` datetime DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticketId`),
  KEY `idx_ticket_event` (`eventId`),
  CONSTRAINT `ticket_eventFK` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `twoFactor` varchar(255) NOT NULL,
  `role` varchar(25) NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `bookingId` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `bookingRef` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `totalPrice` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bookingId`),
  KEY `idx_booking_user` (`userId`),
  KEY `idx_booking_status` (`status`),
  KEY `idx_booking_ref` (`bookingRef`),
  CONSTRAINT `booking_userFK` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_ticket`
--

CREATE TABLE `booking_ticket` (
  `bookingTicketId` int(10) NOT NULL AUTO_INCREMENT,
  `bookingId` int(10) NOT NULL,
  `ticketId` int(10) NOT NULL,
  `pricePaid` decimal(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bookingTicketId`),
  KEY `idx_booking_ticket_booking` (`bookingId`),
  KEY `idx_booking_ticket_ticket` (`ticketId`),
  CONSTRAINT `booking_ticket_bookingFK` FOREIGN KEY (`bookingId`) REFERENCES `booking` (`bookingId`) ON DELETE CASCADE,
  CONSTRAINT `booking_ticket_ticketFK` FOREIGN KEY (`ticketId`) REFERENCES `ticket` (`ticketId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;