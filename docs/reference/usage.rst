.. index::
    single: Usage
    single: Example

Usage
=====


.. configuration-block::

    .. code-block:: php

        <?php
        // Inject Sonata\FormatterBundle\Formatter\Pool in your class
        assert($formatterPool instanceof Pool::class);
        $html = $formatterPool->transform('markdown', $text);

    .. code-block:: jinja

        # Template usage
        {{ my_data | format_text('markdown') }}


.. note::

    By default, the ``Twig`` filter ``format_text`` is not marked as
    ``safe``. So, if you want to ouput the correct result, just add the
    ``| raw`` filter.

Form
----

The bundle provides a widget to format a text when the form is bound.
You have to declare 2 fields:

* ``source content`` field;
* ``formatter`` field.

And initialize a form type:

.. code-block:: php

    <?php
    use Sonata\FormatterBundle\Form\Type\FormatterType;

    $formBuilder
        ->add('rawContent') // source content
        ->add('contentFormatter', FormatterType::class, [
            'source_field' => 'rawContent',
            'target_field' => 'content',
        ])


When data is populated, the ``content`` property will be populated with
the text transformed from the selected transformer name and the
``rawContent`` property.

This can be used to pregenerate the content of a markdown blog post, for
instance.

Twig Formatter
--------------

``TwigFormatter`` uses the ``Twig\Environment`` of the project
(registered within service container as ``twig``).
All settings that affect the ``Twig\Environment`` of the project (like
the template loader in use, enabled extensions etc.) will also affect
the ``TwigFormatter``.

Also, ``TwigFormatter`` cannot have extensions enabled.

Security warning
................

Since in most cases ``TwigFormatter`` will be used as the formatter in
the administration interface (like the one shown above with the form),
be careful of allowing users to edit the templates, as this could
potentially affect the safety of the system. You have to take care of
the safety of your templates.
In exceptional cases, you can create a separate instance of
``Twig\Environment``, register it within the service container and
override ``TwigFormatter`` definition passing own secure Twig
Environment as a parameter instead of the project one.
