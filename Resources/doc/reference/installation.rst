Installation
============

Add the following entry to ``deps`` the run ``php bin/vendors install``::

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