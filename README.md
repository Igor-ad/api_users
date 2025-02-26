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
  Name               Method   Scheme   Host   Path
  api_users_list     GET      ANY      ANY    /v1/api/users
  api_users_show     GET      ANY      ANY    /v1/api/users/{id}
  api_users_create   POST     ANY      ANY    /v1/api/users
  api_users_update   PUT      ANY      ANY    /v1/api/users/{id}
  api_users_delete   DELETE   ANY      ANY    /v1/api/users/{id}
  api_login          POST     ANY      ANY    /api/login
```

### Security Warning!

####  The test version with the .env.test environment uses unhashed passwords in the database and json responses.


#### Response Examples

```
POST
create response:
{"id":4,"login":"user4","phone":"09645678","pass":"***"}

PUT
update response:
{"id":2}

DELETE
delete response:
{"success":true}

GET
list response:
[{"id":1,"login":"admin1","phone":"06712345"},{"id":2,"login":"user1","phone":"05098754"},{"id":3,"login":"user2","phone":"06398754"}]

GET
show response:
{"login":"user1","phone":"0671231","pass":"***"}
```

#### Errors:

```
Validation Error:
{"code":400,"errors":[{"field":"phone","message":"This value is too long. It should have 8 characters or less.","invalidValue":"050456789"}]}

Access denied:
{"code":403,"message":"Access denied to this resource."}

Login error:
{"code":401,"message":"Invalid credentials."}
{"code":401,"message":"Invalid JWT Token"}
{"code":401,"message":"Expired JWT Token"}[

Invalid method:
{"code":405,"message":"Method Not Allowed."}

Resource not found:
{"code":404,"error":"Not Found."}

Database exception:
{"code":500,"message":"Data Base Exception."}

Security exception:
{"code":500,Security Runtime Exception."}

Unexpected Value:
{"code":500,Unexpected Value."}

Bad Request:
{"code":500,Bad Request."}
```
