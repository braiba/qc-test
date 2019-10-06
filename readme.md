# QuidCo Test Application

Fetches an animated gif for a given search term.

## Configuration

Run `composer install`

Create a copy of `.env.example` and rename it `.env`

### Giphy

Set the `GIPHY_BETA_API_KEY` and `GIPHY_PRODUCTION_API_KEY` environment values in your `.env` file as appropriate.
If you want to use the beta key, set `GIF_PROVIDER` to `giphy-beta`, otherwise leave it as `giphy-production`

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

### API

Search for a GIF:

```
GET: /api/gif/search
```

This request requires your search term to be submitted in the  `query` parameter of the query string

Retrieve a random GIF:

```
GET: /api/gif/random
```
