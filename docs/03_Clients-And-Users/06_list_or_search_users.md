# List/Search Users

## Request

You can get a paginated list of all users.
The session user will need `ROLE_USER_ADMINISTRATION` role and, unless they belong
to client **1** they will only be able to search users within their own client.

- **ENDPOINT** : `/users`
- **METHODS** : `GET`

| Parameter | Type    | Required | Description                                                                                        |
|-----------|---------|----------|----------------------------------------------------------------------------------------------------|
| client    | number  | no       | client ID to filter<br/>_(only works if you are a member of client 1)_                             |
| email     | string  | no       | wildcard, case-insensitive search<br/>_eg `example` will search all email addresses for the string |
| enabled   | boolean | no       | filter by users that are specifically enabled or disabled                                          |
| name      | string  | no       | wildcard, case-insensitive search of the name field                                                |
| q         | string  | no       | wildcard quick search, searching both name and email address                                       |
| role      | string  | no       | search for users that have this role                                                               |


## Response

### SUCCESS

A successful response:

```json
{
  "data": [
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
    },
    {
      "id": "01915aed-2cc7-7c82-b4dd-1ae48e5356de",
      "client": {
        "name": "Another Company",
        "enabled": true
      },
      "name": "John Dont",
      "email": "john.dont@example.com",
      "roles": [
        "ROLE_USER"
      ],
      "enabled": false,
      "twoFactorEnabled": false
    },
    {
      "id": "01915aab-435a-7785-91ae-cb1876f165f1",
      "client": {
        "name": "Test Company",
        "enabled": true
      },
      "name": "Test Users",
      "email": "test@example.com",
      "roles": [
        "ROLE_USER_ADMINISTRATION",
        "ROLE_CLIENT_ADMINISTRATION",
        "ROLE_USER"
      ],
      "enabled": true,
      "twoFactorEnabled": false
    }
  ],
  "meta": {
    "pagination": {
      "total": 3,
      "count": 3,
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

The User must have the `ROLE_USER_ADMINISTRATION` role or they will receive
this 403 error response.

## Example

```shell
curl "https://127.0.0.1:8000/users" \
     -H 'Cookie: PHPSESSID=2ggacea2jd06ug7jbp4qvmjts6'
```
