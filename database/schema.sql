-- Initialize the database schema, 1:1 from what is described in our ERD.
-- Runs before `seed.sql`
SET TIMEZONE = 'Europe/Amsterdam';

-- Priority goes from least to most access, a higher access level can
-- do everything the lower levels can do, but not the other way around.
CREATE TYPE role_enum AS ENUM ('Customer', 'Employee', 'Validated', 'Admin');
CREATE TYPE order_status_enum AS ENUM ('Open', 'Processing', 'Completed');
CREATE TYPE cuisine_enum AS ENUM ('Vegan', 'French', 'European', 'Fish & Seafood', 'Mediterranean', 'American', 'Thai', 'Spanish');
CREATE TYPE language_enum AS ENUM ('English', 'Dutch', 'Chinese');
CREATE TYPE genre_enum AS ENUM ('Jazz', 'Dance');

--region addresses
CREATE TABLE IF NOT EXISTS addresses
(
    id              SERIAL PRIMARY KEY,
    street_name     VARCHAR(255) NOT NULL,
    street_number   VARCHAR(15) NOT NULL,
    apartment_suite VARCHAR(255),
    city            VARCHAR(255) NOT NULL,
    postal_code     VARCHAR(255) NOT NULL
);
--endregion addresses

--region history_locations
CREATE TABLE IF NOT EXISTS history_locations
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL
);
--endregion history_locations

--region users
CREATE TABLE IF NOT EXISTS users
(
    id            SERIAL PRIMARY KEY,
    first_name    VARCHAR(255) NOT NULL,
    last_name     VARCHAR(255) NOT NULL,
    username      VARCHAR(255) NOT NULL UNIQUE,
    email         VARCHAR(256) NOT NULL UNIQUE,
    phone_number  VARCHAR(20),
    password_hash TEXT         NOT NULL, -- Ensures no padding compared to VARCHAR
    address_id    INT          NOT NULL,
    role          role_enum    NOT NULL DEFAULT 'Customer',
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    is_active     BOOLEAN      NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_address_id
        FOREIGN KEY (address_id)
            REFERENCES addresses (id)
            ON DELETE NO ACTION
);
--endregion users

--region password_reset_tokens
CREATE TABLE IF NOT EXISTS password_reset_tokens
(
    id         SERIAL PRIMARY KEY,
    user_id    INT          NOT NULL,
    token_hash VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMPTZ  NOT NULL,
    used_at    TIMESTAMPTZ,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_user_reset_id
        FOREIGN KEY (user_id)
            REFERENCES users (id)
            ON DELETE CASCADE
);
--endregion password_reset_tokens

--region orders
CREATE TABLE IF NOT EXISTS orders
(
    id           SERIAL PRIMARY KEY,
    user_id      INT               NOT NULL,
    order_date   TIMESTAMPTZ       NOT NULL DEFAULT NOW(),
    total_cost   DECIMAL(10, 2)    NOT NULL,
    order_status order_status_enum NOT NULL DEFAULT 'Open',
    is_active    BOOLEAN           NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_user_id
        FOREIGN KEY (user_id)
            REFERENCES users (id)
            ON DELETE NO ACTION
);
--endregion orders

--region tickets
CREATE TABLE IF NOT EXISTS tickets
(
    id          SERIAL PRIMARY KEY,
    order_id    INT           NOT NULL,
    employee_id INT,
    qr_code     VARCHAR(255)  NOT NULL UNIQUE,
    is_scanned  BOOLEAN       NOT NULL DEFAULT FALSE,
    ticket_cost DECIMAL(6, 2) NOT NULL,
    is_active   BOOLEAN       NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_order_id
        FOREIGN KEY (order_id)
            REFERENCES orders (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_employee_id
        FOREIGN KEY (employee_id)
            REFERENCES users (id)
            ON DELETE NO ACTION
);
--endregion tickets

--region data_objects
CREATE TABLE IF NOT EXISTS data_objects
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE
);
--endregion dataObjects

