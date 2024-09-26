# List/Search Clients

## Request

You can get a paginated list of all clients
The session user will need `ROLE_CLIENT_ADMINISTRATION` role and must
belong to client **1**.

- **ENDPOINT** : `/clients`
- **METHODS** : `GET`

| Parameter | Type    | Required | Description                                                 |
|-----------|---------|----------|-------------------------------------------------------------|
| enabled   | boolean | no       | filter by users that are specifically enabled or disabled   |
| name      | string  | no       | wildcard, case-insensitive search of the company name field |

## Response

### SUCCESS

A successful response:

```json
{
  "data": [
    {
      "id": 1,
      "companyName": "Test Client",
      "enabled": true
    }
  ],
  "meta": {
    "pagination": {
      "total": 1,
      "count": 1,
      "per_page": 100,
      "current_page": 1,
      "total_pages": 1,
      "links": {}
    }
  }
}
```

### ERROR

An error response (the user has not authenticated themself):

Returns a `HTTP 401 Unauthorized` status

### EXCEPTIONS

#### HTTP 403 Forbidden

The User must have the `ROLE_CLIENT_ADMINISTRATION` role or they will receive
this response.

## Example

```shell
curl "https://127.0.0.1:8000/clients" \
     -H 'Cookie: PHPSESSID=2ggacea2jd06ug7jbp4qvmjts6'
```
