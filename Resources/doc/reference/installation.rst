Installation
============

To begin, add the dependent bundles::

    php composer.phar require sonata-project/formatter-bundle

Register the bundle in ``app/AppKernel.php``:

.. code-block:: php

    <?php
    $bundles = array(
        // ...
        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
        new Sonata\FormatterBundle\SonataFormatterBundle(),
    );

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

            twig:
                service: sonata.formatter.text.twigengine
                extensions: [] # Twig formatter cannot have extensions