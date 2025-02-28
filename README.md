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
{"statusCode":201,"message":"New user created.","data":{"user":{"id":14,"login":"user9","phone":"09645678"},"token":"***"}}

PUT
update response:
{"statusCode":200,"message":"User information has been updated.","data":{"user":{"id":1},"token":"***"}}

DELETE
delete response:
{"statusCode":200,"message":"The user has been removed from the system.","data":{"success":true}}

GET
list response:
{"statusCode":200,"message":"All users.","data":[{"id":1,"login":"admin1","phone":"06712345"},{"id":2,"login":"user1","phone":"0671231"},{"id":3,"login":"user2","phone":"09645678"}]}

GET
show response:
{"statusCode":200,"message":"View user information.","data":{"login":"user1","phone":"0671231"}}
```

### Errors

```
Validation Error:
{"status":"error","code":400,"message":"Validation exception.","errors":[{"field":"phone","message":"This value is too long. It should have 8 characters or less.","invalidValue":"050456789"}]}

Access denied:
{"status":"error","code":403,"message":"Access denied to this resource.","errors":null}

Login error:
{"code":401,"message":"Invalid credentials."}
{"code":401,"message":"Invalid JWT Token"}
{"code":401,"message":"Expired JWT Token"}

Invalid method:
{"status":"error","code":405,"message":"Method Not Allowed.","errors":null}

Resource not found:
{"status":"error","code":404,"message":"Not Found.","errors":null}

Database exception:
{"status":"error","code":500,"message":"Data Base Exception.","errors":null}

Security exception:
{"status":"error","code":500,"message":Security Runtime Exception.","errors":null}

Unexpected Value:
{"status":"error","code":500,"message":Unexpected Value.","errors":null}

Bad Request:
{"status":"error","code":500,"message":Bad Request.","errors":null}
```
