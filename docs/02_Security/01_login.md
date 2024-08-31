# Login

The very first step in authentication.

## Request
- **ENDPOINT** : `/security/login`
- **METHODS** : `POST`

| Parameter    | Type    | Required | Description                         |
|--------------|---------|----------|-------------------------------------|
| email        | string  | yes      | email address of the user           |
| password     | string  | yes      | password for the user               |
| _remember_me | boolean | no       | remember the login for up to 1 week |

## Response

### SUCCESS

A successful response for a user without 2FA.

```json
{
  "login": "success",
  "two_factor_required": false
}
```

A successful response for a user with 2FA enabled:

```json
{
  "login": "success",
  "two_factor_required": true,
  "two_factor_complete": false
}
```

### ERROR

If the credentials are invalid you will get a `401 Unauthorized` response with a JSON body:

```json
{
  "error": "Invalid credentials."
}
```

## Example

```shell
curl -X "POST" "https://127.0.0.1:8000/security/login" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{
  "email": "test@example.com",
  "_remember_me": "true",
  "password": "secure-password"
}'
```
