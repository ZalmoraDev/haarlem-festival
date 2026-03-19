-- Runs after `schema.sql`
-- Seed the db with initial data

-- region Admin
-- Email: admin@example.nl
-- Password: admin123

INSERT INTO addresses (street_name, street_number, apartment_suite, city, postal_code)
VALUES ('Admin Street', 1, null, 'Haarlem', '2012AB');

INSERT INTO users (first_name, last_name, username, email, phone_number, password_hash, address_id, role, is_active)
VALUES ('Admin',
        'Administrator',
        'admin',
        'admin@example.nl',
        '+31234567890',
        '$2y$12$/FnuG3zsuI3pJJR..oWqieccljo57V5o0R4p2kVBv0zZpBk0Kmbty',
        (SELECT id FROM addresses WHERE street_name = 'Admin Street' AND street_number = '1' LIMIT 1),
        'Admin',
        TRUE);
-- endregion Admin