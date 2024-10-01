INSERT INTO "clients" ("id", "company_name", "enabled", "created_at", "updated_at") VALUES
    (1, 'Test Company', 't', NOW(), NOW()),
    (2, 'Another Company', 't', NOW(), NOW())
;

--  all passwords = vafnLPiH.8@g
INSERT INTO "users" ("id", "client_id", "name", "email", "roles", "password", "enabled", "two_factor_status", "google_authenticator_secret", "created_at", "updated_at") VALUES
    ('01915aab-435a-7785-91ae-cb1876f165f1', 1,'Test User', 'test@example.com', '["ROLE_USER_ADMINISTRATION","ROLE_CLIENT_ADMINISTRATION"]', '$2y$13$26MSu1Ry8G1dcl8ElRtrHOpNxNe9BJ/4JpJp5mBEUnpanUu/VZPKy', 't', 'disabled', NULL, NOW(), NOW()),
    ('01915aed-2cc7-7c82-b4dd-1ae48e5356de', 2,'John Dont', 'john.dont@example.com', '[]', '$2y$13$26MSu1Ry8G1dcl8ElRtrHOpNxNe9BJ/4JpJp5mBEUnpanUu/VZPKy', 'f', 'disabled', NULL, NOW(), NOW()),
    ('01915aef-1c8c-7518-b2a1-45539bfc378b', 2,'John Does', 'john.does@example.com', '["ROLE_USER_ADMINISTRATION"]', '$2y$13$26MSu1Ry8G1dcl8ElRtrHOpNxNe9BJ/4JpJp5mBEUnpanUu/VZPKy', 't', 'disabled', NULL, NOW(), NOW())
;

SELECT setval('clients_id_seq', 3, false);
