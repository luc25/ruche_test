INSTALLATION
====================================

- composer install
- update .env with right DB parameters
- create DB, schema and load fixtures :
``` bash
$ php bin/console doctrine:database:create && php bin/console doctrine:schema:update --force && php bin/console doctrine:fixtures:load --append
```
- start project : 
``` bash
$ symfony server:start
```
- get JWT token :
``` bash
$ curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/login_check -d '{"username":"lutin@santa.com","password":"lutin"}'
```
- POST Route /api/gift/upload (with JWT token) expects 2 body parameters : "warehouse" (name of the warehouse) and "file" (CSV file of the gifts)
- GET Route /api/gift/statistics (with JWT token) has one parameter "warehouse" (optional) to filter results by warehouse
