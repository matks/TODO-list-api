TODO List API
=============

[Silex](http://silex.sensiolabs.org/) API application for a todo-list app

# Install

Install/download [composer](https://getcomposer.org/)

Then install vendors using the composer binary
```
$ composer install
```

Then copy the `app/parameters.yml.dist` file to `app/parameters.yml` and
fill it with the configuration of you server.

```
$ cp app/parameters.yml.dist app/parameters.yml
```

Create the SQL schema with Doctrine command tools:
```
$ php app/console orm:schema-tool:create
```

Setup a virtual host for prod environment.

For dev environment, you can use PHP built-in server:
```
$ php -S localhost:8080 -t web web/index_dev.php
```

## Usage

Available API endpoints are:
```
GET /items : get all items
GET /item/{id} : get item by id
POST /item/{id} : create item
PUT /item/{id} : update item
POST /item/{id}/start-progress : start progress on item
POST /item/{id}/complete : complete item
POST /item/{id}/reset-status : reset item status
DELETE /item/{id} : delete item
```
