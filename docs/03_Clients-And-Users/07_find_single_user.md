# Find a specific user by UUID

Every user has a unique UUID as the primary key. This is always the best way
to return a specific User record.

## Request

The session user will need `ROLE_USER_ADMINISTRATION` role and, unless they belong
to client **1** they will only be able to search users within their own client.

- **ENDPOINT** : `/user/01915aef-1c8c-7518-b2a1-45539bfc378b`
- **METHODS** : `GET`


## Response

### SUCCESS

A successful response:

```json
{
  "id": "01915aef-1c8c-7518-b2a1-45539bfc378b",
  "client": {
    "name": "Another Company",
    "enabled": true
  },
  "name": "John Does",
  "email": "john.does@example.com",
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

#### HTTP 403 Forbidden

The User must have the `ROLE_USER_ADMINISTRATION` role or they will receive
this response.

#### HTTP 404 Not Found

You will receive this if the User is not found OR if you are trying to
fetch a User who is not in your Client and you are not in Client 1.

## Example

```shell
curl "https://127.0.0.1:8000/user/01915aef-1c8c-7518-b2a1-45539bfc378b" \
     -H 'Cookie: PHPSESSID=2ggacea2jd06ug7jbp4qvmjts6'
```
