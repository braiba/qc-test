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

### RabbitMQ

Configure your the `RABBITMQ_*` environment variables as appropriate for your connection.

You will also need to set the `GIF_AMQP_EXCHANGE_NAME` and `GIF_AMQP_QUEUE_NAME`. This exchange and queue don't need to
exist already; they will be created when you first run the service if they don't already exist.

Run the AMQP GIF listener service:

```
php artisan amqp:gifs:listen
```

You can then submit requests to the exchange, using the queue name as the routing key. Making sure to include a
`reply_to` property which dictates the routing key that the reply will be sent with (to the exchange defined by
`GIF_AMQP_EXCHANGE_NAME`). You will need to configure a queue that is bound to this routing key in order to
access the replies.

Search for a GIF:

```
{
    "action": "search",
    "query": "(your search term)"
}
```

Retrieve a random GIF:

```
{
    "action": "random"
}
```
