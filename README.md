Leadquizzes-backend
===================

A Symfony project created on December 01, 2019, 12:37 pm.

# Installation

### Clone repository:

`git clone https://franbueno@bitbucket.org/franbueno/leadquizzes-backend.git`

### Install dependencies:

`cd leadquizzes-backend`

`composer install`

### Run pre-configured MySQL5.7 server and adminer for management (Docker required):

`docker-compose up -d`

`docker-compose down` (Only if you want to stop containers and removes containers, networks, volumes, and images created by `up`)

See docker-compose.yml for information.
You can configure connection parameters in /config/parameters.yml

Then, access it via http://localhost:8080 or http://host-ip:8080 and login with user:`root`, password:`root` and db: `leadquizzes`

### Loading Fixtures (`REQUIRED`):

`php bin/console doctrine:schema:update --force`

`php bin/console doctrine:fixtures:load`

It will generate some quizzes and user profile, required to login with user:`admin` and password:`password`

### Generate the SSH keys:

`mkdir -p config/jwt`

`openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096` (Use phrase `quiz` so you won't need to edit paramenters.yml)

`openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`

We used LexikJWTAuthenticationBundle to provide JWT (Json Web Token) authentication for this Symfony API so we need to generate SSH keys.

### Run server:
`php bin/console server:run`

### Everything up and running?

Awesome, you can jump to Leadquizzes Angular SPA. Enjoy! 🚀
