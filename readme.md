# QuidCo Test Application

Fetches an animated gif for a given search term.

## Configuration

Run `composer install`

Create a copy of `.env.example` and rename it `.env`

### Faker GIF Provider

By default `GIF_PROVIDER` is set to `faker` which will cause the application to generate fake GIF data using Faker.

### Giphy GIF Provider

If you want to use Giphy for GIF data, set `GIF_PROVIDER` to either `giphy-beta` or `giphy-production`,
depending on the type of API key you want to use for Giphy. Then set `GIPHY_BETA_API_KEY` or `GIPHY_PRODUCTION_API_KEY` as appropriate.

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
