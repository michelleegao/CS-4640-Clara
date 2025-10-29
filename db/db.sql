-- db/db.sql

CREATE TABLE users_clara (
    id            SERIAL PRIMARY KEY,
    email         TEXT UNIQUE NOT NULL,
    password_hash TEXT        NOT NULL,
    display_name  TEXT        NOT NULL
);

-- one daily log entry per row
CREATE TABLE logs (
    id            SERIAL PRIMARY KEY,
    user_id       INT NOT NULL REFERENCES users_clara(id) ON DELETE CASCADE,
    log_date      DATE NOT NULL,
    locations     TEXT[] NOT NULL DEFAULT '{}'::text[],   -- optional default
    severity      TEXT NOT NULL CHECK (severity IN ('Mild','Moderate','Severe')),
    types         TEXT[] NOT NULL DEFAULT '{}'::text[],   -- optional default
    water_cups    INT  DEFAULT 0 CHECK (water_cups >= 0),
    activity      TEXT,
    notes         TEXT,
    created_at    TIMESTAMP NOT NULL DEFAULT NOW()
);

-- user’s current routine items
CREATE TABLE routine_products (
    id           SERIAL PRIMARY KEY,
    user_id      INT  NOT NULL REFERENCES users_clara_clara(id) ON DELETE CASCADE,
    name         TEXT NOT NULL,  -- free-text product name/label the user sees
    time_of_day  TEXT NOT NULL CHECK (time_of_day IN ('Morning','Night')),

    -- enforce allowed types:
    product_type TEXT NOT NULL CHECK (
        product_type IN (
            'cleanser','moisturizer','lotion','serum','toner',
            'sunscreen','retinoid','retinol'
        )
    ),

    is_active    BOOLEAN NOT NULL DEFAULT TRUE
);