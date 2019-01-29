.. index::
    single: Installation; Bundle; Configuration

Installation
============

If you are using Symfony4 you will need to make some adjustments, if not
you can skip to "Install SonataFormatterBundle".

First you will need to add a line to your `composer.json`:

.. code-block:: javascript

    "replace": {
        "egeloen/ckeditor-bundle": "*"
    }

And after that you need to install `FOSCKEditorBundle`_.

Install SonataFormatterBundle:

.. code-block:: bash

    composer require sonata-project/formatter-bundle

Register the bundles in ``bundles.php`` file::

    // config/bundles.php

    return [
        // ...
        Knp\Bundle\MarkdownBundle\KnpMarkdownBundle::class => ['all' => true],
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
            markdown:
                service: sonata.formatter.text.markdown
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
            #        - sonata.media.formatter.twig #keep this commented unless you are using media bundle.

            text:
                service: sonata.formatter.text.text
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
            #        - sonata.media.formatter.twig

            rawhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
            #        - sonata.media.formatter.twig

            richhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
            #        - sonata.media.formatter.twig

            twig:
                service: sonata.formatter.text.twigengine
                extensions: [] # Twig formatter cannot have extensions

.. _`FOSCKEditorBundle`: https://github.com/FriendsOfSymfony/FOSCKEditorBundle
