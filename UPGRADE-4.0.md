UPGRADE FROM 3.x to 4.0
=======================

Ckeditor has been removed from formatter code, you will have to run:

```bash
bin/console ckeditor:install
```

and then:

```bash
bin/console assets:install
```

to regenerate assets.
