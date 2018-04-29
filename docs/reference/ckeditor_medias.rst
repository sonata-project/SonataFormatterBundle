.. index::
    single: Media; MediaBundle
    double: CKEditor; Configuration

Use CKEditor to select medias in SonataMediaBundle
==================================================

When using ``richhtml`` formatting option with ``CKEditor``, you can add a feature to select images directly
from ``SonataMediaBundle`` or even upload new medias from the editor in ``SonataMediaBundle`` to add them to your content.

It can be a quick way for editors to manage medias.

Configuration
-------------

First of all, you have to define your ``FOSCKEditorBundle`` (already embedded in ``SonataFormatterBundle``) configuration.
Be sure to have the ``fos/fos_ck_editor.yml`` configuration file available. It should contain something like this:

.. code-block:: yaml

    # app/config/fos/fos_ck_editor.yml

    fos_ck_editor:
        default_config: default
        configs:
            default:
                # default toolbar plus Format button
                toolbar:
                - [Bold, Italic, Underline, -, Cut, Copy, Paste, PasteText, PasteFromWord, -, Undo, Redo, -, NumberedList, BulletedList, -, Outdent, Indent, -, Blockquote, -, Image, Link, Unlink, Table]
                - [Format, Maximize, Source]

                filebrowserBrowseRoute: admin_sonata_media_media_ckeditor_browser
                filebrowserImageBrowseRoute: admin_sonata_media_media_ckeditor_browser
                # Display images by default when clicking the image dialog browse button
                filebrowserImageBrowseRouteParameters:
                    provider: sonata.media.provider.image
                filebrowserUploadRoute: admin_sonata_media_media_ckeditor_upload
                filebrowserUploadRouteParameters:
                    provider: sonata.media.provider.file
                # Upload file as image when sending a file from the image dialog
                filebrowserImageUploadRoute: admin_sonata_media_media_ckeditor_upload
                filebrowserImageUploadRouteParameters:
                    provider: sonata.media.provider.image
                    context: my-context # Optional, to upload in a custom context
                    format: my-big # Optional, media format or original size returned to editor

You can provide custom routes and a custom context to match your needs.

Second step, don't forget to import this ``fos/fos_ck_editor.yml`` file in your ``app/config.yml`` like this:

.. code-block:: yaml

  # app/config.yml

  # ...

  # FOSCKEditor
  - { resource: fos/fos_ck_editor.yml }

This third step is optional. You can do it if you need to define some custom browsing and uploading templates.
To do so, add these few lines in your ``sonata_formatter.yml`` file:

.. code-block:: yaml

  # app/config/sonata/sonata_formatter.yml

  sonata_formatter:

      # ...

      ckeditor:
          templates:
              browser: '@SonataFormatter/Ckeditor/browser.html.twig'
              upload: '@SonataFormatter/Ckeditor/upload.html.twig'

Last step takes place in your admin class. You just have to specify the ``ckeditor_context`` parameter to activate ``CKEditor``.

Here is an example to alter `shortDescription` field of the `ProductAdmin`:

.. code-block:: php

    <?php
    use Sonata\FormatterBundle\Form\Type\FormatterType;

    // ...

    $formMapper->add('shortDescription', FormatterType::class, array(
        'source_field'         => 'rawDescription',
        'source_field_options' => array('attr' => array('class' => 'span10', 'rows' => 20)),
        'format_field'         => 'descriptionFormatter',
        'target_field'         => 'description',
        'ckeditor_context'     => 'default',
        'event_dispatcher'     => $formMapper->getFormBuilder()->getEventDispatcher()
    ));

And that's it, enjoy browsing and uploading your medias using ``SonataMediaBundle``.

Custom image media format returned to CKEditor
----------------------------------------------

When you upload an image using CKEditor, the image URL returned by the server leads to the original size.
You can configure custom image format in ``SonataMediaBundle``:

.. code-block:: yaml

    sonata_media:
        contexts:
            default:
                formats:
                    big:   { width: 1280, quality: 95 }

Then you can pass this format to CKEditor:

.. code-block:: yaml

    fos_ck_editor:
        configs:
            default:
                filebrowserImageUploadRoute: admin_sonata_media_media_ckeditor_upload
                filebrowserImageUploadRouteParameters:
                    provider: sonata.media.provider.image
                    context: default
                    format: big

Alternatively you can specify custom return image format per field:

.. code-block:: php

    <?php
    use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

    // ...

    $formMapper->add('details', SimpleFormatterType::class, [
        'format' => 'richhtml',
        'ckeditor_context' => 'default',
        'ckeditor_image_format' => 'big',
    ]);
