# Session User Data

The logged-in user is managed via their Session. This helps avoid
confusion with the `/users` endpoints which are for searching and managing
all other users.


## Request

- **ENDPOINT** : `/session/me`
- **METHODS** : `GET`

You can fetch the data about the logged-in user. This is also a great way to check 
their session is still valid.

## Response

### SUCCESS

A successful response:

```json
{
  "id": "01915aab-435a-7785-91ae-cb1876f165f1",
  "client": {
    "name": "Test Client",
    "enabled": true
  },
  "name": "Test Example",
  "email": "test@example.com",
  "roles": [
    "ROLE_USER_ADMINISTRATION",
    "ROLE_USER"
  ],
  "enabled": true,
  "twoFactorEnabled": false
}
```

### ERROR

An error response (the user has not authenticated themself):

Returns a `HTTP 401 Unauthorized` status

### EXCEPTIONS

#### HTTP 401 Unauthorized

You will receive this exception if the user has not authenticated (logged in).

## Example

```shell
curl "https://127.0.0.1:8000/session/me" \
     -H 'Cookie: PHPSESSID=2ggacea2jd06ug7jbp4qvmjts6'
```
