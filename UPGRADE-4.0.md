UPGRADE FROM 3.x to 4.0
=======================

# Assets
Ckeditor has been removed from formatter code, you will have to run:

```bash
bin/console ckeditor:install
```

and then:

```bash
bin/console assets:install
```

to regenerate assets.

# Type hints

PHP 7.1 type hints have been added wherever possible, you have to add them in
types extending or implementing ours.
