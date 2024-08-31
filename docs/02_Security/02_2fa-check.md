# Two Factor Authentication

If the user login requires the 2FA step, you must ask them for the code and
complete the login process using this endpoint.

## Request

- **ENDPOINT** : `/security/2fa/check`
- **METHODS** : `POST`

| Parameter    | Type             | Required | Description |
|--------------|------------------|----------|-------------|
| _auth_code   | number or string | yes      | 2FA code    |

> NB: The `PHPSESSID` cookie must also be passed

## Response

### SUCCESS

A successful response:

```json
{
  "login": "success",
  "two_factor_required": true,
  "two_factor_complete": true
}
```
> The user can now use other endpoints 
> (provided the PHPSESSID cookie is passed with every request)

### ERROR

An error response (the user must try again, maybe the code expired):

```json
{
  "error": "2fa_failed",
  "two_factor_complete": false
}
```

> No other API requests will work until the user has completed both steps
> of authentication succesfully

### EXCEPTIONS

#### HTTP 403 Forbidden

You may receive this exception if the user is not in a 2FA process, 
eg - they have already authenticated fully or if they do not have 2FA enabled.

## Example

```shell
curl -X "POST" "https://127.0.0.1:8000/security/2fa/check" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -H 'Cookie: REMEMBERME=App.Entity.User%3AY2hyaXNAY3RvLnN1cHBvcnQ~%3A1724754992%3AiAnKekXlyyWQVloCB54sjThWAKVVyE_41XNl4nqTsIM~MEJkIwqKabR76ECf_Wtbg24oGyPp4N3PzQ8oncZLEhw~; PHPSESSID=6mrigkv3tnmekh3hlvmiej6itc' \
     -d $'{
  "_auth_code": 678233
}'
```
