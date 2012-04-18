Usage
=====

.. code-block:: php

    <?php
    // Use the service
    $html = $this->container->get('sonata.formatter.pool')->transform('markdown', $text);

.. code-block:: jinja

    # Template usage
    {{ my_data | format_text('markdown') }}


Note : By default the twig filter ``format_text`` is not marked as ``safe``. So if you want to ouput
the correct result, just add the ``| raw`` filter.

Form
----

The bundle provided a widget to format a text when the form is bound. Just declare 2 fields:

 - source content
 - formatter field

And initialize a form type:

.. code-block:: php

    <?php
    $formBuilder
        ->add('rawContent') // source content
        ->add('contentFormatter', 'sonata_formatter_type_selector', array(
            'source' => 'rawContent',
            'target' => 'content'
        ))


When data is populated the ``content`` property will be populated with the text transformed from the selected
transformer name and the ``rawContent`` property.

For instance, this can be used to pregenerate the content of a markdown blog post.