--region restaurants
CREATE TABLE IF NOT EXISTS restaurants
(
    id              SERIAL PRIMARY KEY,
    address_id      INT           NOT NULL,
    phone           VARCHAR(20)   NOT NULL,
    email           VARCHAR(255)  NOT NULL,
    star_rating     INT           NOT NULL CHECK (star_rating >= 0 AND star_rating <= 5),
    price_adult     DECIMAL(6, 2) NOT NULL,
    price_child     DECIMAL(6, 2) NOT NULL,
    available_seats INT           NOT NULL,
    cuisine         cuisine_enum  NOT NULL,
    reservation_fee DECIMAL(6, 2) NOT NULL,
    CONSTRAINT fk_address_id
        FOREIGN KEY (address_id)
            REFERENCES addresses (id)
            ON DELETE NO ACTION,
    CONSTRAINT fk_restaurant_id
        FOREIGN KEY (id)
            REFERENCES data_objects (id)
            ON DELETE CASCADE
);
--endregion restaurants

--region timeslots
CREATE TABLE IF NOT EXISTS timeslots
(
    id            SERIAL PRIMARY KEY,
    restaurant_id INT         NOT NULL,
    start_time    TIMESTAMPTZ NOT NULL,
    end_time      TIMESTAMPTZ NOT NULL,
    is_active     BOOLEAN     NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_restaurant_id
        FOREIGN KEY (restaurant_id)
            REFERENCES restaurants (id)
            ON DELETE CASCADE
);
--endregion timeslots

--region reservations
CREATE TABLE IF NOT EXISTS reservations
(
    ticket_id          SERIAL PRIMARY KEY,
    timeslot_id        INT         NOT NULL,
    reservation_date   TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_at         TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    amount_of_children INT         NOT NULL,
    amount_of_adults   INT         NOT NULL,
    is_active          BOOLEAN     NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_timeslot_id
        FOREIGN KEY (timeslot_id)
            REFERENCES timeslots (id)
            ON DELETE NO ACTION,
    CONSTRAINT fk_ticket_id
        FOREIGN KEY (ticket_id)
            REFERENCES tickets (id)
            ON DELETE CASCADE
);
--endregion reservations

--region pictures
CREATE TABLE IF NOT EXISTS pictures
(
    id             SERIAL PRIMARY KEY,
    data_object_id INT          NOT NULL,
    picture_url    VARCHAR(255) NOT NULL,
    alt_text       VARCHAR(255) NOT NULL,
    hover_text     VARCHAR(255) NOT NULL,
    is_active      BOOLEAN      NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_dataObject_id
        FOREIGN KEY (data_object_id)
            REFERENCES data_objects (id)
            ON DELETE NO ACTION
);
--endregion pictures

--region tours
CREATE TABLE IF NOT EXISTS tours
(
    id              SERIAL PRIMARY KEY,
    date            TIMESTAMPTZ   NOT NULL,
    language        language_enum NOT NULL,
    available_spots INT           NOT NULL,
    price           DECIMAL(6, 2) NOT NULL,
    is_active       BOOLEAN       NOT NULL DEFAULT TRUE
);
--endregion tours

--region tour_tickets
CREATE TABLE IF NOT EXISTS tour_tickets
(
    ticket_id INT PRIMARY KEY,
    tour_id   INT NOT NULL,
    CONSTRAINT fk_tour_id
        FOREIGN KEY (tour_id)
            REFERENCES tours (id)
            ON DELETE NO ACTION
);
--endregion tour_tickets

--region venues
CREATE TABLE IF NOT EXISTS venues
(
    id         SERIAL PRIMARY KEY,
    address_id INT NOT NULL,
    CONSTRAINT fk_address_id
        FOREIGN KEY (address_id)
            REFERENCES addresses (id)
            ON DELETE NO ACTION
);
--endregion venues

