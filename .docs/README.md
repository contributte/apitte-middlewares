# Apitte Middlewares

[PSR-15](https://www.php-fig.org/psr/psr-15/) middlewares for [Apitte](https://github.com/apitte/core).

Transform and validate request or early return response before it is handled by dispatcher.

## Content

- [Setup](#setup)
- [Configuration](#configuration)
- [Middlewares](#middlewares)

## Setup

First of all, setup [core](https://github.com/apitte/core).

Install and register middlewares plugin

```bash
composer require apitte/middlewares
```

```yaml
api:
    plugins: 
        Apitte\Middlewares\DI\MiddlewaresPlugin:
```

## Configuration

TODO - EnforceHttpsMiddleware, BasicAuthMiddleware, RequestLoggingMiddleware

```yaml
api:
    plugins: 
        Apitte\Middlewares\DI\MiddlewaresPlugin:
            autoBasePath: true
            methodOverride: true
```

## Register middlewares

If you want to add another middleware, just register a class with appropriate tags.

```yaml
services:
    m1: 
        factory: App\Api\Middleware\ExampleMiddleware
        tags: [apitte.middleware: [priority: 10]]
```

## Own middlewares

```php
namespace App\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExampleMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request,RequestHandlerInterface $handler) : ResponseInterface {
    	// Do something
    	// Return new response or call next middleware in a row
    	return $handler->handle($request);
    }

}
```
