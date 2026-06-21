-- SQL to create an admin user. Run in your MySQL shell or admin tool after creating the database.
-- Replace the password hash with the value you generated using PHP's password_hash().

INSERT INTO admin_users (username, password_hash) VALUES ('admin', '$2y$REPLACE_WITH_YOUR_GENERATED_HASH');
