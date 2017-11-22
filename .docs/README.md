# Apitte/Debug

## Content

- [Installation - how to register a plugin](#plugin)
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

## Middlewares

This package is based on [contributte/middlewares](https://github.com/contributte/middlewares). You should register also middleware extension in your config file.

```yaml
extensions:
    middlewares: Contributte\Middlewares\DI\MiddlewaresExtension
    api: Apitte\Core\DI\ApiExtension
```

After that you feel the power of the middlewares.

## Playground

I've made a repository with full applications for education.

Take a look: https://github.com/apitte/playground