--region shows
CREATE TABLE IF NOT EXISTS shows
(
    id              SERIAL PRIMARY KEY,
    venue_id        INT           NOT NULL REFERENCES venues (id),
    name            VARCHAR(255)  NOT NULL,
    date_time       TIMESTAMPTZ   NOT NULL,
    available_spots INT           NOT NULL,
    price           DECIMAL(6, 2) NOT NULL
);
--endregion shows

--region music_tickets
CREATE TABLE IF NOT EXISTS music_tickets
(
    ticket_id INT PRIMARY KEY REFERENCES tickets (id),
    genre     genre_enum NOT NULL,
    CONSTRAINT fk_ticket_id
        FOREIGN KEY (ticket_id)
            REFERENCES tickets (id)
            ON DELETE CASCADE
);
--endregion music_tickets

--region single_tickets
CREATE TABLE IF NOT EXISTS single_tickets
(
    ticket_id INT PRIMARY KEY REFERENCES tickets (id),
    show_id   INT NOT NULL REFERENCES shows (id),
    CONSTRAINT fk_show_id
        FOREIGN KEY (show_id)
            REFERENCES shows (id)
            ON DELETE NO ACTION,
    CONSTRAINT fk_ticket_id
        FOREIGN KEY (ticket_id)
            REFERENCES tickets (id)
            ON DELETE CASCADE
);
--endregion single_tickets

--region day_passes
CREATE TABLE IF NOT EXISTS day_passes
(
    ticket_id INT PRIMARY KEY REFERENCES tickets (id),
    date      DATE NOT NULL,
    CONSTRAINT fk_ticket_id
        FOREIGN KEY (ticket_id)
            REFERENCES tickets (id)
            ON DELETE CASCADE
);
--endregion day_passes

--region access_passes
CREATE TABLE IF NOT EXISTS access_passes
(
    ticket_id INT PRIMARY KEY REFERENCES tickets (id),
    CONSTRAINT fk_ticket_id
        FOREIGN KEY (ticket_id)
            REFERENCES tickets (id)
            ON DELETE CASCADE
);
--endregion access_passes

--region artists
CREATE TABLE IF NOT EXISTS artists
(
    id                           SERIAL PRIMARY KEY,
    career_highlight_description TEXT NOT NULL,
    CONSTRAINT fk_artist_id
        FOREIGN KEY (id)
            REFERENCES data_objects (id)
            ON DELETE CASCADE
);
--endregion artists

--region lineups
CREATE TABLE IF NOT EXISTS lineups
(
    show_id   INT NOT NULL,
    artist_id INT NOT NULL,
    CONSTRAINT fk_show_id
        FOREIGN KEY (show_id)
            REFERENCES shows (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_artist_id
        FOREIGN KEY (artist_id)
            REFERENCES artists (id)
            ON DELETE NO ACTION,
    PRIMARY KEY (show_id, artist_id)
);
--endregion lineups

--region demo_songs
CREATE TABLE IF NOT EXISTS demo_songs
(
    id        SERIAL PRIMARY KEY,
    artist_id INT          NOT NULL,
    title     VARCHAR(255) NOT NULL,
    song_url  VARCHAR(255) NOT NULL,
    picture   VARCHAR(255) NOT NULL,
    is_active BOOLEAN      NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_artist_id
        FOREIGN KEY (artist_id)
            REFERENCES artists (id)
            ON DELETE CASCADE
);
--endregion demo_songs

--region pages
CREATE TABLE IF NOT EXISTS pages
(
    id        SERIAL PRIMARY KEY,
    page_name VARCHAR(255) NOT NULL
);
--endregion pages

--region content_blocks
CREATE TABLE IF NOT EXISTS content_blocks
(
    id             SERIAL PRIMARY KEY,
    page_id        INT  NOT NULL,
    encoded_string TEXT NOT NULL,
    CONSTRAINT fk_page_id
        FOREIGN KEY (page_id)
            REFERENCES pages (id)
            ON DELETE CASCADE
);
--endregion content_blocks
