.. index::
    single: Installation; Bundle; Configuration

Installation
============

To begin, add the dependent bundles:

.. code-block:: bash

    $ php composer.phar require sonata-project/formatter-bundle

Register the bundles in ``app/AppKernel.php``:

.. code-block:: php

    <?php

    // app/AppKernel.php

    $bundles = array(

        // ...

        // SonataMarkItUpBundle is deprecated. All assets are now available in formatter bundle
        // new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
        new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
        new Sonata\FormatterBundle\SonataFormatterBundle(),
    );

Configuration
=============

Edit the ``config.yml`` file and add these lines:

.. code-block:: yaml

    # Twig Configuration
    twig:
        debug:            "%kernel.debug%"
        strict_variables: "%kernel.debug%"

        #sonata
        
        #Uncomment the following 3 liness for Symfony2
        #form:
        #    resources:
        #        - 'SonataFormatterBundle:Form:formatter.html.twig'
        
        #Remove the following 2 lines for Symfony2
        form_themes:
            - 'SonataFormatterBundle:Form:formatter.html.twig'

    sonata_formatter:
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
