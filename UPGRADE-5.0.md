UPGRADE FROM 3.x to 4.0
=======================

## Deprecations

All the deprecated code introduced on 4.x is removed on 5.0.

Please read [4.x](https://github.com/sonata-project/SonataFormatterBundle/tree/4.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/SonataFormatterBundle/compare/4.x...5.0.0).

## Container parameters

We have been using container parameters as undocumented extension points for things like classes and configurations.

In SonataMediaBundle 4.0 those are completely removed and we encourage you to use the default
dependency injection override to change the default values for the removed service configurations if you need to.

If you need to change something that you believe it should be handled somehow in configuration,
please open an issue and we will discuss it.
