## Run docker

```
docker-compose up
```

## Configure

### Command:

```
docker-compose exec app bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

php artisan dusk:chrome-driver
```

### Config .env

Config url frontend:
```
APP_URL_FE=http://localhost:{port}
```

Config mail test with <a href="https://mailtrap.io/"> Mailtrap </a>:
```
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```

Config mail receive contact:
```
MAIL_TO_ADDRESS_CONTACT=
MAIL_TO_NAME_CONTACT=
```


Config login with Facebook with test app <a href="https://developers.facebook.com">Meta for Developers</a>:
```
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

### Run queue
Run queue in docker with `php artisan queue:work`:

## Fix code style

```
./vendor/bin/pint
```

View even more detail about changes:
```
./vendor/bin/pint -v
```

Simply inspect code for style errors without actually changing the files:
```
./vendor/bin/pint --test
```

## Swagger

http://localhost/swagger


## Telescope

http://localhost/telescope

