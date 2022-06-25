.. index::
    single: Installation; Bundle; Configuration

Installation
============

First, you need to install `FOSCKEditorBundle`_.

Install SonataFormatterBundle:

.. code-block:: bash

    composer require sonata-project/formatter-bundle

Register the bundles in ``bundles.php`` file::

    // config/bundles.php

    return [
        // ...
        FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],
        Sonata\FormatterBundle\SonataFormatterBundle::class => ['all' => true],
    ];

You have to install Ckeditor:

.. code-block:: bash

    bin/console ckeditor:install

and install Symfony assets:

.. code-block:: bash

    bin/console assets:install

Configuration
=============

Add Twig configuration:

.. code-block:: yaml

    # config/packages/twig.yaml

    twig:
        debug:            '%kernel.debug%'
        strict_variables: '%kernel.debug%'

        form_themes:
            - '@SonataFormatter/Form/formatter.html.twig'

Now add SonataFormatter configuration:

.. code-block:: yaml

    # config/packages/sonata.yaml

    sonata_formatter:
        default_formatter: text
        formatters:
            text:
                service: sonata.formatter.text.text
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist

            richhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    # - sonata.formatter.twig.media # do not add this unless you are using media bundle.

.. _`FOSCKEditorBundle`: https://github.com/FriendsOfSymfony/FOSCKEditorBundle
