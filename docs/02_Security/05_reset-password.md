# Reset Password (Forgot Password)

A user can change their password as long as they have access to their email account.

This 2-step process begins by sending them an email containing a special link. Your
`.env.local` must contain a valid value for `FRONT_END_HOSTNAME` as this will be used
to build the special link.

## Step 1 - Begin
- **ENDPOINT** : `/security/reset-password/begin`
- **METHODS** : `POST`

| Parameter    | Type    | Required | Description                         |
|--------------|---------|----------|-------------------------------------|
| email        | string  | yes      | email address of the user           |

## Response

### SUCCESS

A successful response.

```json
{
  "message": "OK"
}
```

> This response will be returned for any provided email address - whether
> actually belonging to a valid User or not.

## Example

```shell
curl -X "POST" "https://127.0.0.1:8000/security/reset-password/begin" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{
  "email": "test@example.com"
}'
```

## Step 2 - Reset Password

Once the user has clicked the link containing the token, you must pass this token
and their new password to this endpoint:

- **ENDPOINT** : `/security/reset-password/reset`
- **METHODS** : `POST`

| Parameter | Type    | Required | Description                 |
|-----------|---------|----------|-----------------------------|
| token     | string  | yes      | token received via web link |
| password  | string  | yes      | new password, min 8 chars   |

## Response

### SUCCESS

A successful response.

```json
{
  "message": "OK"
}
```

> This response will normally be shown for any provided email address - whether
> actually belonging to a valid User or not.

### ERROR

If the token is invalid you will get a `400 Bad Request` response with a JSON body:

```json
{
  "message": "The reset password link is invalid. Please try to reset your password again."
}
```

## Example

```shell
curl -X "POST" "https://127.0.0.1:8000/security/reset-password/reset" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{
  "token": "r8ON0H057qLZbNl5bIfB0CYmLGSEvVri662PWbY2",
  "password": "!my-NEW-password!"
}'
```
