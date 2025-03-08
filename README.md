# Symfony User API Application

Simple Symfony API for user management.

The application uses the Bearer JWT authentication method.

### Requirements:

PHP >=8.2, composer, git, docker-compose


### Initializing the application

```
cd /projects
git clone https://github.com/Igor-ad/api_users.git
cd ./api_users
cp ./src/.env.dev ./src/.env
composer install
docker-compose up
php bin/console doctrine:migrations:migrate
## php bin/console doctrine:fixtures:load ## Test Data
```

### Routes

```
  Name               Method   Scheme   Host         Path
  api_users_list     GET      http     localhost   /v1/api/users
  api_users_show     GET      http     localhost   /v1/api/users/{id}
  api_users_create   POST     http     localhost   /v1/api/users
  api_users_update   PUT      http     localhost   /v1/api/users/{id}
  api_users_delete   DELETE   http     localhost   /v1/api/users/{id}
  api_login          POST     http     localhost   /api/login
```

### Security Warning!

####  The test version with the .env.test environment uses unhashed passwords in the database.


### Responses

```
POST
create response:
{
    "statusCode": 201,
    "message": "New user created.",
    "data": {
        "id": 5,
        "login": "user3",
        "phone": "0335577",
        "token": "***",
        "roles": [
            "ROLE_USER"
        ]
    }
}

PUT
update response:
{
    "statusCode": 200,
    "message": "User information has been updated.",
    "data": {
        "id": 1,
        "token": "***",
        "roles": [
            "ROLE_USER"
        ]
    }
}

DELETE
delete response:
{
    "statusCode": 200,
    "message": "The user has been removed from the system.",
    "data": {
        "success": true
    }
}

GET
list response:
{
    "statusCode": 200,
    "message": "All users.",
    "data": [
        {
            "id": 1,
            "login": "admin1",
            "phone": "06712345",
            "roles": [
                "ROLE_ADMIN",
                "ROLE_USER"
            ]
        },
        {
            "id": 2,
            "login": "user1",
            "phone": "0671231",
            "roles": [
                "ROLE_USER"
            ]
        },
        {
            "id": 3,
            "login": "user2",
            "phone": "09645678",
            "roles": [
                "ROLE_USER"
           ]
        }
    ]
}


GET
show response:
{
    "statusCode": 200,
    "message": "View user information.",
    "data": [
        {
            "login": "user1",
            "phone": "0671231",
            "roles": [
                "ROLE_USER"
            ]
        }
    ]
}
```

### Errors

```
Validation Error:
{
    "status": "Bad Request",
    "code": 400,
    "message": "Validation exception.",
    "errors": [
        {
            "field": "login",
            "violationMessage": "This value should not be blank.",
            "invalidValue": null
        },
        {
            "field": "phone",
            "violationMessage": "This value is too long. It should have 8 characters or less.",
            "invalidValue": "113355991"
        },
        {
            "field": "pass",
            "violationMessage": "This value should not be blank.",
            "invalidValue": null
        }
    ]
}

Access denied:
{
    "status": "Internal Server Error",
    "code": 500,
    "message": "Access Denied by #[IsGranted(\"USER_VIEW\", \"user\")] on controller",
    "errors": null
}

Login error:
{"code":401,"message":"Invalid credentials."}
{"code":401,"message":"Invalid JWT Token"}
{"code":401,"message":"Expired JWT Token"}

Invalid method:
{
    "status": "Method Not Allowed",
    "code": 405,
    "message": "No route found for \"POST http://localhost/v1/api/users/1\": Method Not Allowed (Allow: GET, PUT, DELETE)",
    "errors": null
}

Resource not found:
{
    "status": "Not Found",
    "code": 404,
    "message": "No route found for \"GET http://localhost/v1/api/user\"",
    "errors": null
}

{
    "status": "Not Found",
    "code": 404,
    "message": "\"App\\Entity\\User\" object not found by \"Symfony\\Bridge\\Doctrine\\ArgumentResolver\\EntityValueResolver\".",
    "errors": null
}

Database exception:
{
    "status": "Internal Server Error",
    "code": 500,
    "message": "An exception occurred while executing a query: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'user1' for key 'users.unique_login'",
    "errors": null
}

Security exception:
{"status":"error","code":500,"message":Security Runtime Exception.","errors":null}

Unexpected Value:
{
    "status": "Internal Server Error",
    "code": 500,
    "message": "The type of the \"login\" attribute for class \"App\\Entity\\User\" must be one of \"string\" (\"int\" given).",
    "errors": null
}

Bad Request:
{
    "status": "Bad Request",
    "code": 400,
    "message": "Extra attributes are not allowed (\"login-\" is unknown).",
    "errors": null
}
```
