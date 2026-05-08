<?php

declare(strict_types=1);

/**
 * Mix Max Database Setup + Seed File
 * Visit: http://localhost/Mix-Max/setup-database.php
 * This script will create the 'mix-max' database, set up the necessary tables, and insert seed data.
 */

$host = 'localhost';
$port = '3306';
$user = 'mixmax_user';
$password = '';
$dbName = 'mix-max';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $sql = <<<SQL
DROP DATABASE IF EXISTS `mix-max`;
CREATE DATABASE `mix-max` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mix-max`;

CREATE TABLE `venue` (
  `venueId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `capacity` int NOT NULL,
  `imageUrl` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`venueId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `userId` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `twoFactor` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(25) NOT NULL DEFAULT 'user',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event` (
  `eventId` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'concert',
  `description` text DEFAULT NULL,
  `venueId` int NOT NULL,
  `createdByUserId` int NULL,
  `date` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `imageUrl` varchar(255) DEFAULT NULL,
  `status` enum('pending','scheduled','cancelled','postponed','completed','rejected') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`eventId`),
  KEY `idx_event_venue` (`venueId`),
  KEY `idx_event_user` (`createdByUserId`),
  CONSTRAINT `event_venueFK` FOREIGN KEY (`venueId`) REFERENCES `venue` (`venueId`) ON DELETE CASCADE,
  CONSTRAINT `event_createdByUserFK` FOREIGN KEY (`createdByUserId`) REFERENCES `users` (`userId`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ticket` (
  `ticketId` int NOT NULL AUTO_INCREMENT,
  `eventId` int NOT NULL,
  `section` varchar(20) NOT NULL,
  `rowLetter` varchar(3) NOT NULL,
  `seatNumber` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `heldUntil` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ticketId`),
  KEY `idx_ticket_event` (`eventId`),
  CONSTRAINT `ticket_eventFK` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `booking` (
  `bookingId` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `bookingRef` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `totalPrice` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`bookingId`),
  KEY `idx_booking_user` (`userId`),
  CONSTRAINT `booking_userFK` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `booking_ticket` (
  `bookingTicketId` int NOT NULL AUTO_INCREMENT,
  `bookingId` int NOT NULL,
  `ticketId` int NOT NULL,
  `pricePaid` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`bookingTicketId`),
  KEY `idx_booking_ticket_booking` (`bookingId`),
  KEY `idx_booking_ticket_ticket` (`ticketId`),
  CONSTRAINT `booking_ticket_bookingFK` FOREIGN KEY (`bookingId`) REFERENCES `booking` (`bookingId`) ON DELETE CASCADE,
  CONSTRAINT `booking_ticket_ticketFK` FOREIGN KEY (`ticketId`) REFERENCES `ticket` (`ticketId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `venue` (`name`, `address`, `city`, `capacity`, `imageUrl`) VALUES
('Madison Square Garden', '4 Pennsylvania Plaza', 'New York', 20000, 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a'),
('Bell Centre', '1909 Avenue des Canadiens-de-Montréal', 'Montreal', 21302, 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819'),
('Scotiabank Arena', '40 Bay St', 'Toronto', 19800, 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f'),
('MetLife Stadium', '1 MetLife Stadium Dr', 'East Rutherford', 82500, 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14');

INSERT INTO `users` (`username`, `email`, `password`, `twoFactor`, `phone`, `role`) VALUES
('admin', 'admin@mixmax.com', '\$2y\$10\$wH4O9QmJ0m0aBvFvHqF1VesX2Y9G4JQj8Q8P2I2rYk3l9F3QzQF6K', '', '123-456-7890', 'admin'),
('demo_user', 'user@mixmax.com', '\$2y\$10\$wH4O9QmJ0m0aBvFvHqF1VesX2Y9G4JQj8Q8P2I2rYk3l9F3QzQF6K', '', '098-765-4321', 'user');

INSERT INTO `event` (`title`, `artist`, `category`, `description`, `venueId`, `createdByUserId`, `date`, `startTime`, `endTime`, `imageUrl`, `status`) VALUES
('The Midnight Tour 2026', 'Arctic Wolves', 'concert', 'A high-energy concert experience featuring the band''s newest album and greatest hits.', 1, NULL, '2026-05-14', '20:00:00', '23:00:00', 'https://images.unsplash.com/photo-1503095396549-807759245b35', 'scheduled'),
('Summer Music Festival', 'Various Artists', 'concert', 'An outdoor festival featuring multiple artists across pop, hip-hop, and indie genres.', 3, NULL, '2026-06-19', '14:00:00', '22:00:00', 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f', 'scheduled'),
('Championship Finals', 'City Stars vs Thunder', 'sports', 'The season finale between two top-ranked teams.', 4, NULL, '2026-05-27', '19:30:00', '22:00:00', 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14', 'scheduled'),
('Laugh Out Loud Night', 'Mike Rivers', 'comedy', 'A stand-up comedy night with one of the city''s funniest performers.', 2, NULL, '2026-05-21', '19:00:00', '21:00:00', 'https://images.unsplash.com/photo-1527224857830-43a7acc85260', 'scheduled'),
('Broadway Classics', 'Vanier Theater Company', 'theater', 'A live theater performance inspired by Broadway classics and modern stage productions.', 2, NULL, '2026-06-02', '18:30:00', '21:00:00', 'https://images.unsplash.com/photo-1507924538820-ede94a04019d', 'scheduled');

INSERT INTO `ticket` (`eventId`, `section`, `rowLetter`, `seatNumber`, `price`) VALUES
(1, 'Orchestra', 'A', '1', 99.99),
(1, 'Orchestra', 'A', '2', 99.99),
(1, 'Orchestra', 'A', '3', 99.99),
(1, 'Orchestra', 'B', '10', 79.99),
(1, 'Orchestra', 'B', '11', 79.99),

(2, 'Floor', 'A', '1', 120.00),
(2, 'Floor', 'A', '2', 120.00),
(2, 'General', 'C', '15', 89.00),
(2, 'General', 'C', '16', 89.00),
(2, 'General', 'D', '20', 89.00),

(3, 'Lower', 'A', '5', 75.00),
(3, 'Lower', 'A', '6', 75.00),
(3, 'Upper', 'F', '30', 55.00),
(3, 'Upper', 'F', '31', 55.00),

(4, 'Main', 'B', '8', 49.99),
(4, 'Main', 'B', '9', 49.99),
(4, 'Main', 'C', '12', 39.99),

(5, 'Front', 'A', '1', 65.00),
(5, 'Front', 'A', '2', 65.00),
(5, 'Rear', 'D', '14', 45.00);
SQL;

    $pdo->exec($sql);

    echo "<h1>Mix Max database setup completed successfully.</h1>";
    echo "<p>Database <strong>mix-max</strong> was created with tables and seed data.</p>";
    echo "<p><strong>Important:</strong> Delete <code>setup-database.php</code> after running it.</p>";
    echo "<p><a href='/Mix-Max/'>Go to Mix Max</a></p>";

} catch (PDOException $e) {
    echo "<h1>Database setup failed</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}