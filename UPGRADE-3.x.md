UPGRADE 3.x
===========

## Deprecated unspecified default_formatter configuration node

The `default_formatter` configuration node will become required.

```yaml
sonata_formatter:
    default_formatter: my_formatter
```

UPGRADE FROM 3.0 to 3.1
=======================

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes. 
You can't extend them anymore, because they are only loaded when running internal tests. 
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
