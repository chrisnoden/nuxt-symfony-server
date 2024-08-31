# Common errors/exceptions

All the authenticated endpoints (where we expect a valid session for a fully
logged-in user) may return one of these responses:

## Not Fully Logged In

If the user has two-factor authentication (2FA) enabled and they have not completed
the second step (entering the 6 digit code), then you will received this response:

`HTTP 401 Unauthorized`
```json
{
  "error": "access_denied",
  "two_factor_complete": false
}
```

