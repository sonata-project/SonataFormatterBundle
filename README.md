SonataFormatterBundle
=====================

The ``SonataFormatterBundle`` provides text helper to format text.

PARSERS
-------

For now, only 3 parsers are available:

 - markdown
 - text : return the provided text with the ``nl2br`` function
 - raw : return the provided text


INSTALLATION
------------

Add the following entry to ``deps`` the run ``php bin/vendors install``.

    [KnpMarkdownBundle]
        git=http://github.com/knplabs/KnpMarkdownBundle.git
        target=/bundles/Knp/Bundle/MarkdownBundle

    [SonataFormatterBundle]
        git=http://github.com/sonata-project/SonataFormatterBundle.git
        target=/bundles/Sonata/FormatterBundle

Register the bundle in ``app/AppKernel.php``

    $bundles = array(
        // ...
        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
        new Sonata\FormatterBundle\SonataFormatterBundle(),
    );

Register namespace in ``app/autoload.php``

    $loader->registerNamespaces(array(
        // ...
        'Knp'              => __DIR__.'/../vendor/bundles',
        'Sonata'           => __DIR__.'/../vendor/bundles',
    ));

USAGE
-----

    // Use the service
    $html = $this->container->get('sonata.formatter.pool')->transform('markdown', $text);

    // Template usage
    {{ my_data | format_text('markdown') }}


Configuration
-------------


```
sonata_formatter:
    formatters:
        markdown:
            service: sonata.formatter.text.markdown
            extensions: []

        text:
            service: sonata.formatter.text.text
            extensions: []
```


Twig Usage
----------


```
    blog.content | format_text(blog.formatter)
```