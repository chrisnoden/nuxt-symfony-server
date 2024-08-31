# Introduction

This repo is part of a client/server project. The client (front-end) is served using
[Nuxt v3](https://nuxt.com) and the server (back-end) serves RESTful JSON API
using [Symfony 7.1](https://symfony.com) built on [PHP 8.3](https://php.net)

- [Front-End Client Repository](https://github.com/chrisnoden/nuxt-symfony-client)
- [Back-End Server Repository](https://github.com/chrisnoden/nuxt-symfony-server)

The intention is that the back-end server is deployed behind a firewall and is only
accessible by the front-end (Nuxt) server. The BFF (back-end for front-end) pattern
is used in the front-end. 

Therefore, this Symfony codebase is not as
secure as a typical Symfony web/API project (eg CORS is not enabled).

However, I have configured 2FA for the Users and have implemented multi-tenant
functionality - limiting users who are not in Client `1` to their own data. 

## Dev Setup

This documentation applies to working on the project locally. It is not for deployment
to a remote server (eg staging/production).


### Dependencies

You will need a **PostgreSQL** (v16) server. This is provided for you in the docker compose configuration.
Edit the `.env.local` with the correct connection string for your database.

This works best with the [symfony cli](https://symfony.com/download).

### Starting

1. Ensure the `.env.local` reflects your local environment
2. `composer install`
3. `symfony serve -d`
4. `symfony console doctrine:migrations:migrate`
5. `symfony console doctrine:fixtures:load`

You should be able to visit: [https://127.0.0.1:8000/](https://127.0.0.1:8000/) 
and browse the API docs (which are in the project `/docs` directory).

> NB: The docs can only be browsed in a `dev` environment where `APP_ENV = dev`


## Test Suite

PHPUnit is used for testing. You will need a test db and to load the fixtures.
Ensure you have a valid `.env.test.local` file first.

1. `symfony console doctrine:database:create --env=test`
2. `symfony console doctrine:migrations:migrate --env=test`
3. `symfony console doctrine:fixtures:load --env=test`

Run the tests:
```bash
bin/phpunit
```
