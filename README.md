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

