Usage
=====

.. code-block:: php

    <?php
    // Use the service
    $html = $this->container->get('sonata.formatter.pool')->transform('markdown', $text);

.. code-block:: jinja

    # Template usage
    {{ my_data | format_text('markdown') }}


.. note::

    By default, the twig filter ``format_text`` is not marked as ``safe``. So if you want to ouput the correct result, just add the ``| raw`` filter.

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
        ->add('contentFormatter', 'sonata_formatter_type', array(
            'source_field' => 'rawContent',
            'target_field' => 'content'
        ))


When data is populated, the ``content`` property will be populated with the text transformed from the selected
transformer name and the ``rawContent`` property.

For instance, this can be used to pregenerate the content of a markdown blog post.

Twig Formatter
--------------

Twig formatter uses projects Twig Environment (registered within service container with name ``twig``).
All settings that affect the projects Twig Environment (like used template loader, enabled extensions etc.)
will also affect the Twig Formatter.

Also twig formatter cannot have extensions enabled.

Security warning
................

Since in most cases Twig Formatter will be used as the formatter in the administrative interface 
(like the one shown above with the form), be careful of allowing users to edit the templates, as 
this could potentially affect the safety of the system. You have to take care of the safety of 
your templates.

In exceptional cases, you can create a separate instance of Twig Environment, register it within 
service container and override Twig Formatter definition passing own secure Twig Environment as 
a parameter instead of project one.
