# Create a new Client

> Only a user belonging to client 1 with role `ROLE_CLIENT_ADMINISTRATION` can
> create or modify Client data.

## Request

- **ENDPOINT** : `/client`
- **METHODS** : `PUT`

| Parameter   | Type    | Required | Description           |
|-------------|---------|----------|-----------------------|
| companyName | string  | yes      | Client/company name   |
| enabled     | boolean | no       | Is the client enabled |

> If the client is disabled, none of their Users will be able to login.
> The companyName must be unique

## Response

### SUCCESS

A successful response returns the new client data:

```json
{
  "id": 4,
  "name": "this is a new company",
  "enabled": true
}
```

## Example

```shell
## Create Client
curl -X "PUT" "https://127.0.0.1:8000/client" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -H 'Cookie: PHPSESSID=mcbs80l32m9dkat1j5om9antbn' \
     -d $'{
  "enabled": true,
  "companyName": "this is a new company"
}'
```


# Modify an existing Client

> Only a user belonging to client 1 with role `ROLE_CLIENT_ADMINISTRATION` can
> create or modify Client data.

## Request

- **ENDPOINT** : `/client/{id}`
- **METHODS** : `POST`

| Parameter   | Type    | Required | Description           |
|-------------|---------|----------|-----------------------|
| companyName | string  | yes      | Client/company name   |
| enabled     | boolean | no       | Is the client enabled |

> If the client is disabled, none of their Users will be able to login.
> The companyName must be unique

## Response

### SUCCESS

A successful response returns the new client data:

```json
{
  "id": 4,
  "name": "this is the modified company",
  "enabled": false
}
```

## Example

```shell
## Create Client
curl -X "POST" "https://127.0.0.1:8000/client/4" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -H 'Cookie: PHPSESSID=mcbs80l32m9dkat1j5om9antbn' \
     -d $'{
  "enabled": false,
  "companyName": "this is the modified company"
}'
```
