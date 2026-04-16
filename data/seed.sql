USE `mix-max`;

-- Optional: clear old data first
DELETE FROM booking_ticket;
DELETE FROM booking;
DELETE FROM ticket;
DELETE FROM event;
DELETE FROM venue;
DELETE FROM users;

-- Reset auto-increment
ALTER TABLE booking_ticket AUTO_INCREMENT = 1;
ALTER TABLE booking AUTO_INCREMENT = 1;
ALTER TABLE ticket AUTO_INCREMENT = 1;
ALTER TABLE event AUTO_INCREMENT = 1;
ALTER TABLE venue AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;

-- =========================
-- VENUES
-- =========================
INSERT INTO venue (name, address, city, capacity, imageUrl) VALUES
('Madison Square Garden', '4 Pennsylvania Plaza', 'New York', 20000, 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a'),
('Bell Centre', '1909 Avenue des Canadiens-de-Montréal', 'Montreal', 21302, 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819'),
('Scotiabank Arena', '40 Bay St', 'Toronto', 19800, 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f'),
('MetLife Stadium', '1 MetLife Stadium Dr', 'East Rutherford', 82500, 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14');

-- =========================
-- EVENTS
-- =========================
INSERT INTO event (title, artist, description, venueId, date, startTime, endTime, imageUrl, status) VALUES
(
    'The Midnight Tour 2026',
    'Arctic Wolves',
    'A high-energy concert experience featuring the band''s newest album and greatest hits.',
    1,
    '2026-05-14',
    '20:00:00',
    '23:00:00',
    'https://images.unsplash.com/photo-1503095396549-807759245b35',
    'scheduled'
),
(
    'Summer Music Festival',
    'Various Artists',
    'An outdoor festival featuring multiple artists across pop, hip-hop, and indie genres.',
    3,
    '2026-06-19',
    '14:00:00',
    '22:00:00',
    'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f',
    'scheduled'
),
(
    'Championship Finals',
    'City Stars vs Thunder',
    'The season finale between two top-ranked teams.',
    4,
    '2026-05-27',
    '19:30:00',
    '22:00:00',
    'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14',
    'scheduled'
),
(
    'Laugh Out Loud Night',
    'Mike Rivers',
    'A stand-up comedy night with one of the city''s funniest performers.',
    2,
    '2026-05-21',
    '19:00:00',
    '21:00:00',
    'https://images.unsplash.com/photo-1527224857830-43a7acc85260',
    'scheduled'
),
(
    'Broadway Classics',
    'Vanier Theater Company',
    'A live theater performance inspired by Broadway classics and modern stage productions.',
    2,
    '2026-06-02',
    '18:30:00',
    '21:00:00',
    'https://images.unsplash.com/photo-1507924538820-ede94a04019d',
    'scheduled'
);

-- =========================
-- TICKETS
-- =========================
INSERT INTO ticket (eventId, section, rowLetter, seatNumber, price) VALUES
-- Event 1
(1, 'A', 'A', '1', 99.99),
(1, 'A', 'A', '2', 99.99),
(1, 'A', 'A', '3', 99.99),
(1, 'B', 'B', '10', 79.99),
(1, 'B', 'B', '11', 79.99),

-- Event 2
(2, 'Floor', 'A', '1', 120.00),
(2, 'Floor', 'A', '2', 120.00),
(2, 'General', 'C', '15', 89.00),
(2, 'General', 'C', '16', 89.00),
(2, 'General', 'D', '20', 89.00),

-- Event 3
(3, 'Lower', 'A', '5', 75.00),
(3, 'Lower', 'A', '6', 75.00),
(3, 'Upper', 'F', '30', 55.00),
(3, 'Upper', 'F', '31', 55.00),

-- Event 4
(4, 'Main', 'B', '8', 49.99),
(4, 'Main', 'B', '9', 49.99),
(4, 'Main', 'C', '12', 39.99),

-- Event 5
(5, 'Front', 'A', '1', 65.00),
(5, 'Front', 'A', '2', 65.00),
(5, 'Rear', 'D', '14', 45.00);

-- =========================
-- USERS (optional)
-- password for both below: password123
-- =========================
INSERT INTO users (username, email, password, twoFactor, role) VALUES
(
    'admin',
    'admin@mixmax.com',
    '$2y$10$wH4O9QmJ0m0aBvFvHqF1VesX2Y9G4JQj8Q8P2I2rYk3l9F3QzQF6K',
    '',
    'admin'
),
(
    'demo_user',
    'user@mixmax.com',
    '$2y$10$wH4O9QmJ0m0aBvFvHqF1VesX2Y9G4JQj8Q8P2I2rYk3l9F3QzQF6K',
    '',
    'user'
);