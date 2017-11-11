.. index::
    double: Widget; Usage
    double: Widget; Configuration


One recurrent need is to provide some kind of rich user interface to edit content.
However, depending on the final target the content can have different formats: ``markdown``, ``textile``, ``bbcode``, ``rawhtml``, ``richhtml``.

Simple Formatter Wiget
======================

The ``sonata_simple_formatter_type_selector`` widget has been implemented to allow developers to force the input formatter value.


.. code-block:: php

    <?php

    $formMapper->add('comment', 'sonata_simple_formatter_type', array(
        'format' => 'markdown',
        'ckeditor_context' => 'default', // optional
    ));

Advanced Formatter Widget
=========================

The ``sonata_formatter_type_selector`` widget has been implemented to allow end users to select the correct format for his/her need.
And depending on the format, the textarea will change to match its requirements.

By default, the widget supports these types:

* `text` with a standard textarea widget;
* `markdown` with `Markdow MarkItUp! <http://markitup.jaysalvat.com/examples/markdown/>`_;
* `rawhtml` with `HTML MarkItUp! <http://markitup.jaysalvat.com/examples/html/>`_;
* `richhtml` with `CKEditor <http://ckeditor.com/>`_.

Preview
-------

.. figure:: ../images/formatter_with_ckeditor.png
   :align: center
   :alt: formatter with CKEditor

   The formatter with CKEditor

.. figure:: ../images/formatter_with_markitup.png
   :align: center
   :alt: formatter with MarkItUp!

   The formatter with MarkItUp!


How to use it ?
---------------

In order to make it work, let's take a real life example: "a post from a blog can have different input formats".
So, the post model requires the following fields:

* a ``contentFormatter`` field: store the selected formatter;
* a ``rawContent`` field: store the original content from the user;
* a ``content`` field: store the transformed content display to the visitor.

Now, let's define a form to edit this post:

.. code-block:: php

    <?php

    $formBuilder
        ->add('content', 'sonata_formatter_type', array(
            'event_dispatcher' => $formBuilder->getEventDispatcher(),
            'format_field'   => 'contentFormatter',
            'format_field_options' => array(
                'choices' => array('text', 'markdown'),
                'data' => 'markdown',
            ),
            'source_field'   => 'rawContent',
            'source_field_options'      => array(
                'attr' => array('class' => 'span10', 'rows' => 20)
            ),
            'listener'       => true,
            'target_field'   => 'content'
        ))

The form type defines a ``contentFormatter`` with a select choice (``sonata_formatter_type_selector``).
The available formatter choices are ``text`` and ``markdown`` here, with the ``markdown`` formatter preselected.
The ``sonata_formatter_type_selector`` takes various options:

* ``listener`` (optional, default is ``true``);
* ``event_dispatcher``: the form dispatcher to attach the "submit" event  (optional, depends on the ``listener`` value);
* ``format_field``: the entity's format field;
* ``format_field_options``: the format field options (optional);
* ``source_field``: the entity's source field;
* ``source_field_options``: the source field options (optional);
* ``target_field``: the entity's final field with the transformed data.

Additionally, the following options can be added to give `CKEditor` a context in order to select images directly from ``SonataMediaBundle``, and to define a custom `CKEditor` toolbar configuration:

* ``ckeditor_context``: give CKEditor a context in order to customize routes used to browse and upload medias (see "Use CKEditor to select medias in SonataMediaBundle" chapter)
* ``ckeditor_toolbar_icons`` : give CKEditor a custom toolbar configuration (optional)

Here is the default `CKEditor` custom toolbar configuration, you can tweak:

.. code-block:: php

    <?php

    $ckeditor_toolbar_icons = array(
        1 => array('Bold', 'Italic', 'Underline',
            '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
            '-', 'Undo', 'Redo',
            '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
            '-', 'Blockquote',
            '-', 'Image', 'Link', 'Unlink', 'Table'),
        2 => array('Maximize', 'Source')
    );

If you stop here, the most interesting part will not be present. Let's edit some configuration files.

.. note::

    If you want to see an actual implementation of this can, you can have a look at the ``SonataNewsBundle``.


Dynamic Input
-------------

Open the ``config.yml`` file and add the following lines (or adjust the current configuration):

.. code-block:: yaml

    twig:
        debug:            "%kernel.debug%"
        strict_variables: "%kernel.debug%"

        form:
            resources:
                # ...
                - 'SonataFormatterBundle:Form:formatter.html.twig'


Make sure the ``SonataFormatterBundle:Form:formatter.html.twig`` is set.
This template contains custom javascript code to load the selected text editor.

You also need to include some assets in your template:

.. code-block:: html

    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/skins/sonata/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/markdown/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/html/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/textile/style.css') }}" type="text/css" media="all" />

    <script src="{{ asset('bundles/ivoryckeditor/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/vendor/markitup-markitup/markitup/jquery.markitup.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/markitup/sets/markdown/set.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/markitup/sets/html/set.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonataformatter/markitup/sets/textile/set.js') }}" type="text/javascript"></script>

.. note::

    Files provided in the ``SonataMarkItUpBundle`` are fine for standard usage, feel free to include your own configuration files.
    For more information about how to edit configuration please refer to their officials documentations.

Sonata Admin Integration
------------------------

Of course, it is possible to use this feature with the ``SonataAdminBundle``. In order to make it work, you need to do an extra bit of work.

Create a new file named ``layout.html.twig`` inside the ``app/Resources/SonataAdminBundle/views/`` with the following content:

.. code-block:: jinja

    {% extends 'SonataAdminBundle::standard_layout.html.twig' %}

    {% block stylesheets %}
        {{ parent() }}

        <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/skins/sonata/style.css') }}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/markdown/style.css') }}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/html/style.css') }}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('bundles/sonataformatter/markitup/sets/textile/style.css') }}" type="text/css" media="all" />
    {% endblock %}

    {% block javascripts %}
        {{ parent() }}

        <script src="{{ asset('bundles/ivoryckeditor/ckeditor.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonataformatter/vendor/markitup-markitup/markitup/jquery.markitup.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonataformatter/markitup/sets/markdown/set.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonataformatter/markitup/sets/html/set.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonataformatter/markitup/sets/textile/set.js') }}" type="text/javascript"></script>
    {% endblock %}

Then, update the ``sonata_admin`` configuration to use this template:

.. code-block:: yaml

    sonata_admin:
        templates:
            # default global templates
            layout:  SonataAdminBundle::layout.html.twig
