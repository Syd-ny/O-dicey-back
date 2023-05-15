# Configuration du back : O'Dicey

## Installation

### Install the project

| `git clone git@github.com:O-clock-Photon-Proton-Apotheose/projet-04-o-dicey-back.git`

### Install the dependencies

| `composer install`

___

## Configuration

### Configure the .env to create and have access to the database

| `DATABASE_URL="mysql://login:!password!@127.0.0.1:3306/dbName?serverVersion=8&charset=utf8mb4"`
@@ -21,3 +25,13 @@
Your keys will land in `config/jwt/private.pem` and `config/jwt/public.pem` (unless you configured a different path).

They should get saved automatically in the .env file, but if not, do it manually.

___

## Access to the API

### Create your own user, using the following customized command

| `php bin/console odicey:create-user` followed by the arguments *email* *login* *password* *roles*

You can use this command to create an admin and get access to the API routes for testing and for connecting the DB with the frontend of the app.
