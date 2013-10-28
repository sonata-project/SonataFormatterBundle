Formatter Widget
================

One recurrent needs is to provide some kind of rich user interface to edit content. However
depends on the final target the content can have different format : markdown, raw content or html.

The ``sonata_formatter_type_selector`` widget has been implemented to allow end users to select
the correct format for his/her need. And depends of the format the textarea will change to match its
requirements.

By default the widget supports these types:

 - markdown with `Markdow MarkItUp! <http://markitup.jaysalvat.com/examples/markdown/>`_
 - rawhtml with `HTML MarkItUp! <http://markitup.jaysalvat.com/examples/html/>`_
 - text with an standard textarea widget
 - richhtml with `CKEditor <http://ckeditor.com/>`_

Preview
-------

.. figure:: ../images/formatter_with_ckeditor.png
   :align: center
   :alt: formatter with ckeditor

   The formatter with ckeditor

.. figure:: ../images/formatter_with_markitup.png
   :align: center
   :alt: formatter with MarkItUp!

   The formatter with MarkItUp!


How to use it ?
---------------

In order to make is work, let's take a real life example: "a post from a blog can
have different input formats". So the post model requires the following fields:

 - a ``contentFormatter`` field: store the selected formatter
 - a ``rawContent`` field: store the original content from the user
 - a ``content`` field: store the transformed content display to the visitor

Now, let's define a form to edit this post:

.. code-block:: php

    <?php

    $formBuilder
        ->add('content', 'sonata_formatter_type', array(
            'event_dispatcher' => $formBuilder->getEventDispatcher(),
            'format_field'   => 'contentFormatter',
            'source_field'   => 'rawContent',
            'source_field_options'      => array(
                'attr' => array('class' => 'span10', 'rows' => 20)
            ),
            'target_field'   => 'content',
            'listener'       => true,
        ))

The form type defines a ``contentFormatter`` with a select choice (``sonata_formatter_type_selector``)
the ``sonata_formatter_type_selector`` takes 2 options:

 - ``event_dispatcher``: the form dispatcher to attach the "submit" event
 - ``format_field``: the entity's format field
 - ``format_field_options``: the format field options (optional)
 - ``source_field``:  the entity's source field
 - ``source_field_options``: the source field options  (optional)
 - ``target_field``: the entity's final field with the transformed data

If you stop here, the most interesting part will not be present, let's edit some configuration files.

.. note::

    You can review the ``SonataNewsBundle`` which includes this code.


Dynamic Input
-------------

Open the config.yml file and add a following lines (or adjust the current configuration):

.. code-block:: yaml

    twig:
        debug:            %kernel.debug%
        strict_variables: %kernel.debug%

        form:
            resources:
                - 'SonataAdminBundle:Form:silex_form_div_layout.html.twig'
                - 'SonataFormatterBundle:Form:formatter.html.twig'


Make sure the ``SonataFormatterBundle:Form:formatter.html.twig`` is set. This template contains custom javascript
code to load the selected text editor.

You also need to include some assets in your template:

.. code-block:: html

    <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/skins/sonata/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/markdown/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/html/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/textile/style.css') }}" type="text/css" media="all" />

    <script src="{{ asset('bundles/ivoryckeditor/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/jquery.markitup.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/markdown/set.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/html/set.js') }}" type="text/javascript"></script>
    <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/textile/set.js') }}" type="text/javascript"></script>


.. note::

    Files provided in the ``SonataMarkItUpBundle`` are fine for standard usage, feel free to include
    your own configuration files. For more information about how to edit configuration please refer
    to their officials documentations.

Sonata Admin Integration
------------------------

Of course, it is possible to use this feature with the ``SonataAdminBundle``. In order to make it work
you need to create an extra bit of work

Create a new file named ``layout.html.twig`` inside the ``app/Resources/SonataAdminBundle/views/`` with the
following content:

.. code-block:: jinja

    {% extends 'SonataAdminBundle::standard_layout.html.twig' %}

    {% block stylesheets %}
        {{ parent() }}

        <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/skins/sonata/style.css') }}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/markdown/style.css') }}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/html/style.css') }}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/textile/style.css') }}" type="text/css" media="all" />
    {% endblock %}

    {% block javascripts %}
        {{ parent() }}

        <script src="{{ asset('bundles/ivoryckeditor/ckeditor.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/jquery.markitup.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/markdown/set.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/html/set.js') }}" type="text/javascript"></script>
        <script src="{{ asset('bundles/sonatamarkitup/markitup/markitup/sets/textile/set.js') }}" type="text/javascript"></script>
    {% endblock %}

Then update the ``sonata_admin`` configuration to use this template:

.. code-block:: yaml

    sonata_admin:
        templates:
            # default global templates
            layout:  SonataAdminBundle::layout.html.twig
