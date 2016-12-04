UPGRADE 3.x
===========

## Deprecated unspecified default_formatter configuration node

The `default_formatter` configuration node will become required.

```yaml
sonata_formatter:
    default_formatter: my_formatter
```

## Deprecated injecting a logger through the Sonata\FormatterBundle\Formatter\Pool constructor

Providing a logger to a `Sonata\FormatterBundle\Formatter\Pool` instance should
now be done through the `setLogger` method.


Before:

```php
use Sonata\FormatterBundle\Formatter\Pool;

new Pool($myLogger, 'myFormatter');
```

After:

```php
$pool = new Pool('myFormatter');
$pool->setLogger($myLogger);
```

## Deprecated unspecified default formatter in Sonata\FormatterBundle\Formatter\Pool constructor

Instantiating the `Sonata\FormatterBundle\Formatter\Pool` class should be done
with a `$defaultFormatter` argument.

Before:

```php
use Sonata\FormatterBundle\Formatter\Pool;

new Pool();
```

After:

```php
use Sonata\FormatterBundle\Formatter\Pool;

new Pool('myFormatter');
```

UPGRADE FROM 3.0 to 3.1
=======================

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes. 
You can't extend them anymore, because they are only loaded when running internal tests. 
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
