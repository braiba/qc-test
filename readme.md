# QuidCo Test Application

Fetches an animated gif for a given search term.

## Configuration

Run `composer install`

Create a copy of `.env.example` and rename it `.env`

## Usage

### Command line

Search for a GIF:

```
php artisan gif:search {query}
```

Retrieve a random GIF:

```
php artisan gif:random
```
