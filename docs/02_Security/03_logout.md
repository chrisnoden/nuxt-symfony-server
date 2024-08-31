# Logout

You can just logout by deleting the `PHPSESSID` cookie or:

## Request
- **ENDPOINT** : `/security/logout`
- **METHODS** : `POST` `GET`

## Response

### SUCCESS

You will get a `HTTP 204` (No Content) status and an empty body

## Example

```shell
curl -X "POST" "https://127.0.0.1:8000/security/logout" \
     -H 'Content-Type: application/json; charset=utf-8'
```
