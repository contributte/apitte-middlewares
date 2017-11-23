# Apitte/Middlewares

## Content

- [Installation - how to register a plugin](#plugin)
- [Configuration - how to configure](#configuration)
- [Middlewares - cycle](#tracy)
- [Playground - real examples](#playground)

## Plugin

This plugin requires [Apitte/Core](https://github.com/apitte/core) library.

At first you have to register the main extension.

```yaml
extensions:
    api: Apitte\Core\DI\ApiExtension
```

Secondly, add the `MiddlewaresPlugin` plugin.

```yaml
api:
    plugins:
        Apitte\Middlewares\DI\MiddlewaresPlugin:
```

## Configuration

You can configure a few options.

```
api:
    plugins: 
        Apitte\Middlewares\DI\MiddlewaresPlugin:
            tracy: true
            autobasepath: true
```

- `tracy` - Automatically register `Contributte\Middlewares\TracyMiddleware` with priority 100.
- `autobasepath` - Automatically register `Contributte\Middlewares\AutoBasePathMiddleware` with priority 200.

By default, the `Apitte\Middlewares\ApiMiddleware` is registered with priority 500. So you can add as many middlewares as you want.

## Middlewares

This package is based on [contributte/middlewares](https://github.com/contributte/middlewares). You should register also middleware extension in your config file.

```yaml
extensions:      
    middlewares: Contributte\Middlewares\DI\MiddlewaresExtension
    api: Apitte\Core\DI\ApiExtension
```

After that you feel the power of the middlewares.

If you wanna add a another middleware, just register a class with appropriate tags.

```
services:
  m1: 
    factory: App\Model\AppMiddleware1
    tags: [middleware: [priority: <int>]]
```

## Playground

I've made a repository with full applications for education.

Take a look: https://github.com/apitte/playground
