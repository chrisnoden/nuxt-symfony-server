# Clients and Users

Almost all the API endpoints require an authenticated user. Please see the 
[Security Docs](../02_Security/00_index.md) for more information.

The logged in User is managed separately to the main Users data and
the logged in User will not be able to update or delete their own data
using the User endpoints. They must use the Session endpoints for this.

You should also refer to the [common error responses](../02_common_error_responses)
document.

## Contents

### The Logged In User (Session User)

1. [Get Session User Data](./01_get_session_user_data)
2. [Update Session User Data](./02_update_session_user_data.md)

### Clients

3. [List or Search Clients](./03_list_or_search_clients.md)
4. [Create or Update Client](./04_create_or_update_client.md)

### Users

6. [List or Search Users](./06_list_or_search_users.md)
7. [Find a Single User](./07_find_single_user.md)

