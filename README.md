# REST API with Phalcon v6

A REST API developed with Phalcon v6. This document explains how the project is organised, how the main components interact, and the important design decisions to keep in mind when extending the codebase.

## Introduction

Our goal is to build a REST API that has:
- Slim/efficient design
- Middleware
- JSON (or other) responses
- Action/Domain/Responder implementation
- JWT token authentication

> This is not **THE** way to build a REST API with Phalcon. It is simply **A** way to do that. You can adopt this implementation if you wish, parts of it or none of it.

This application has evolved significantly with every video release. Several areas were implemented in one way and later refactored to demonstrate the design trade-offs and how earlier choices affect the codebase.

The main takeaways that we want to convey to developers are:
- The code has to be easy to read and understand
- Each component must do one thing and one thing only
- Components can be swapped out with others so the use of interfaces is essential
- Static analysis tools (PHPStan) must not produce errors
- Code coverage for tests must be at 100%
                                                    
### Videos on YouTube

- Part 01 https://youtu.be/f3wP_M_NFKc
- Part 02 https://youtu.be/VEZvUf_PdSY
- Part 03 https://youtu.be/LP64Doh0t4g
- Part 04 https://youtu.be/jCEZ2WMil8Q
- Part 05 https://youtu.be/syU_3cIXFMM
- Part 06 https://youtu.be/AgCbqW-leCM
- Part 07 https://youtu.be/tGV4pSyVLdI
- Part 08 https://youtu.be/GaJhNnw_1cE
- Part 09 https://youtu.be/CWofDyTdToI
- Part 10 https://youtu.be/8YUrGAbafaA

## Directory Structure

The directory structure for this projects follows the recommendations of [pds/skeleton][pds_skeleton]

The folders contain:

- `bin`: empty for now, we might use it later on
- `config`: .env configuration files for CI and example
- `docs`: documentation (TODO)
- `public`: entry point of the application where `index.php` lives
- `resources`: stores database migrations and docker files for local development
- `src`: source code of the project
- `storage`: various storage data such as application logs
- `tests`: tests

## High-level architecture

The application follows the [ADR pattern][adr_pattern] where the application is split into an `Action` layer, the `Domain` layer and a `Responder` layer.

- `Action` — receives HTTP input, collects and sanitizes request data, and calls a Domain service.
- `Domain` — contains the application logic. Implements small components, services that map to endpoints, validators, repositories and helpers.
- `Responder` — builds and emits the HTTP response from a `Payload`.

Core files live under `src/` and are registered in the DI container in `src/Domain/Components/Container.php`.

## Main components

### `Action` layer

Contains a handler that translate HTTP requests into Domain calls. For example, actions route requests to `LoginPostService`, `LogoutPostService`, `RefreshPostService` etc.

### `Domain` layer

#### `ADR`

- `Payload`: A uniform result object used across Domain → Responder.
- `Input`: Class collecting request input and used to pass it to the domain
- Interfaces for domain and `Input`

#### `Infrastructure`

##### `Constants`

Classes with constants and helper methods used throughout the application 

##### `DataSource`

**`Auth`**

Contains Data Transfer Objects (DTOs) to move data from input to domain and from database back to domain. A Facade is available for orchestration, sanitizer for input as well as validators.

**`Interfaces`**

Mapper and Sanitizer interfaces

**`User`**

Contains Data Transfer Objects (DTOs) to move data from input to domain and from database back to domain. A Facade is available for orchestration, a repository for database operations, sanitizer for input as well as validators.

**`Validation`**

Contains the `ValidatorInterface` for all validators, a `Result` object for returning back validation results/errors and the `AbsInt` validator to check the id for `Put` operations.

##### `Encryption`

Contains components for JWT handling and passwords. The `Security` component is a wrapper for the `password_*` PHP classes, which are used for password hashing and verification.

The `TokenManager` offers methods to issue, refresh and revoke tokens. It works in conjunction with the `TokenCache` to store or invalidate tokens stored in Cache (Redis)

##### `Enums`

There are several enumerations present in the application. Those help with common values for tasks. For example the `FlagsEnum` holds the values for the `co_users.usr_status_flag` field. We could certainly introduce a lookup table in the database for "status" and hold the values there, joining it to the `co_users` table with a lookup table. However, this will introduce an extra join in our query which will inevitable reduce performance. Since the `FlagsEnum` can keep the various statuses, we keep everything in code instead of the database. Thorough tests for enumerations ensure that if a change is made in the future, tests will fail, so that database integrity can be kept.

The `RoutesEnum` holds the various routes of the application. Every route is represented by a specific element in the enumeration and the relevant prefix/suffix are calculated for each endpoint. Also, each endpoint is mapped to a particular service, registered in the DI container, so that the action handler can invoke it when the route is matched.

Finally, the `RoutesEnum` also holds the middleware array, which defines their execution and the "hook" they will execute in (before/after).

##### `Env`

The environment manager and adapters. It reads environment variables using [DotEnv][dotenv] as the main adapter but can be extended if necessary.

##### `Exceptions`

Exception classes used in the application.

##### `Container`

