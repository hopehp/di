### Create container

```php
$container = new Hope\Di\Container();
```

### Register values

```php
$container->add('App\Session');

$container->add('session', 'App\Session')
    ->method('injectStorage', 'App\StorageInterface');

$container->add('storage', 'App\Storage')
    ->property('driver', 'mysql');

$session = $container->get('storage');

```