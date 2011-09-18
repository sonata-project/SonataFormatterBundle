SonataFormatterBundle
=====================

The ``SonataFormatterBundle`` provides text helper to format text.

Parsers
-------

For now, only 3 parsers are available:

 - markdown
 - text : return the provided text with the ``nl2br`` function
 - raw : return the provided text


Installation
------------

Add the following entry to ``deps`` the run ``php bin/vendors install``::

    [KnpMarkdownBundle]
        git=http://github.com/knplabs/KnpMarkdownBundle.git
        target=/bundles/Knp/Bundle/MarkdownBundle

    [SonataFormatterBundle]
        git=http://github.com/sonata-project/SonataFormatterBundle.git
        target=/bundles/Sonata/FormatterBundle

Register the bundle in ``app/AppKernel.php``::

    $bundles = array(
        // ...
        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
        new Sonata\FormatterBundle\SonataFormatterBundle(),
    );

Register namespace in ``app/autoload.php``::

    $loader->registerNamespaces(array(
        // ...
        'Knp'              => __DIR__.'/../vendor/bundles',
        'Sonata'           => __DIR__.'/../vendor/bundles',
    ));



Configuration
-------------


Edit the ``config.yml`` file and add::

    sonata_formatter:
        formatters:
            markdown:
                service: sonata.formatter.text.markdown
                extensions: []

            text:
                service: sonata.formatter.text.text
                extensions: []

Usage
-----

Usage::

    // Use the service
    $html = $this->container->get('sonata.formatter.pool')->transform('markdown', $text);

    // Template usage
    {{ my_data | format_text('markdown') }}


Note : By default the twig filter ``format_text`` is not marked as ``safe``. So if you want to ouput
the correct result, just add the ``| raw`` filter.

Form
----

The bundle provided a widget to format a text when the form is bound. Just declare 2 fields:

 - source content
 - formatter field

And initialize a form type::

    $formBuilder
        ->add('rawContent') // source content
        ->add('contentFormatter', 'sonata_formatter_type_selector', array(
            'source' => 'rawContent',
            'target' => 'content'
        ))


When data is populated the ``content`` property will be populated with the text transformed from the selected
transformer name and the ``rawContent`` property.

For instance, this can be used to pregenerate the content of a markdown blog post.