The application uses the `Phalcon\Di\Di` container with minimal components lazy loaded. Each non "core" component is also registered there (i.e. domain services, responder etc.) and all necessary dependencies are injected based on the service definitions.

Additionally there are two `Providers` that are also registered in the DI container for further functionality. The `ErrorHandlerProvider` which caters for the starting up/shut down of the application and error logging, and the very important `RoutesProvider` which handles registering all the routes that the application serves.

#### `Services`:

Separated also in `User` and `Auth` it contains the classes that the action handler will invoke. The naming of these services shows what endpoint they are targeting and what HTTP method will invoke them. For example the `LoginPostService` will be a `POST` to the `/auth/login`.

### `Responder`

The `JsonResponder` responder is responsible for constructing the response with the desired output, and emitting it back to the caller. For the moment we have only implemented a JSON response with a specified array as the payload to be sent back.

The responder receives the outcome of the Domain, by means of a `Payload` object. The object contains all the data necessary to inject in the response.

#### Response payload

The application responds always with a specific JSON payload. The payload contains the following nodes:
- `data` - contains any data that are returned back (can be empty)
- `errors` - contains any errors occurred (can be empty)
- `meta` - array of information regarding the payload
    - `code` - the application code returned
    - `hash` - a `sha1` hash of the `data`, `errors` and timestamp
    - `message` - `success` or `error`
    - `timestamp` - the time in UTC format


## Request flow (example: login)

1. Route matches and middleware runs (see Middleware section below).
2. `Action` extracts request body and calls `LoginPostService->handle($data)`.
3. `LoginPostService` calls the `AuthFacade->authenticate($input, $loginValidator)` (method injection).
4. `AuthFacade`:
    - Builds DTO via `AuthInput`.
    - Calls the supplied validator (`AuthLoginValidator`) which returns a `Result`.
    - On success, fetches user via repository and verifies credentials (`Security`).
    - Issues tokens via `TokenManager`.
    - Returns a `Payload::success(...)`.
5. `Responder` builds JSON and returns HTTP response.

## Validators

- Specific validators exist for each potential input that needs to be validated
- Method injection is used for validators: the `AuthFacade` does not require a single validator in its constructor. Instead, callers pass the appropriate validator to each method: login uses `AuthLoginValidator`, logout/refresh use `AuthTokenValidator`.
- The validation `Result` supports `meta` data. Token validators may perform repository lookups and attach the resolved `User` to `ValidationResult->meta['user']` to avoid repeating DB queries. The facade reads that meta on success.

## Token management and cache

- `TokenManager` depends on a domain-specific `TokenCacheInterface` rather than a raw PSR cache. This keeps token-specific operations discoverable and testable.
- `TokenCache` enhances the Cache operations by providing token specific operations for storing and invalidating tokens. 
- `TokenCacheInterface` defines token operations like `storeTokenInCache` and `invalidateForUser`.

## Middleware sequence

There are several middleware registered for this application and they are being executed one after another (order matters) before the action is executed. As a result, the application will stop executing if an error occurs, or if certain validations fail. Middleware returns early with a `Payload` error when validation fails.

The middleware execution order is defined in the `RoutesEnum`. The available middleware is:

- [NotFoundMiddleware.php](src/Domain/Infrastructure/Middleware/NotFoundMiddleware.php)
- [HealthMiddleware.php](src/Domain/Infrastructure/Middleware/HealthMiddleware.php)
- [ValidateTokenClaimsMiddleware.php](src/Domain/Infrastructure/Middleware/ValidateTokenClaimsMiddleware.php)
- [ValidateTokenPresenceMiddleware.php](src/Domain/Infrastructure/Middleware/ValidateTokenPresenceMiddleware.php)
- [ValidateTokenRevokedMiddleware.php](src/Domain/Infrastructure/Middleware/ValidateTokenRevokedMiddleware.php)
- [ValidateTokenStructureMiddleware.php](src/Domain/Infrastructure/Middleware/ValidateTokenStructureMiddleware.php)
- [ValidateTokenUserMiddleware.php](src/Domain/Infrastructure/Middleware/ValidateTokenUserMiddleware.php)

**NotFoundMiddleware**

Checks if the route has been matched. If not, it will return a `Resource Not Found` payload

**HealthMiddleware**

Invoked when the `/health` endpoint is called and returns a `OK` payload

**ValidateTokenPresenceMiddleware**

Checks if a JWT token is present in the `Authorization` header. If not, an error is returned

**ValidateTokenStructureMiddleware**

Gets the JWT token and checks if it can be parsed. If not, an error is returned

**ValidateTokenUserMiddleware**

Gets the userId from the JWT token, along with other information, and tries to match it with a user in the database. If the user is not found, an error is returned

**ValidateTokenClaimsMiddleware**

Checks all the claims of the JWT token to ensure that it validates. For instance, this checks the token validity (expired, not before), the claims, etc. If a validation error happens, then an error is returned.

**ValidateTokenRevokedMiddleware**

Checks if the token has been revoked. If it has, an error is returned


[adr_pattern]: https://github.com/pmjones/adr
[pds_skeleton]: https://github.com/php-pds/skeleton
[dotenv]: https://github.com/vlucas/phpdotenv
