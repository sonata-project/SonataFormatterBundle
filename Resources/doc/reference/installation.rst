Installation
============

Add the following entry to ``deps`` the run ``php bin/vendors install``::

    [KnpMarkdownBundle]
        git=http://github.com/KnpLabs/KnpMarkdownBundle.git
        target=/bundles/Knp/Bundle/MarkdownBundle

    [SonataFormatterBundle]
        git=http://github.com/sonata-project/SonataFormatterBundle.git
        target=/bundles/Sonata/FormatterBundle

Register the bundle in ``app/AppKernel.php``:

.. code-block:: php

    <?php
    $bundles = array(
        // ...
        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
        new Sonata\FormatterBundle\SonataFormatterBundle(),
    );

Register namespace in ``app/autoload.php``:

.. code-block:: php

    <?php
    $loader->registerNamespaces(array(
        // ...
        'Knp'              => __DIR__.'/../vendor/bundles',
        'Sonata'           => __DIR__.'/../vendor/bundles',
    ));

Configuration
-------------

Edit the ``config.yml`` file and add:

.. code-block:: yaml

    sonata_formatter:
        formatters:
            markdown:
                service: sonata.formatter.text.markdown
                extensions: []

            text:
                service: sonata.formatter.text.text
                extensions: []