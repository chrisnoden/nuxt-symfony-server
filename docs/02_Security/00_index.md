# Security / Authentication

The [Symfony Security](https://symfony.com/doc/current/security.html) bundle
is used for all login and session management related functionality.

This means that a PHPSESSID cookie must be available to the server.

A `User` may have two-factor authentication enabled (implemented via the
[Scheb bundle](https://symfony.com/bundles/SchebTwoFactorBundle)) and this
will be indicated in the initial Login response (if the credentials are valid).

### NB - Cookies

The Login and 2FA endpoints return 2 cookies:
- `PHPSESSID`
- `REMEMBERME`

It is important that these are stored in the client and passed to every subsequent
request or you will get a `401 Unauthorized` response!

## Contents

1. [Login](./01_login.md)
2. [2FA Check](./02_2fa-check.md)
3. [Logout](./03_logout.md)
4. [Enable 2FA](./04_enable-2fa.md)
5. [Reset Password](./05_reset-password.md)
