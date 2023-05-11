# Configuration du back : O'Dicey

## Installation

### Install the project

| `git clone git@github.com:O-clock-Photon-Proton-Apotheose/projet-04-o-dicey-back.git`

### Install the dependencies

| `composer install`

### Configure the .env to create and have access to the database

| `DATABASE_URL="mysql://login:!password!@127.0.0.1:3306/dbName?serverVersion=8&charset=utf8mb4"`

### Generate the JWT keys with the following command

| `php bin/console lexik:jwt:generate-keypair`

Your keys will land in `config/jwt/private.pem` and `config/jwt/public.pem` (unless you configured a different path).

They should get saved automatically in the .env file, but if not, do it manually.
