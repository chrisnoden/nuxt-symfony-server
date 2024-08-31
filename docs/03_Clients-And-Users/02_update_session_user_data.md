# Updated Session User Data

The logged in user can update their personal data via this endpoint

## Request

- **ENDPOINT** : `/session/me`
- **METHODS** : `POST`

| Parameter   | Type    | Required | Description                       |
|-------------|---------|----------|-----------------------------------|
| password    | string  | yes      | The current password for the user |
| name        | string  | no       | New full name                     |
| email       | string  | no       | New email address for the user    |
| newPassword | string  | no       | New password for the user         |

> The (current) password MUST be passed and valid for any of the changes to be effected.
> If changing the password then `password` is the old password - you should check
> the newPassword is correct client-side (eg with a "repeat new password" style field)

> If you are changing the email address then this is a 2 step process.
> The new email will not be reflected in the user data until step 2 has been completed 
> (See below).
> 
> The token generated for the email change expires after 4 hours. Repeated
> requests to the API to change the email will reset the expiry (ie 4 hours from "now")

## Response

### SUCCESS

A successful response returns the user data:

```json
{
  "id": "01915aab-435a-7785-91ae-cb1876f165f1",
  "client": {
    "name": "Test Client",
    "enabled": true
  },
  "name": "Another Example",
  "email": "test@example.com",
  "roles": [
    "ROLE_USER_ADMINISTRATION",
    "ROLE_USER"
  ],
  "enabled": true,
  "twoFactorEnabled": false
}
```

## Example

```shell
## Change name of user
curl -X "POST" "https://127.0.0.1:8000/session/me" \
     -H 'Content-Type: application/json' \
     -H 'Accept: application/json' \
     -H 'Cookie: PHPSESSID=2ggacea2jd06ug7jbp4qvmjts6' \
     -d $'{
  "name": "Another Example"
}'
```


## Confirming New Email Address

If you set a new email address this will trigger an email being sent (via MAILER)
to the new email address. A link in the email must be clicked which
will take the user to a page on the front-end (Nuxt) application.
eg `https://localhost:3000/security/confirm-email?token=01918461-821e-7264-bfd9-d3f983eb7a22`

You must pass this token to the confirm-email endpoint below:

- **ENDPOINT** : `/security/confirm-email`
- **METHODS** : `POST`

| Parameter | Type    | Required | Description |
|-----------|---------|----------|-------------|
| token     | string  | yes      | The token   |

### SUCCESS

A successful response contains the User data with the new email address.

```json
{
  "id": "01915aab-435a-7785-91ae-cb1876f165f1",
  "client": {
    "name": "Test Client",
    "enabled": true
  },
  "name": "Another Example",
  "email": "new-email@example.com",
  "roles": [
    "ROLE_USER_ADMINISTRATION",
    "ROLE_USER"
  ],
  "enabled": true,
  "twoFactorEnabled": false
}
```

### ERROR

If the token is invalid you will get a `400 Bad Request` response with a JSON body:

```json
{
  "message": "invalid token"
}
```

## Example

```shell
curl -X "POST" "https://127.0.0.1:8000/security/confirm-email" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{
  "token": "0191705a-e999-70a9-98e8-2fcb10788208"
}'
```
